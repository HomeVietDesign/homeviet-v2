<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

wp_enqueue_style( 'fw-ext-builder-frontend-grid' );

$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_script(
	'fw-shortcode-section',
	$shortcodes_extension->locate_URI( '/shortcodes/section/static/js/scripts.js' ),
	array(),
	false,
	true
);

wp_enqueue_style(
	'fw-shortcode-section',
	$shortcodes_extension->locate_URI( '/shortcodes/section/static/css/styles.css' ),
	[],
	'1.1'
);

