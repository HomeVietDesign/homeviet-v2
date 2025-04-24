<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-counterup',
	$shortcodes_extension->locate_URI( '/shortcodes/counterup/static/css/style.css' ),
	[],
	'1.0'
);

wp_enqueue_script(
	'counterup',
	'https://unpkg.com/counterup2@2.0.2/dist/index.js',
	array(),
	false,
	true
);
wp_enqueue_script(
	'fw-shortcode-counterup',
	$shortcodes_extension->locate_URI( '/shortcodes/counterup/static/js/script.js' ),
	array( 'jquery', 'counterup' ),
	false,
	true
);
