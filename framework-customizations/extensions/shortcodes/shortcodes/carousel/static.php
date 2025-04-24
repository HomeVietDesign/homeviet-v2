<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-carousel',
	$shortcodes_extension->locate_URI( '/shortcodes/carousel/static/css/style.css' ),
	['bootstrap'],
	'2.5'
);

// wp_enqueue_script(
// 	'fw-shortcode-carousel',
// 	$shortcodes_extension->locate_URI('/shortcodes/carousel/static/js/scripts.js'),
// 	array('bootstrap'),
// 	false,
// 	true
// );
