/**
 * Site chrome behavior: dark-mode toggle + mobile nav toggle for the
 * floating header. Both adapted from Rentl's main.js.
 */

/* Dark mode toggle — the head inline script already set data-theme before
   paint; this handles the button state and click persistence. */
(function () {
	'use strict';

	var STORAGE_KEY = 'jt-theme';
	var html = document.documentElement;
	var toggles = document.querySelectorAll('.jt-theme-toggle');
	if (!toggles.length) {
		return;
	}

	function applyTheme(theme) {
		html.setAttribute('data-theme', theme);
		toggles.forEach(function (btn) {
			btn.setAttribute('data-current-theme', theme);
			btn.setAttribute('aria-label', 'dark' === theme ? 'Switch to light mode' : 'Switch to dark mode');
		});
	}

	applyTheme(html.getAttribute('data-theme') || 'light');

	toggles.forEach(function (btn) {
		btn.addEventListener('click', function () {
			var next = 'dark' === html.getAttribute('data-theme') ? 'light' : 'dark';
			try {
				localStorage.setItem(STORAGE_KEY, next);
			} catch (err) {
				/* private mode — theme still applies for this page view */
			}
			applyTheme(next);
		});
	});
})();

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
