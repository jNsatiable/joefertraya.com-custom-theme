<?php
/**
 * Portfolio page — replicates Elementor post 705. Galleries remain Flickr
 * embeds (the live site's existing gallery mechanism).
 */

get_header();
?>

<section class="portfolio-hero">
	<svg class="jt-divider jt-divider--top jt-divider--fill-bg" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>
	<div class="container portfolio-hero__inner">
		<h1 class="portfolio-hero__title">Hey!</h1>
		<p>Unfortunately, most of J's skills can't be easily represented visually, so he is actively working on finding effective ways to showcase them.</p>
		<p>For the meantime, feel free to explore his latest projects through the following channels. Additionally, a curated collection of his photography and retouching work is featured below.</p>
		<?php jt_social_icons( 'portfolio-hero__social' ); ?>
	</div>
	<svg class="jt-divider jt-divider--bottom jt-divider--flip jt-divider--fill-light" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>
</section>

<section class="page-section gallery-section gallery-section--tint">
	<div class="container">
		<h2 class="section-title">Retouching</h2>
		<h5 class="gallery-section__hint">(Tap on side edges to browse gallery)</h5>
		<div class="gallery-section__embed">
			<a data-flickr-embed="true" href="https://www.flickr.com/photos/164769429@N03/albums/72177720299414240" title="Beauty Retouching"><img src="https://live.staticflickr.com/65535/52113429390_67d3fe9f00_b.jpg" srcset="https://live.staticflickr.com/65535/52113429390_67d3fe9f00_n.jpg 320w, https://live.staticflickr.com/65535/52113429390_67d3fe9f00_z.jpg 640w, https://live.staticflickr.com/65535/52113429390_67d3fe9f00_c.jpg 800w, https://live.staticflickr.com/65535/52113429390_67d3fe9f00_b.jpg 1024w" sizes="(max-width: 1180px) calc(100vw - 40px), 1100px" width="1024" height="768" alt="Beauty Retouching" fetchpriority="high"></a>
		</div>
	</div>
	<svg class="jt-divider jt-divider--bottom jt-divider--fill-bg" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>
</section>

<section class="page-section gallery-section">
	<div class="container">
		<h2 class="section-title">Captures</h2>
		<h5 class="gallery-section__hint">(Tap on side edges to browse gallery)</h5>
		<div class="gallery-section__embed">
			<a data-flickr-embed="true" href="https://www.flickr.com/photos/164769429@N03/albums/72177720314447888" title="Personal Captures: A Glimpse Into My Lens"><img src="https://live.staticflickr.com/1726/41458306675_d78d3af15f_b.jpg" srcset="https://live.staticflickr.com/1726/41458306675_d78d3af15f_n.jpg 320w, https://live.staticflickr.com/1726/41458306675_d78d3af15f_z.jpg 640w, https://live.staticflickr.com/1726/41458306675_d78d3af15f_c.jpg 800w, https://live.staticflickr.com/1726/41458306675_d78d3af15f_b.jpg 1024w" sizes="(max-width: 1180px) calc(100vw - 40px), 1100px" width="1024" height="768" alt="Personal Captures: A Glimpse Into My Lens" loading="lazy"></a>
		</div>
	</div>
	<svg class="jt-divider jt-divider--bottom jt-divider--flip jt-divider--fill-light" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>
</section>

<section class="page-section jt-cta">
	<div class="container">
		<h3 class="jt-cta__title">Got questions for J that just can't wait?</h3>
		<a class="jt-btn" href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">LET'S TALK</a>
	</div>
</section>

<script async src="https://embedr.flickr.com/assets/client-code.js" charset="utf-8"></script>

<?php
get_footer();
