<?php
/**
 * Category archive — used by /category/blog/ (the site's Blog listing).
 * Cards are the Rentl Digest overlay design (template-parts/post-card.php).
 */

get_header();
?>

<section class="blog-hero">
	<div class="container">
		<?php /* Hybrid naming (2026-07-17): the nav keeps "Blog" for instant
		   recognition; the page's own hero carries the brand name instead.
		   Falls back to the category name for any future non-blog category. */ ?>
		<h1 class="page-title"><?php echo is_category( 'blog' ) ? 'The J Files' : esc_html( single_cat_title( '', false ) ); ?></h1>
		<p class="blog-hero__intro">Sharing what I've learned the hard way &mdash; fixes for the stuff that breaks, shortcuts for the stuff that shouldn't take as long as it does, and the tools that actually earn a spot in my workflow.</p>
	</div>
</section>

<section class="page-section blog-archive">
	<div class="container">
		<?php if ( have_posts() ) : ?>
			<div class="blog-archive__grid">
				<?php
				$jt_index = 0;
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/post-card', null, array( 'index' => $jt_index ) );
					$jt_index++;
				}
				?>
			</div>
			<div class="blog-archive__pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<p>No posts yet.</p>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
