<?php
/**
 * Portfolio page gallery — replaces the Flickr iframe embeds (deferred
 * since PR #33/#34: the cross-origin iframe couldn't take the site's own
 * label styling, and a CSS stopgap was rejected on visual review).
 *
 * Photos stay hosted on Flickr's own CDN — only the embeddable iframe
 * widget is dropped, in favor of plain <img> tags the theme fully
 * controls, browsed via a hand-built grid + lightbox instead of Flickr's
 * carousel. Category names and photo lists are both admin-editable via a
 * meta box on the Portfolio page's own edit screen (no ACF, no separate
 * CPT) — one structured textarea, not a JS repeater UI:
 *
 *   ## Category Name
 *   https://live.staticflickr.com/.../photo.jpg | Optional caption
 *   https://live.staticflickr.com/.../photo2.jpg
 *
 * A blank saved field falls back to today's two Flickr cover photos, so
 * the page never goes empty before the real photo lists are filled in.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Default + parser
   ========================================================================== */

function jt_portfolio_gallery_default() {
	return "## Retouching\nhttps://live.staticflickr.com/65535/52113429390_67d3fe9f00_b.jpg | Beauty Retouching\n\n## Captures\nhttps://live.staticflickr.com/1726/41458306675_d78d3af15f_b.jpg | Personal Captures: A Glimpse Into My Lens";
}

/**
 * '## Category' starts a new category; every non-empty line after it,
 * until the next '##' line, is a photo ('URL' or 'URL | Caption') in
 * that category. Lines before the first '##' are ignored.
 */
function jt_parse_portfolio_gallery( $raw ) {
	$lines      = preg_split( '/\r\n|\r|\n/', (string) $raw );
	$categories = array();
	$index      = -1;

	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}

		if ( 0 === strpos( $line, '##' ) ) {
			$label = trim( substr( $line, 2 ) );
			if ( '' === $label ) {
				continue;
			}
			$categories[] = array(
				'label'  => $label,
				'photos' => array(),
			);
			++$index;
			continue;
		}

		if ( $index < 0 ) {
			continue; // Photo line before any '##' heading — nothing to attach it to.
		}

		$parts = array_map( 'trim', explode( '|', $line, 2 ) );
		$url   = esc_url_raw( $parts[0] );
		if ( '' === $url ) {
			continue;
		}
		$categories[ $index ]['photos'][] = array(
			'url'     => $url,
			'caption' => isset( $parts[1] ) ? $parts[1] : '',
		);
	}

	return $categories;
}

/**
 * $post_id is always the Portfolio page's own ID — page-portfolio.php is
 * only ever rendered for that page (jt_force_page_templates() routes it),
 * so this reads the current post's own meta rather than looking the page
 * up separately by slug.
 */
function jt_get_portfolio_gallery( $post_id ) {
	$raw = get_post_meta( $post_id, '_jt_portfolio_gallery_raw', true );
	if ( '' === trim( (string) $raw ) ) {
		$raw = jt_portfolio_gallery_default();
	}
	return jt_parse_portfolio_gallery( $raw );
}

/* ==========================================================================
   2. Meta box — Portfolio page's own edit screen only
   ========================================================================== */

function jt_add_portfolio_gallery_meta_box( $post_type, $post ) {
	if ( 'page' !== $post_type || ! $post || 'portfolio' !== $post->post_name ) {
		return;
	}
	add_meta_box(
		'jt_portfolio_gallery',
		'Portfolio Gallery',
		'jt_render_portfolio_gallery_meta_box',
		'page',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'jt_add_portfolio_gallery_meta_box', 10, 2 );

function jt_render_portfolio_gallery_meta_box( $post ) {
	wp_nonce_field( 'jt_portfolio_gallery_save', 'jt_portfolio_gallery_nonce' );
	$raw = get_post_meta( $post->ID, '_jt_portfolio_gallery_raw', true );
	?>
	<p>One block per category. Start a category with <code>##</code> followed by its name, then list that category's photo URLs below it — one per line, optionally <code>URL | Caption</code>.</p>
	<textarea name="jt_portfolio_gallery_raw" rows="18" class="large-text code" style="width: 100%; font-family: monospace;"><?php echo esc_textarea( $raw ); ?></textarea>
	<p class="description">Leave blank to use the default (today's two Flickr cover photos) until this is filled in.</p>
	<?php
}

function jt_save_portfolio_gallery_meta( $post_id ) {
	if (
		! isset( $_POST['jt_portfolio_gallery_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['jt_portfolio_gallery_nonce'] ) ), 'jt_portfolio_gallery_save' )
	) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_page', $post_id ) ) {
		return;
	}
	if ( ! isset( $_POST['jt_portfolio_gallery_raw'] ) ) {
		return;
	}
	update_post_meta( $post_id, '_jt_portfolio_gallery_raw', sanitize_textarea_field( wp_unslash( $_POST['jt_portfolio_gallery_raw'] ) ) );
}
add_action( 'save_post_page', 'jt_save_portfolio_gallery_meta' );
