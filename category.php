<?php
/**
 * Category archive — used by /category/blog/ (the site's Blog listing).
 * Cards are the Rentl Digest overlay design (template-parts/post-card.php).
 */

get_header();
?>

<section class="page-section blog-archive">
	<div class="container">
		<h1 class="page-title"><?php single_cat_title(); ?></h1>
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
