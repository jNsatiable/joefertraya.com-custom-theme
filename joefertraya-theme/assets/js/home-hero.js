/**
 * Home hero: Canvas 2D dot-globe background + skill typewriter.
 *
 * Replaces the Elementor site's VANTA.GLOBE (Three.js, ~600KB across three
 * CDNs) and Essential Addons fancy-text widget. Same visual, a few KB,
 * zero dependencies — Fibonacci sphere, nearest-neighbor edges, perspective
 * projection via focal / (focal + z).
 */
(function () {
	'use strict';

	var hero = document.querySelector('.home-hero');
	if (!hero) {
		return;
	}

	var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---- Globe ---- */

	var canvas = hero.querySelector('.home-hero__canvas');
	var ctx = canvas.getContext('2d');
	var ACCENT = 'rgba(251, 146, 60, ';  /* --color-accent */
	var WHITE = 'rgba(255, 255, 255, ';
	var POINTS = 220;
	var FOCAL = 3.2;

	var points = [];
	var golden = Math.PI * (3 - Math.sqrt(5));
	for (var i = 0; i < POINTS; i++) {
		var y = 1 - (i / (POINTS - 1)) * 2;
		var r = Math.sqrt(1 - y * y);
		points.push([Math.cos(golden * i) * r, y, Math.sin(golden * i) * r]);
	}

	/* Connect each point to its two nearest neighbors, deduplicated. */
	var edges = [];
	for (var a = 0; a < POINTS; a++) {
		var nearest = [];
		for (var b = 0; b < POINTS; b++) {
			if (a === b) {
				continue;
			}
			var dx = points[a][0] - points[b][0];
			var dy = points[a][1] - points[b][1];
			var dz = points[a][2] - points[b][2];
			nearest.push([dx * dx + dy * dy + dz * dz, b]);
		}
		nearest.sort(function (p, q) { return p[0] - q[0]; });
		for (var k = 0; k < 2; k++) {
			if (a < nearest[k][1]) {
				edges.push([a, nearest[k][1]]);
			}
		}
	}

	var rotation = 0;

	/*
	 * Sized via ResizeObserver, not a one-shot read: a deferred script can
	 * execute before layout settles, leaving the backing store at a stale
	 * size (observed live as a 20px-wide canvas under a 1280px CSS box).
	 */
	function resize() {
		var dpr = Math.min(window.devicePixelRatio || 1, 2);
		var w = Math.round(canvas.offsetWidth * dpr);
		var h = Math.round(canvas.offsetHeight * dpr);
		if (w > 0 && h > 0 && (canvas.width !== w || canvas.height !== h)) {
			canvas.width = w;
			canvas.height = h;
			return true;
		}
		return false;
	}

	function drawFrame() {
		var w = canvas.width;
		var h = canvas.height;
		ctx.clearRect(0, 0, w, h);

		var radius = h * 0.42;
		var cx = w * 0.72;
		var cy = h * 0.52;
		var cos = Math.cos(rotation);
		var sin = Math.sin(rotation);

		var projected = [];
		for (var i = 0; i < POINTS; i++) {
			var p = points[i];
			var x = p[0] * cos - p[2] * sin;
			var z = p[0] * sin + p[2] * cos;
			var scale = FOCAL / (FOCAL + z);
			projected.push([cx + x * radius * scale, cy + p[1] * radius * scale, scale, z]);
		}

		ctx.lineWidth = 1;
		for (var e = 0; e < edges.length; e++) {
			var pa = projected[edges[e][0]];
			var pb = projected[edges[e][1]];
			var lineAlpha = Math.max(0.06, 0.38 * (pa[2] + pb[2] - 1.4));
			ctx.strokeStyle = ACCENT + lineAlpha.toFixed(3) + ')';
			ctx.beginPath();
			ctx.moveTo(pa[0], pa[1]);
			ctx.lineTo(pb[0], pb[1]);
			ctx.stroke();
		}

		for (var j = 0; j < projected.length; j++) {
			var pt = projected[j];
			var alpha = Math.max(0.15, (pt[3] + 1) / 2);
			ctx.fillStyle = pt[3] > 0.3
				? WHITE + (alpha * 0.9).toFixed(2) + ')'
				: ACCENT + alpha.toFixed(2) + ')';
			ctx.beginPath();
			ctx.arc(pt[0], pt[1], 2.4 * pt[2], 0, Math.PI * 2);
			ctx.fill();
		}
	}

	function animate() {
		rotation += 0.0035;
		drawFrame();
		requestAnimationFrame(animate);
	}

	resize();
	if (typeof ResizeObserver !== 'undefined') {
		new ResizeObserver(function () {
			if (resize() && reducedMotion) {
				drawFrame();
			}
		}).observe(canvas);
	} else {
		window.addEventListener('resize', function () {
			if (resize() && reducedMotion) {
				drawFrame();
			}
		});
	}

	if (reducedMotion) {
		drawFrame();
	} else {
		animate();
	}

	/* ---- Typewriter ---- */

	var skills;
	try {
		skills = JSON.parse(hero.getAttribute('data-skills'));
	} catch (err) {
		skills = [];
	}
	var textEl = hero.querySelector('.home-hero__rotator-text');
	if (!textEl || !skills.length) {
		return;
	}

	if (reducedMotion) {
		textEl.textContent = skills[0];
		return;
	}

	var skillIndex = 0;
	var charIndex = 0;
	var deleting = false;

	function tick() {
		var current = skills[skillIndex];
		if (!deleting) {
			charIndex++;
			textEl.textContent = current.slice(0, charIndex);
			if (charIndex === current.length) {
				deleting = true;
				setTimeout(tick, 1400);
				return;
			}
			setTimeout(tick, 55);
		} else {
			charIndex--;
			textEl.textContent = current.slice(0, charIndex);
			if (charIndex === 0) {
				deleting = false;
				skillIndex = (skillIndex + 1) % skills.length;
			}
			setTimeout(tick, 28);
		}
	}
	tick();
})();
