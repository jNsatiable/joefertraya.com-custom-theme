<?php
/**
 * Search results — WordPress's default search scope mixes Posts and Pages,
 * so results render as a simple list rather than the Blog's image-driven
 * post-card grid (Pages have no featured image to build that card around).
 * Mockup approved 2026-07-18.
 */

get_header();
?>

<section class="page-hero">
	<div class="container">
		<h1 class="page-title">Search results for &ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>
		<p class="page-hero__intro">
			<?php
			if ( have_posts() ) {
				printf(
					/* translators: %d: number of search results */
					esc_html( _n( '%d result found.', '%d results found.', $GLOBALS['wp_query']->found_posts, 'joefertraya' ) ),
					(int) $GLOBALS['wp_query']->found_posts
				);
			} else {
				esc_html_e( 'No results found.', 'joefertraya' );
			}
			?>
		</p>
	</div>
</section>

<section class="page-section">
	<div class="container">
		<?php get_template_part( 'template-parts/search-form' ); ?>

		<?php if ( have_posts() ) : ?>
			<div class="search-results">
				<?php
				while ( have_posts() ) {
					the_post();
					?>
					<article <?php post_class( 'search-result' ); ?>>
						<?php if ( 'post' === get_post_type() ) : ?>
							<p class="search-result__meta">Post &middot; <?php echo esc_html( get_the_date() ); ?></p>
						<?php else : ?>
							<p class="search-result__meta"><?php echo esc_html( ucfirst( get_post_type() ) ); ?></p>
						<?php endif; ?>
						<h2 class="search-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<p class="search-result__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 30 ); ?></p>
					</article>
					<?php
				}
				?>
			</div>
			<div class="search-results__pagination"><?php the_posts_pagination(); ?></div>
		<?php else : ?>
			<div class="search-empty">
				<p>Nothing matched that search. Try a different term, or browse the <a href="<?php echo esc_url( home_url( '/category/blog/' ) ); ?>">Blog</a> directly.</p>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
