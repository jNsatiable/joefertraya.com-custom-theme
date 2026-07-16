<?php
/**
 * Category archive — used by /category/blog/ (the site's Blog listing).
 */

get_header();
?>

<section class="page-section blog-archive">
	<div class="container">
		<h1 class="page-title"><?php single_cat_title(); ?></h1>
		<?php if ( have_posts() ) : ?>
			<div class="blog-archive__grid">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article <?php post_class( 'blog-card' ); ?>>
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="blog-card__thumb" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
						<?php endif; ?>
						<div class="blog-card__body">
							<h2 class="blog-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="blog-card__date"><?php echo esc_html( get_the_date() ); ?></p>
							<p class="blog-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
							<a class="blog-card__more" href="<?php the_permalink(); ?>">Read more &rarr;</a>
						</div>
					</article>
					<?php
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
