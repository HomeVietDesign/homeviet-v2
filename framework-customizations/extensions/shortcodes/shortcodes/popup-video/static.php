<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-popup-video',
	$shortcodes_extension->locate_URI( '/shortcodes/popup-video/static/css/style.css' ),
	['bootstrap']
);


wp_enqueue_script(
	'fw-shortcode-popup-video',
	$shortcodes_extension->locate_URI( '/shortcodes/popup-video/static/js/script.js' ),
	['bootstrap', 'jquery'],
	false,
	true
);
