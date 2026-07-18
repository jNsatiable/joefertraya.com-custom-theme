<?php
/**
 * Home page "Tools I work with" widgets — a monochrome auto-scroll marquee
 * (breadth: the full toolkit) and a split-flap-style flip board (a single
 * curated highlight, one at a time). Both admin-editable from Settings >
 * JT Theme, reusing the options page contact-form.php already registers —
 * an empty item list falls back to the default list below.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ==========================================================================
   1. Defaults — spans the categories already listed in front-page.php's
      $jt_skills rotator (eCommerce, web dev, automation, PM, data, design/
      photo/video), ordered with the most recognizable/searched names first.
   ========================================================================== */

function jt_tools_marquee_default_items() {
	return array(
		'Shopify',
		'WordPress',
		'WooCommerce',
		'Klaviyo',
		'Zapier',
		'Notion',
		'Airtable',
		'Python',
		'SQL',
		'Power BI',
		'Photoshop',
		'Lightroom',
		'Premiere Pro',
		'After Effects',
		'React',
		'Salesforce',
		'n8n',
		'Google Sheets',
	);
}

function jt_tools_flap_default_items() {
	return array(
		'Shopify',
		'WordPress',
		'Python',
		'Zapier',
		'Notion',
		'Photoshop',
		'SQL',
		'Premiere Pro',
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

function jt_sanitize_flap_speed( $value ) {
	$value = (float) $value;
	return $value > 0 ? $value : 1;
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
	register_setting(
		'jt_settings_group',
		'jt_tools_flap_items',
		array(
			'type'              => 'string',
			'sanitize_callback' => 'sanitize_textarea_field',
			'default'           => '',
		)
	);
	register_setting(
		'jt_settings_group',
		'jt_tools_flap_speed',
		array(
			'type'              => 'number',
			'sanitize_callback' => 'jt_sanitize_flap_speed',
			'default'           => 1,
		)
	);

	add_settings_section(
		'jt_tools_widgets',
		'Home page "Tools" widgets',
		function () {
			echo '<p>The scrolling marquee and the flip board below the home hero. One item per line; leave a list blank to use the built-in default.</p>';
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
	add_settings_field(
		'jt_tools_flap_items',
		'Flip board — items',
		'jt_settings_field_textarea',
		'jt-theme',
		'jt_tools_widgets',
		array(
			'name' => 'jt_tools_flap_items',
			'desc' => 'Blank = default list (' . count( jt_tools_flap_default_items() ) . ' curated tools).',
		)
	);
	add_settings_field(
		'jt_tools_flap_speed',
		'Flip board — flip speed',
		'jt_settings_field_number',
		'jt-theme',
		'jt_tools_widgets',
		array(
			'name' => 'jt_tools_flap_speed',
			'desc' => 'Seconds per flip (the flip motion itself, not how long each word holds). Default 1.',
			'min'  => 0.2,
			'step' => 0.1,
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
 * Two duplicate sets back to back, scrolled left by exactly one set's width
 * (translateX(-50%) — see home-hero.css) for a seamless loop; the second is
 * aria-hidden since it's a visual duplicate, not new content.
 */
function jt_render_tools_marquee() {
	$items = jt_tools_widget_parse_items( get_option( 'jt_tools_marquee_items', '' ), jt_tools_marquee_default_items() );
	$speed = get_option( 'jt_tools_marquee_speed', 34 );
	?>
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
	<?php
}

/**
 * Single flap, whole-word flip (not per-character split-flap — word lengths
 * vary too much for a fixed character grid). tools-widgets.js drives the
 * cycle from data-items/data-speed; PHP only needs to print the first word
 * so there's real content before JS runs.
 */
function jt_render_tools_flap() {
	$items = jt_tools_widget_parse_items( get_option( 'jt_tools_flap_items', '' ), jt_tools_flap_default_items() );
	$speed = get_option( 'jt_tools_flap_speed', 1 );
	?>
	<div class="jt-flap-widget" data-items="<?php echo esc_attr( wp_json_encode( $items ) ); ?>" data-speed="<?php echo esc_attr( $speed ); ?>">
		<span class="jt-flap-widget__label">Tools in the mix:</span>
		<span class="jt-flap"><span class="jt-flap__text"><?php echo esc_html( $items[0] ); ?></span></span>
	</div>
	<?php
}
