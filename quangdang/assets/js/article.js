/**
 * Article page: copy-link button and the phone table of contents.
 * Vanilla JS, no dependencies.
 */
(function () {
	'use strict';

	/* Copy link with "Đã sao chép" feedback (button is hidden until JS runs) -- */
	Array.prototype.forEach.call(document.querySelectorAll('[data-copy-link]'), function (btn) {
		btn.hidden = false;
		var label = btn.querySelector('[data-copy-label]');
		var status = document.querySelector('[data-copy-status]');
		var original = label ? label.textContent : '';
		var timer = null;

		var done = function (ok) {
			var text = ok ? 'Đã sao chép' : 'Không sao chép được';
			if (label) label.textContent = text;
			if (status) status.textContent = text;
			btn.classList.toggle('is-done', ok);
			clearTimeout(timer);
			timer = setTimeout(function () {
				if (label) label.textContent = original;
				btn.classList.remove('is-done');
			}, 2200);
		};

		var fallback = function (url) {
			var field = document.createElement('textarea');
			field.value = url;
			field.setAttribute('readonly', '');
			field.style.cssText = 'position:fixed;top:0;left:0;opacity:0';
			document.body.appendChild(field);
			field.select();
			var ok = false;
			try { ok = document.execCommand('copy'); } catch (err) { ok = false; }
			document.body.removeChild(field);
			done(ok);
		};

		btn.addEventListener('click', function () {
			var url = btn.getAttribute('data-copy-link') || window.location.href;
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(url).then(function () { done(true); }, function () { fallback(url); });
			} else {
				fallback(url);
			}
		});
	});

	/* Phone table of contents: close the box after choosing a section --------- */
	var box = document.querySelector('.toc-box');
	if (box) {
		box.addEventListener('click', function (e) {
			if (e.target.closest('a[href^="#"]')) box.removeAttribute('open');
		});
	}
})();
