<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-panorama',
	$shortcodes_extension->locate_URI( '/shortcodes/panorama/static/css/style.css' ),
	[],
	'0.1'
);

wp_enqueue_script(
	'fw-shortcode-panorama',
	$shortcodes_extension->locate_URI('/shortcodes/panorama/static/js/script.js'),
	array('jquery'),
	'0.1',
	true
);
