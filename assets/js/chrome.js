/**
 * Site chrome behavior: mobile nav toggle for the floating header.
 * Adapted from Rentl's main.js — data-nav-open on the header drives the
 * panel, with click-outside and Escape both closing it.
 */
(function () {
	'use strict';

	var header = document.querySelector('.site-header');
	var hamburger = document.querySelector('.site-header__hamburger');
	if (!header || !hamburger) {
		return;
	}

	function setOpen(open) {
		header.setAttribute('data-nav-open', open ? 'true' : 'false');
		hamburger.setAttribute('aria-expanded', open ? 'true' : 'false');
		hamburger.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
	}

	hamburger.addEventListener('click', function () {
		setOpen(header.getAttribute('data-nav-open') !== 'true');
	});

	document.addEventListener('click', function (e) {
		if (!header.contains(e.target) && header.getAttribute('data-nav-open') === 'true') {
			setOpen(false);
		}
	});

	document.addEventListener('keydown', function (e) {
		if ('Escape' === e.key && header.getAttribute('data-nav-open') === 'true') {
			setOpen(false);
			hamburger.focus();
		}
	});
})();
