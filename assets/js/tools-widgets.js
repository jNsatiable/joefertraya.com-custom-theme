/**
 * Home page "tools" flip board: a single flap that rotates through a list
 * of tool names. The flip is two 90° phases rather than one 180° pass —
 * text is swapped while the card is edge-on (invisible) between phase 1
 * (0 -> -90deg) and phase 2 (90deg -> 0), so no double-sided card markup
 * or backface-visibility trickery is needed, just a transform and a timed
 * content swap. (The marquee needs no JS — it's a plain CSS animation with
 * its duration set inline per admin-configured speed.)
 */
(function () {
	'use strict';
	var widget = document.querySelector('.jt-flap-widget');
	if (!widget) { return; }

	var items = [];
	try {
		items = JSON.parse(widget.getAttribute('data-items') || '[]');
	} catch (e) {
		items = [];
	}
	if (items.length < 2) { return; }

	var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	if (reducedMotion) { return; }

	var flap = widget.querySelector('.jt-flap');
	var textEl = widget.querySelector('.jt-flap__text');
	var speed = parseFloat(widget.getAttribute('data-speed'));
	if (!speed || speed <= 0) { speed = 1; }
	var HOLD_MS = 1600;

	var index = 0;

	function flipToNext() {
		var halfMs = ( speed * 1000 ) / 2;
		flap.style.transition = 'transform ' + ( speed / 2 ) + 's cubic-bezier(0.5, 0, 0.75, 0)';
		flap.style.transform = 'rotateX(-90deg)';
		setTimeout(function () {
			index = (index + 1) % items.length;
			textEl.textContent = items[index];
			flap.style.transition = 'none';
			flap.style.transform = 'rotateX(90deg)';
			void flap.offsetHeight; /* force reflow so the next transition isn't merged with this reset */
			flap.style.transition = 'transform ' + ( speed / 2 ) + 's cubic-bezier(0.25, 1, 0.5, 1)';
			flap.style.transform = 'rotateX(0deg)';
		}, halfMs);
		setTimeout(flipToNext, speed * 1000 + HOLD_MS);
	}

	setTimeout(flipToNext, HOLD_MS);
})();
