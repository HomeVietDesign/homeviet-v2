<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

// wp_enqueue_style(
// 	'fw-shortcode-media-video',
// 	$shortcodes_extension->locate_URI( '/shortcodes/media-video/static/css/style.css' ),
// 	['bootstrap']
// );


wp_enqueue_script(
	'fw-shortcode-media-video',
	$shortcodes_extension->locate_URI( '/shortcodes/media-video/static/js/scripts.js' ),
	['jquery'],
	false,
	true
);
