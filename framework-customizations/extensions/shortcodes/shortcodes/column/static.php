<?php if (!defined('FW')) die('Forbidden');

wp_enqueue_style('fw-ext-builder-frontend-grid');

$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-column',
	$shortcodes_extension->locate_URI( '/shortcodes/column/static/css/styles.css' ),
	false,
	'1.3'
);

wp_enqueue_script(
	'fw-shortcode-column',
	$shortcodes_extension->locate_URI( '/shortcodes/column/static/js/scripts.js' ),
	array(),
	false,
	true
);