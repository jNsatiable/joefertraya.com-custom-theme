/**
 * Home hero: Canvas 2D layered dot-network background + skill typewriter.
 *
 * Replaced the earlier hand-rolled VANTA.GLOBE-style dot-sphere (2026-07-18)
 * with a flatter, mouse-interactive "Net" effect: three depth layers
 * (back/mid/front — smaller and fainter behind, larger and fuller in
 * front), edges only ever connecting within their own layer so a line
 * never spans two depths and flattens the illusion. Only the front layer
 * reacts to the mouse — points within a radius get pushed away, and
 * nearby front-layer points also draw a live line to the cursor, joining
 * it as a node in the network. A click adds a stronger, one-shot outward
 * burst on top of that (front layer only, same as hover) that decays over
 * ~700ms rather than lingering. Same zero-dependency Canvas 2D approach as
 * before, no library weight added.
 */
(function () {
	'use strict';

	var hero = document.querySelector('.home-hero');
	if (!hero) {
		return;
	}

	var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	/* ---- Layered network ---- */

	var canvas = hero.querySelector('.home-hero__canvas');
	var ctx = canvas.getContext('2d');
	var ACCENT = 'rgba(251, 146, 60, ';  /* --color-accent */
	var WHITE = 'rgba(255, 255, 255, ';

	var DEPTH_LAYERS = [
		{ count: 30, radius: 1.4, alphaWhite: 0.30, alphaAccent: 0.45, connectDist: 0.20, interactive: false },
		{ count: 25, radius: 2.2, alphaWhite: 0.55, alphaAccent: 0.70, connectDist: 0.24, interactive: false },
		{ count: 15, radius: 3.2, alphaWhite: 0.80, alphaAccent: 0.90, connectDist: 0.28, interactive: true }
	];
	var points = [];
	DEPTH_LAYERS.forEach(function (cfg, layerIndex) {
		for (var i = 0; i < cfg.count; i++) {
			points.push({
				x: Math.random() * 2 - 1,
				y: Math.random() * 2 - 1,
				vx: (Math.random() - 0.5) * 0.0009,
				vy: (Math.random() - 0.5) * 0.0009,
				accent: Math.random() < 0.15,
				layer: layerIndex
			});
		}
	});
	var POINTS = points.length;

	/* Mouse tracking — canvas-pixel space (already DPR-scaled), reset to
	   inactive on mouseleave so the effect fades cleanly rather than
	   sticking at the last known position. Gated to the canvas's own
	   bounds, which fill the hero section exactly (inset: 0), so this is
	   already "only within the section" with no extra check needed. */
	var mouse = { x: 0, y: 0, active: false };
	if (!reducedMotion) {
		window.addEventListener('mousemove', function (e) {
			var rect = canvas.getBoundingClientRect();
			if (rect.width === 0 || rect.height === 0) {
				return;
			}
			mouse.x = (e.clientX - rect.left) * (canvas.width / rect.width);
			mouse.y = (e.clientY - rect.top) * (canvas.height / rect.height);
			mouse.active = e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom;
		});
		window.addEventListener('mouseleave', function () { mouse.active = false; });
	}

	/* Click burst — a one-shot, decaying outward push, layered on top of
	   the continuous hover repulsion. Stateless like the hover push: each
	   frame recomputes the burst purely from how long ago the click was
	   and how far the point currently is, so nothing needs to be manually
	   reset or unwound once it fades. Front layer only, same as hover. */
	var click = { x: 0, y: 0, time: -Infinity };
	if (!reducedMotion) {
		window.addEventListener('click', function (e) {
			var rect = canvas.getBoundingClientRect();
			if (rect.width === 0 || rect.height === 0) { return; }
			if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) { return; }
			click.x = (e.clientX - rect.left) * (canvas.width / rect.width);
			click.y = (e.clientY - rect.top) * (canvas.height / rect.height);
			click.time = performance.now();
		});
	}

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

		var cx = w * 0.72;
		var cy = h * 0.52;
		var fieldRadius = Math.min(w, h) * 0.44;
		var REPEL_RADIUS = Math.min(w, h) * 0.16;
		var MAX_PUSH = Math.min(w, h) * 0.045;
		var MOUSE_CONNECT_RADIUS = Math.min(w, h) * 0.24;
		var CLICK_RADIUS = Math.min(w, h) * 0.38;
		var CLICK_MAX_PUSH = Math.min(w, h) * 0.16;
		var CLICK_DURATION = 700;

		for (var i = 0; i < POINTS; i++) {
			var p = points[i];
			p.x += p.vx;
			p.y += p.vy;
			if (p.x > 1 || p.x < -1) { p.vx *= -1; }
			if (p.y > 1 || p.y < -1) { p.vy *= -1; }
		}

		var proj = points.map(function (p) { return [cx + p.x * fieldRadius, cy + p.y * fieldRadius]; });

		var clickElapsed = performance.now() - click.time;
		var clickTimeFactor = clickElapsed < CLICK_DURATION ? Math.pow(1 - clickElapsed / CLICK_DURATION, 2) : 0;

		/* Displayed positions = natural drift position + a temporary push
		   away from the cursor and/or the most recent click, recomputed
		   fresh every frame — nothing about the underlying drift state is
		   mutated, so a point relaxes back to its natural path the instant
		   the cursor moves away or the click burst fades out. Only the
		   front, interactive layer is ever displaced. */
		var displayed = proj.map(function (pt, idx) {
			if (!DEPTH_LAYERS[points[idx].layer].interactive) {
				return pt;
			}
			var ox = 0;
			var oy = 0;
			if (mouse.active) {
				var dx = pt[0] - mouse.x;
				var dy = pt[1] - mouse.y;
				var dist = Math.sqrt(dx * dx + dy * dy);
				if (dist < REPEL_RADIUS && dist >= 0.001) {
					var push = (1 - dist / REPEL_RADIUS) * MAX_PUSH;
					ox += (dx / dist) * push;
					oy += (dy / dist) * push;
				}
			}
			if (clickTimeFactor > 0) {
				var cdx = pt[0] - click.x;
				var cdy = pt[1] - click.y;
				var cdist = Math.sqrt(cdx * cdx + cdy * cdy);
				if (cdist < CLICK_RADIUS && cdist >= 0.001) {
					var cpush = (1 - cdist / CLICK_RADIUS) * CLICK_MAX_PUSH * clickTimeFactor;
					ox += (cdx / cdist) * cpush;
					oy += (cdy / cdist) * cpush;
				}
			}
			return [pt[0] + ox, pt[1] + oy];
		});

		/* Edges only ever connect two points in the SAME layer — a line
		   spanning depths would visually flatten the illusion. */
		ctx.lineWidth = 1;
		for (var a = 0; a < POINTS; a++) {
			for (var b = a + 1; b < POINTS; b++) {
				if (points[a].layer !== points[b].layer) { continue; }
				var cfg = DEPTH_LAYERS[points[a].layer];
				var dx = points[a].x - points[b].x;
				var dy = points[a].y - points[b].y;
				var dist = Math.sqrt(dx * dx + dy * dy);
				if (dist < cfg.connectDist) {
					var alpha = (1 - dist / cfg.connectDist) * cfg.alphaWhite * 0.7;
					ctx.strokeStyle = WHITE + alpha.toFixed(3) + ')';
					ctx.beginPath();
					ctx.moveTo(displayed[a][0], displayed[a][1]);
					ctx.lineTo(displayed[b][0], displayed[b][1]);
					ctx.stroke();
				}
			}
		}

		/* Cursor-as-node: only front-layer points connect straight to the
		   mouse in accent orange, so the cursor visibly joins the nearest
		   layer rather than reaching all the way to the back. */
		if (mouse.active) {
			for (var m = 0; m < POINTS; m++) {
				if (!DEPTH_LAYERS[points[m].layer].interactive) { continue; }
				var mdx = displayed[m][0] - mouse.x;
				var mdy = displayed[m][1] - mouse.y;
				var mdist = Math.sqrt(mdx * mdx + mdy * mdy);
				if (mdist < MOUSE_CONNECT_RADIUS) {
					var mAlpha = (1 - mdist / MOUSE_CONNECT_RADIUS) * 0.55;
					ctx.strokeStyle = ACCENT + mAlpha.toFixed(3) + ')';
					ctx.beginPath();
					ctx.moveTo(mouse.x, mouse.y);
					ctx.lineTo(displayed[m][0], displayed[m][1]);
					ctx.stroke();
				}
			}
		}

		for (var j = 0; j < POINTS; j++) {
			var lcfg = DEPTH_LAYERS[points[j].layer];
			ctx.fillStyle = points[j].accent ? ACCENT + lcfg.alphaAccent.toFixed(2) + ')' : WHITE + lcfg.alphaWhite.toFixed(2) + ')';
			ctx.beginPath();
			ctx.arc(displayed[j][0], displayed[j][1], points[j].accent ? lcfg.radius * 1.3 : lcfg.radius, 0, Math.PI * 2);
			ctx.fill();
		}
	}

	function animate() {
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
