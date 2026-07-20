<?php
/**
 * Portfolio page — replicates Elementor post 705. Galleries are a
 * hand-built grid + lightbox (photos hosted on Flickr's CDN, no iframe
 * embed) — see includes/portfolio-gallery.php for the category/photo
 * data model and its admin meta box.
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

<?php
$jt_gallery_categories = jt_get_portfolio_gallery( get_the_ID() );
$jt_gallery_total      = count( $jt_gallery_categories );
$jt_divider_i          = 1; // 0 is the hero's own bottom divider above, already flipped.
foreach ( $jt_gallery_categories as $jt_gallery_index => $jt_gallery_cat ) :
	$jt_gallery_tinted      = ( 0 === $jt_gallery_index % 2 );
	$jt_gallery_has_next    = ( $jt_gallery_index < $jt_gallery_total - 1 );
	// The next section's own tint state decides this divider's fill color
	// (it's cutting INTO that section) — if there's no next category, the
	// next thing is the CTA band, which is always --color-light-section.
	$jt_gallery_next_tinted = $jt_gallery_has_next ? ( 0 === ( $jt_gallery_index + 1 ) % 2 ) : true;
	$jt_gallery_flip        = ( 0 === $jt_divider_i % 2 );
	++$jt_divider_i;
	?>
	<section class="page-section gallery-section<?php echo $jt_gallery_tinted ? ' gallery-section--tint' : ''; ?>">
		<div class="container">
			<h2 class="section-title"><?php echo esc_html( $jt_gallery_cat['label'] ); ?></h2>
			<?php if ( ! empty( $jt_gallery_cat['photos'] ) ) : ?>
				<h5 class="gallery-section__hint">Click a photo to view it larger</h5>
				<div class="gallery-grid" data-gallery="<?php echo esc_attr( sanitize_title( $jt_gallery_cat['label'] ) ); ?>">
					<?php foreach ( $jt_gallery_cat['photos'] as $jt_photo_index => $jt_photo ) :
						$jt_photo_alt = '' !== $jt_photo['caption'] ? $jt_photo['caption'] : ( $jt_gallery_cat['label'] . ' photo ' . ( $jt_photo_index + 1 ) );
						?>
						<button type="button" class="gallery-tile" data-index="<?php echo (int) $jt_photo_index; ?>">
							<img src="<?php echo esc_url( $jt_photo['url'] ); ?>" alt="<?php echo esc_attr( $jt_photo_alt ); ?>" loading="lazy">
							<span class="gallery-tile__overlay" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg>
							</span>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<svg class="jt-divider jt-divider--bottom<?php echo $jt_gallery_flip ? ' jt-divider--flip' : ''; ?> jt-divider--fill-<?php echo $jt_gallery_next_tinted ? 'light' : 'bg'; ?>" viewBox="0 0 1000 100" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path d="M761.9,44.1L643.1,27.2L333.8,98L0,3.8V0l1000,0v3.9"/></svg>
	</section>
<?php endforeach; ?>

<div class="lightbox" data-lightbox aria-hidden="true">
	<div class="lightbox__stage">
		<button type="button" class="lightbox__close" data-lightbox-close aria-label="Close">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M6 6L18 18M18 6L6 18"/></svg>
		</button>
		<button type="button" class="lightbox__nav lightbox__nav--prev" data-lightbox-prev aria-label="Previous photo">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
		</button>
		<div class="lightbox__frame">
			<img class="lightbox__image" data-lightbox-image src="" alt="">
		</div>
		<button type="button" class="lightbox__nav lightbox__nav--next" data-lightbox-next aria-label="Next photo">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
		</button>
		<div class="lightbox__meta">
			<span data-lightbox-caption></span>
			<span data-lightbox-count></span>
		</div>
	</div>
</div>

<section class="page-section jt-cta">
	<div class="container">
		<h3 class="jt-cta__title">Got questions for J that just can't wait?</h3>
		<a class="jt-btn" href="<?php echo esc_url( home_url( '/contact-form/' ) ); ?>">LET'S TALK</a>
	</div>
</section>

<?php
get_footer();
