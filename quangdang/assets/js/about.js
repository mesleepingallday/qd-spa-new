/* About subpages: bring the current tab into view in the scrollable sub-navigation. */
(function () {
	var nav = document.querySelector('[data-about-subnav]');
	if (!nav) return;
	var list = nav.querySelector('.about-subnav__list');
	var current = nav.querySelector('[aria-current="page"]');
	if (!list || !current) return;
	var item = current.parentElement;
	list.scrollLeft = Math.max(0, item.offsetLeft - (list.clientWidth - item.offsetWidth) / 2);
})();
