/**
 * Quang Đăng Clinic — site behaviour (no dependencies).
 * Header state · desktop mega menu · mobile drawer · search dialog · announcement · scroll-spy.
 */
(function () {
	'use strict';

	var mqDesktop = window.matchMedia('(min-width: 1024px)');

	/* Header: hairline once the page scrolls -------------------------------- */
	var header = document.querySelector('[data-header]');
	if (header) {
		var onScroll = function () {
			header.classList.toggle('is-scrolled', window.scrollY > 8);
		};
		window.addEventListener('scroll', onScroll, { passive: true });
		onScroll();
	}

	/* Desktop nav: hover intent + click + keyboard (disclosure pattern) ----- */
	var navItems = Array.prototype.slice.call(document.querySelectorAll('[data-nav-item]'));
	var openItem = null;
	var hoverTimer = null;

	function setOpen(item, open) {
		var trigger = item.querySelector('[data-nav-trigger]');
		var panel = item.querySelector('[data-nav-panel]');
		if (!trigger || !panel) return;
		trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
		panel.classList.toggle('is-open', open);
		if (open) {
			if (openItem && openItem !== item) setOpen(openItem, false);
			openItem = item;
		} else if (openItem === item) {
			openItem = null;
		}
	}

	navItems.forEach(function (item) {
		var trigger = item.querySelector('[data-nav-trigger]');
		if (!trigger) return;

		trigger.addEventListener('click', function () {
			setOpen(item, trigger.getAttribute('aria-expanded') !== 'true');
		});

		item.addEventListener('mouseenter', function () {
			if (!mqDesktop.matches) return;
			clearTimeout(hoverTimer);
			// Open at once if another panel is already open (moving along the bar), else wait a beat.
			hoverTimer = setTimeout(function () { setOpen(item, true); }, openItem ? 0 : 140);
		});

		item.addEventListener('mouseleave', function () {
			clearTimeout(hoverTimer);
			hoverTimer = setTimeout(function () { setOpen(item, false); }, 220);
		});

		item.addEventListener('focusout', function (e) {
			if (!item.contains(e.relatedTarget)) setOpen(item, false);
		});
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && openItem) {
			var t = openItem.querySelector('[data-nav-trigger]');
			setOpen(openItem, false);
			if (t) t.focus();
		}
	});

	document.addEventListener('click', function (e) {
		if (openItem && !openItem.contains(e.target)) setOpen(openItem, false);
	});

	/* Mobile drawer with drill-down panels ---------------------------------- */
	var drawer = document.querySelector('[data-drawer]');
	if (drawer) {
		var opener = document.querySelector('[data-drawer-open]');
		var panels = drawer.querySelectorAll('[data-drawer-panel]');
		var lastFocus = null;

		var showPanel = function (id, back) {
			var next = drawer.querySelector('#' + id);
			if (!next) return;
			Array.prototype.forEach.call(panels, function (p) {
				var active = p === next;
				p.classList.toggle('is-active', active);
				// The panel we leave slides left when going deeper.
				p.classList.toggle('is-behind', !active && !back && p.classList.contains('was-active'));
				p.classList.toggle('was-active', active);
			});
			var focusable = next.querySelector('button, a');
			if (focusable) focusable.focus({ preventScroll: true });
		};

		var openDrawer = function () {
			lastFocus = document.activeElement;
			drawer.classList.add('is-open');
			drawer.setAttribute('aria-hidden', 'false');
			if (opener) opener.setAttribute('aria-expanded', 'true');
			document.documentElement.style.overflow = 'hidden';
			var close = drawer.querySelector('.drawer__head [data-drawer-close]');
			if (close) close.focus();
		};

		var closeDrawer = function () {
			drawer.classList.remove('is-open');
			drawer.setAttribute('aria-hidden', 'true');
			if (opener) opener.setAttribute('aria-expanded', 'false');
			document.documentElement.style.overflow = '';
			if (lastFocus) lastFocus.focus();
		};

		if (opener) opener.addEventListener('click', openDrawer);
		drawer.addEventListener('click', function (e) {
			var go = e.target.closest('[data-drawer-go]');
			var back = e.target.closest('[data-drawer-back]');
			if (e.target.closest('[data-drawer-close]')) closeDrawer();
			else if (go) showPanel(go.getAttribute('data-drawer-go'), false);
			else if (back) showPanel(back.closest('[data-drawer-panel]').getAttribute('data-parent'), true);
		});
		drawer.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') closeDrawer();
			if (e.key !== 'Tab') return;
			// Keep focus inside the sheet (hidden panels are skipped).
			var nodes = Array.prototype.filter.call(
				drawer.querySelectorAll('.drawer__sheet a[href], .drawer__sheet button, .drawer__sheet input'),
				function (el) { return el.getClientRects().length && getComputedStyle(el).visibility !== 'hidden'; }
			);
			var first = nodes[0];
			var last = nodes[nodes.length - 1];
			if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
			else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
		});
		mqDesktop.addEventListener('change', function (e) { if (e.matches) closeDrawer(); });
		var root = drawer.querySelector('#drawer-root');
		if (root) root.classList.add('was-active');
	}

	/* Search dialog --------------------------------------------------------- */
	var dialog = document.querySelector('[data-search-dialog]');
	if (dialog && typeof dialog.showModal === 'function') {
		document.addEventListener('click', function (e) {
			if (e.target.closest('[data-search-open]')) {
				dialog.showModal();
				var input = dialog.querySelector('input');
				if (input) input.focus();
			}
			if (e.target.closest('[data-search-close]') || e.target === dialog) dialog.close();
		});
	}

	/* Announcement dismiss (remembered per message) -------------------------- */
	var announce = document.querySelector('[data-announce]');
	if (announce) {
		var btn = announce.querySelector('[data-announce-close]');
		if (btn) btn.addEventListener('click', function () {
			announce.hidden = true;
			try { localStorage.setItem('qd-announce', announce.getAttribute('data-announce')); } catch (err) { /* private mode */ }
		});
	}

	/* Scroll-spy for in-page tab bars: <nav data-scrollspy> with #hash links -- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-scrollspy]'), function (nav) {
		if (!('IntersectionObserver' in window)) return;
		var links = nav.querySelectorAll('a[href^="#"]');
		var map = {};
		Array.prototype.forEach.call(links, function (a) {
			var target = document.getElementById(a.getAttribute('href').slice(1));
			if (target) map[target.id] = a;
		});
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				Array.prototype.forEach.call(links, function (a) { a.removeAttribute('aria-current'); });
				var link = map[entry.target.id];
				if (link) {
					link.setAttribute('aria-current', 'true');
					link.scrollIntoView({ block: 'nearest', inline: 'center' });
				}
			});
		}, { rootMargin: '-40% 0px -55% 0px' });
		Object.keys(map).forEach(function (id) { io.observe(document.getElementById(id)); });
	});
})();
