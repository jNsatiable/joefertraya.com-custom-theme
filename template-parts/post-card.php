<?php
/**
 * Blog post card — Rentl Digest overlay design: the image IS the card,
 * title + date on a bottom gradient scrim, category pills top-left.
 * The excerpt is deliberately absent (Rentl decision: the title carries
 * the click). Used by the category archive; reusable for future archive
 * and author pages via get_template_part( 'template-parts/post-card',
 * null, array( 'index' => $i ) ).
 */

// First card gets the eager/high-priority image treatment (one true LCP
// element only — Rentl/Framework lesson); the rest stay lazy.
$jt_card_index  = isset( $args['index'] ) ? (int) $args['index'] : PHP_INT_MAX;
$jt_image_attrs = 0 === $jt_card_index
	? array( 'alt' => '', 'loading' => 'eager', 'decoding' => 'async', 'fetchpriority' => 'high' )
	: array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' );

// Hide the pill for the category being browsed — on /category/blog/ every
// card would otherwise wear an identical "Blog" pill. Topic pills appear
// automatically once Joefer's planned categories exist.
$jt_categories = get_the_category();
$jt_current    = is_category() ? get_queried_object_id() : 0;
?>

<article <?php post_class( 'post-card' ); ?>>

	<?php /* Full-card click target — a real anchor, not a stretched-link
	pseudo-element (Rentl bug: the pseudo's containing block resolved to the
	overlay, leaving only the bottom strip clickable). */ ?>
	<a href="<?php the_permalink(); ?>" class="post-card__link" aria-label="<?php the_title_attribute(); ?>"></a>

	<div class="post-card__image">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'medium_large', $jt_image_attrs ); ?>
		<?php else : ?>
			<div class="post-card__image-placeholder">
				<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
					<circle cx="8.5" cy="8.5" r="1.5"></circle>
					<polyline points="21 15 16 10 5 21"></polyline>
				</svg>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $jt_categories ) && ! is_wp_error( $jt_categories ) ) : ?>
		<div class="post-card__badges">
			<?php foreach ( $jt_categories as $jt_cat ) : ?>
				<?php if ( $jt_cat->term_id === $jt_current ) { continue; } ?>
				<span class="post-card__badge"><?php echo esc_html( $jt_cat->name ); ?></span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<div class="post-card__overlay">
		<h3 class="post-card__title"><?php the_title(); ?></h3>
		<div class="post-card__meta"><?php echo esc_html( get_the_date() ); ?></div>
	</div>

</article>
