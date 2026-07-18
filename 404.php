<?php
/**
 * 404 template — previously fell through to index.php's bare post-loop
 * fallback (an empty page with no recovery path). Mockup approved 2026-07-18.
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<p class="notfound-figure" aria-hidden="true">404</p>
		<h1 class="page-title">Well, this is awkward.</h1>
		<p class="page-hero__intro">The page you're looking for doesn't exist — it might've been moved, renamed, or never was. Let's get you back on track.</p>
	</div>
</section>

<section class="page-section">
	<div class="container">
		<?php get_template_part( 'template-parts/search-form' ); ?>

		<h2 class="notfound-links__heading">Or try one of these</h2>
		<ul class="notfound-links">
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
			<li><a href="<?php echo esc_url( home_url( '/portfolio/' ) ); ?>">Portfolio</a></li>
			<li><a href="<?php echo esc_url( home_url( '/category/blog/' ) ); ?>">Blog</a></li>
			<li><a href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">Get in touch</a></li>
		</ul>
	</div>
</section>

<?php
get_footer();
