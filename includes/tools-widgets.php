<?php
/**
 * Home page "Tools I work with" marquee — a monochrome auto-scroll strip
 * of the full toolkit, admin-editable from Settings > JT Theme, reusing the
 * options page contact-form.php already registers. An empty item list
 * falls back to the default list below.
 *
 * A split-flap "flip board" companion widget shipped alongside this
 * (PR #75) and was removed the next day (2026-07-19) — the flip didn't
 * track correctly live, and the marquee alone covers the same job.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Defaults — curated from Joefer's full toolkit list (2026-07-20),
      spanning the same categories as front-page.php's $jt_skills rotator
      (eCommerce, web dev, automation, PM, data, design/photo/video) plus
      AI tools and hosting, ordered with the most recognizable/searched
      names first. Deliberately not the complete inventory: service
      descriptions ("Shopify Web Design"), category labels ("Applicant
      Tracking Systems"), and niche/internal picks (specific Shopify apps,
      WP plugins, ATS tools) are left out — this is a highlight reel meant
      to register at a skim, not a full list; Settings > JT Theme can
      override it entirely. AI tools use plain brand names (ChatGPT, not
      "ChatGPT Codex") since the marquee's audience is prospective clients,
      not developers who'd recognize the specific product.
   ========================================================================== */

function jt_tools_marquee_default_items() {
	return array(
		'Shopify',
		'WordPress',
		'Figma',
		'WooCommerce',
		'ChatGPT',
		'Klaviyo',
		'Zapier',
		'Notion',
		'Photoshop',
		'Python',
		'SQL',
		'Airtable',
		'Lightroom',
		'Premiere Pro',
		'React',
		'Power BI',
		'Salesforce',
		'n8n',
		'Google Sheets',
		'GitHub',
		'Hostinger',
	);
}

/**
 * One item per line in the admin textarea; falls back to $default if the
 * saved value is blank or parses to nothing.
 */
function jt_tools_widget_parse_items( $raw, $default ) {
	$lines = preg_split( '/[\r\n]+/', (string) $raw );
	$items = array_values( array_filter( array_map( 'trim', $lines ) ) );
	return $items ? $items : $default;
}

/* ==========================================================================
   2. Settings — Settings > JT Theme (page already registered in
      contact-form.php; add_settings_section()/add_settings_field() targeting
      the same 'jt-theme' page slug is how a second file contributes fields
      to it without re-registering the page).
   ========================================================================== */

function jt_sanitize_marquee_speed( $value ) {
	$value = absint( $value );
	return $value > 0 ? $value : 34;
}

function jt_register_tools_widget_settings() {
	register_setting(
		'jt_settings_group',
		'jt_tools_marquee_items',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		)
	);
	register_setting(
		'jt_settings_group',
		'jt_tools_marquee_speed',
		array(
			'type'              => 'integer',
			'sanitize_callback' => 'jt_sanitize_marquee_speed',
			'default'           => 34,
		)
	);

	add_settings_section(
		'jt_tools_widgets',
		'Home page "Tools" marquee',
		function () {
			echo '<p>The scrolling tools strip below the home hero. One item per line; leave the list blank to use the built-in default.</p>';
		},
		'jt-theme'
	);

	add_settings_field(
		'jt_tools_marquee_items',
		'Marquee — items',
		'jt_settings_field_textarea',
		'jt-theme',
		'jt_tools_widgets',
		array(
			'name' => 'jt_tools_marquee_items',
			'desc' => 'Blank = default list (' . count( jt_tools_marquee_default_items() ) . ' tools).',
		)
	);
	add_settings_field(
		'jt_tools_marquee_speed',
		'Marquee — loop speed',
		'jt_settings_field_number',
		'jt-theme',
		'jt_tools_widgets',
		array(
			'name' => 'jt_tools_marquee_speed',
			'desc' => 'Seconds for one full scroll loop. Lower = faster. Default 34.',
			'min'  => 5,
			'step' => 1,
		)
	);
}
add_action( 'admin_init', 'jt_register_tools_widget_settings' );

function jt_settings_field_textarea( $args ) {
	$value = get_option( $args['name'], '' );
	printf(
		'<textarea name="%s" rows="5" cols="50" class="large-text code">%s</textarea>',
		esc_attr( $args['name'] ),
		esc_textarea( $value )
	);
	if ( ! empty( $args['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
	}
}

function jt_settings_field_number( $args ) {
	$value = get_option( $args['name'], '' );
	printf(
		'<input type="number" name="%s" value="%s" min="%s" step="%s" class="small-text">',
		esc_attr( $args['name'] ),
		esc_attr( $value ),
		esc_attr( $args['min'] ?? 0 ),
		esc_attr( $args['step'] ?? 1 )
	);
	if ( ! empty( $args['desc'] ) ) {
		printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
	}
}

/* ==========================================================================
   3. Render
   ========================================================================== */

/**
 * A pinned label sits on the same line as the strip (2026-07-20 redesign —
 * previously stacked above it) rather than scrolling with it; only the
 * track to its right scrolls. Two duplicate sets back to back, scrolled
 * left by exactly one set's width (translateX(-50%) — see home-hero.css)
 * for a seamless loop; the second is aria-hidden since it's a visual
 * duplicate, not new content.
 */
function jt_render_tools_marquee() {
	$items = jt_tools_widget_parse_items( get_option( 'jt_tools_marquee_items', '' ), jt_tools_marquee_default_items() );
	$speed = get_option( 'jt_tools_marquee_speed', 34 );
	?>
	<div class="jt-tools-row">
		<span class="jt-tools-marquee__label">Tools I use</span>
		<div class="jt-tools-marquee">
			<div class="jt-tools-marquee__track" style="animation-duration: <?php echo esc_attr( $speed ); ?>s;">
				<?php for ( $set = 0; $set < 2; $set++ ) : ?>
					<div class="jt-tools-marquee__set"<?php echo 1 === $set ? ' aria-hidden="true"' : ''; ?>>
						<?php foreach ( $items as $item ) : ?>
							<span class="jt-tools-marquee__item"><?php echo esc_html( $item ); ?></span><span class="jt-tools-marquee__dot" aria-hidden="true"></span>
						<?php endforeach; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
	<?php
}
