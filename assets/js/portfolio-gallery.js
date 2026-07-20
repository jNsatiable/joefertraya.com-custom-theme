/**
 * Portfolio page gallery lightbox — replaces the Flickr iframe's own
 * carousel now that photos render as plain <img> tiles this theme
 * controls directly (see includes/portfolio-gallery.php). Navigation is
 * scoped per category: opening a photo from one gallery grid only
 * cycles through that same grid's photos, matching how each Flickr
 * album used to be independent.
 */
(function () {
	'use strict';

	var lightbox = document.querySelector('[data-lightbox]');
	if (!lightbox) {
		return;
	}

	var imageEl = lightbox.querySelector('[data-lightbox-image]');
	var captionEl = lightbox.querySelector('[data-lightbox-caption]');
	var countEl = lightbox.querySelector('[data-lightbox-count]');
	var currentGallery = null;
	var currentIndex = 0;
	var lastFocused = null;

	function tilesFor(gallery) {
		return Array.prototype.slice.call(
			document.querySelectorAll('.gallery-grid[data-gallery="' + gallery + '"] .gallery-tile')
		);
	}

	function show(gallery, index) {
		var tiles = tilesFor(gallery);
		if (!tiles.length) {
			return;
		}
		index = (index + tiles.length) % tiles.length;
		currentGallery = gallery;
		currentIndex = index;

		var img = tiles[index].querySelector('img');
		imageEl.src = img.src;
		imageEl.alt = img.alt;
		captionEl.textContent = img.alt;
		countEl.textContent = (index + 1) + ' / ' + tiles.length;

		lightbox.classList.add('is-open');
		lightbox.setAttribute('aria-hidden', 'false');
	}

	function open(gallery, index) {
		lastFocused = document.activeElement;
		show(gallery, index);
		lightbox.querySelector('[data-lightbox-close]').focus();
	}

	function close() {
		lightbox.classList.remove('is-open');
		lightbox.setAttribute('aria-hidden', 'true');
		if (lastFocused && typeof lastFocused.focus === 'function') {
			lastFocused.focus();
		}
	}

	document.querySelectorAll('.gallery-tile').forEach(function (tile) {
		tile.addEventListener('click', function () {
			var grid = tile.closest('.gallery-grid');
			var gallery = grid.getAttribute('data-gallery');
			var index = parseInt(tile.getAttribute('data-index'), 10) || 0;
			open(gallery, index);
		});
	});

	lightbox.querySelector('[data-lightbox-close]').addEventListener('click', close);
	lightbox.querySelector('[data-lightbox-prev]').addEventListener('click', function () {
		show(currentGallery, currentIndex - 1);
	});
	lightbox.querySelector('[data-lightbox-next]').addEventListener('click', function () {
		show(currentGallery, currentIndex + 1);
	});

	lightbox.addEventListener('click', function (e) {
		if (e.target === lightbox) {
			close();
		}
	});

	document.addEventListener('keydown', function (e) {
		if (!lightbox.classList.contains('is-open')) {
			return;
		}
		if (e.key === 'Escape') {
			close();
		} else if (e.key === 'ArrowLeft') {
			show(currentGallery, currentIndex - 1);
		} else if (e.key === 'ArrowRight') {
			show(currentGallery, currentIndex + 1);
		}
	});
})();
