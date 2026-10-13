/* Vanilla lightbox: <button data-lightbox="group" data-src data-caption> or <a href> triggers, native <dialog>. */
(function () {
	var groups = {}, d, img, cap, cnt, cur = [], i = 0, opener, x0;
	function build() {
		d = document.createElement('dialog');
		d.className = 'lb';
		d.setAttribute('aria-label', 'Thư viện ảnh');
		d.innerHTML = '<figure class="lb__fig"><img class="lb__img" alt=""><figcaption class="lb__cap"></figcaption></figure>' +
			'<p class="lb__count" aria-live="polite"></p>' +
			'<button type="button" class="lb__btn lb__close" aria-label="Đóng">&times;</button>' +
			'<button type="button" class="lb__btn lb__prev" aria-label="Ảnh trước">&#8249;</button>' +
			'<button type="button" class="lb__btn lb__next" aria-label="Ảnh sau">&#8250;</button>';
		document.body.appendChild(d);
		img = d.querySelector('.lb__img'); cap = d.querySelector('.lb__cap'); cnt = d.querySelector('.lb__count');
		d.querySelector('.lb__close').onclick = function () { d.close(); };
		d.querySelector('.lb__prev').onclick = function () { go(-1); };
		d.querySelector('.lb__next').onclick = function () { go(1); };
		d.addEventListener('click', function (e) { if (e.target === d) d.close(); });
		d.addEventListener('close', function () { if (opener) opener.focus(); });
		d.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowLeft') go(-1); else if (e.key === 'ArrowRight') go(1);
		});
		d.addEventListener('pointerdown', function (e) { x0 = e.clientX; });
		d.addEventListener('pointerup', function (e) {
			if (x0 == null) return;
			var dx = e.clientX - x0; x0 = null;
			if (Math.abs(dx) > 40) go(dx < 0 ? 1 : -1);
		});
	}
	function show() {
		var t = cur[i];
		img.src = t.src; img.alt = t.caption; cap.textContent = t.caption;
		cnt.textContent = (i + 1) + ' / ' + cur.length;
	}
	function go(n) { i = (i + n + cur.length) % cur.length; show(); }
	function open(el) {
		if (!d) build();
		var g = el.getAttribute('data-lightbox');
		cur = groups[g]; i = cur.findIndex(function (t) { return t.el === el; });
		opener = el; show(); d.showModal();
	}
	document.querySelectorAll('[data-lightbox]').forEach(function (el) {
		var g = el.getAttribute('data-lightbox');
		var a = el.tagName === 'A' ? el : el.querySelector('a');
		var src = el.dataset.src || (a && a.getAttribute('href'));
		(groups[g] = groups[g] || []).push({ el: el, src: src, caption: el.dataset.caption || '' });
		el.addEventListener('click', function (e) { e.preventDefault(); open(el); });
	});
})();
