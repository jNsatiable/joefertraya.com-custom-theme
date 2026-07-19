/**
 * Home hero tools marquee: hover tooltip positioned at wherever the
 * mouse entered the strip (captured once, on pointerenter) rather than
 * tracking mousemove — it stays put for the rest of the hover. Works
 * identically whether the visitor enters via the pinned "J's Toolkit"
 * label or anywhere along the scrolling ticker, since the whole row is
 * one link (see jt_render_tools_marquee() in tools-widgets.php).
 *
 * Gated behind hover-capable devices — on touch, a lingering tooltip
 * positioned from a single tap isn't useful and could flash oddly right
 * before navigation.
 */
(function () {
	'use strict';
	if (!window.matchMedia || !window.matchMedia('(hover: hover)').matches) {
		return;
	}

	var row = document.querySelector('.jt-tools-row');
	if (!row) {
		return;
	}
	var tip = row.querySelector('.jt-tools-hover-tip');
	if (!tip) {
		return;
	}

	row.addEventListener('pointerenter', function (e) {
		var rect = row.getBoundingClientRect();
		if (rect.width === 0) {
			return;
		}
		var margin = 60;
		var x = e.clientX - rect.left;
		x = Math.max(margin, Math.min(rect.width - margin, x));
		tip.style.setProperty('--tip-x', x + 'px');
		tip.classList.add('is-visible');
	});

	row.addEventListener('pointerleave', function () {
		tip.classList.remove('is-visible');
	});
})();
