<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-owlcarousel',
	$shortcodes_extension->locate_URI( '/shortcodes/owlcarousel/static/css/style.css' ),
	['owlcarousel'],
	'0.2'
);

wp_enqueue_script(
	'fw-shortcode-owlcarousel',
	$shortcodes_extension->locate_URI('/shortcodes/owlcarousel/static/js/scripts.js'),
	array('jquery', 'owlcarousel'),
	'0.2',
	true
);
