<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-grid-images',
	$shortcodes_extension->locate_URI( '/shortcodes/grid-images/static/css/style.css' ),
	[],
	'1.0'
);

wp_enqueue_script(
	'fw-shortcode-grid-images',
	$shortcodes_extension->locate_URI('/shortcodes/grid-images/static/js/script.js'),
	array('jquery', 'isotope', 'imagesloaded'),
	'1.0',
	true
);
