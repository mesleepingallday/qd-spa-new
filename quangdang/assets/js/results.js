/**
 * Home results carousel: arrow buttons, keyboard, start on the 2nd slide, and the zoom dialog.
 * The curve itself is CSS (scroll-driven animation); without JS the track still swipes and
 * each poster link opens the full image.
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-result-arc]');
	if (!root) return;

	var track = root.querySelector('[data-result-track]');
	var slides = track.children;
	var prev = root.querySelector('[data-result-prev]');
	var next = root.querySelector('[data-result-next]');
	var calm = window.matchMedia('(prefers-reduced-motion: reduce)');

	function behavior() {
		return calm.matches ? 'auto' : 'smooth';
	}

	// Index of the slide nearest the centre of the track.
	function current() {
		var mid = track.scrollLeft + track.clientWidth / 2;
		var best = 0;
		var dist = Infinity;
		for (var i = 0; i < slides.length; i++) {
			var s = slides[i];
			var d = Math.abs(s.offsetLeft + s.offsetWidth / 2 - mid);
			if (d < dist) {
				dist = d;
				best = i;
			}
		}
		return best;
	}

	function go(i, how) {
		i = Math.max(0, Math.min(slides.length - 1, i));
		var s = slides[i];
		track.scrollTo({ left: s.offsetLeft + s.offsetWidth / 2 - track.clientWidth / 2, behavior: how || behavior() });
	}

	function update() {
		if (!prev) return;
		var max = track.scrollWidth - track.clientWidth - 2;
		prev.disabled = track.scrollLeft <= 2;
		next.disabled = track.scrollLeft >= max;
	}

	if (slides.length >= 3) go(1, 'auto');
	update();

	var ticking = false;
	track.addEventListener('scroll', function () {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(function () {
			ticking = false;
			update();
		});
	}, { passive: true });
	window.addEventListener('resize', update, { passive: true });

	if (prev) {
		prev.addEventListener('click', function () { go(current() - 1); });
		next.addEventListener('click', function () { go(current() + 1); });
	}

	track.addEventListener('keydown', function (e) {
		if (e.target !== track) return;
		if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
			e.preventDefault();
			go(current() + (e.key === 'ArrowRight' ? 1 : -1));
		}
	});

	/* Zoom dialog ----------------------------------------------------------- */
	var dialog = root.querySelector('[data-result-dialog]');
	if (!dialog || typeof dialog.showModal !== 'function') return;

	var img = dialog.querySelector('[data-result-dialog-img]');
	var title = dialog.querySelector('[data-result-dialog-title]');
	var points = dialog.querySelector('[data-result-dialog-points]');
	var opener = null;

	track.addEventListener('click', function (e) {
		var link = e.target.closest('[data-result-zoom]');
		if (!link) return;
		e.preventDefault();

		// A side slide is brought to the centre first; the centred one opens.
		var li = link.closest('.result-slide');
		var i = Array.prototype.indexOf.call(slides, li);
		if (i !== current()) {
			go(i);
			return;
		}

		var source = link.querySelector('img');
		opener = link;
		img.src = link.href;
		img.alt = source ? source.alt : '';
		title.textContent = link.getAttribute('data-title') || '';
		points.textContent = '';
		var list = li.querySelector('[data-result-points]');
		if (list) {
			Array.prototype.forEach.call(list.children, function (item) {
				var p = document.createElement('li');
				p.textContent = item.textContent;
				points.appendChild(p);
			});
		}
		dialog.showModal();
	});

	dialog.addEventListener('click', function (e) {
		if (e.target === dialog || e.target.closest('[data-result-close]')) dialog.close();
	});

	dialog.addEventListener('close', function () {
		img.src = 'data:,';
		if (opener) opener.focus();
	});
})();
