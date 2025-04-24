<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-apply-position',
	$shortcodes_extension->locate_URI( '/shortcodes/apply-position/static/css/style.css' ),
	[],
	'0.3'
);

wp_enqueue_script(
	'fw-shortcode-apply-position',
	$shortcodes_extension->locate_URI('/shortcodes/apply-position/static/js/script.js'),
	array('jquery'),
	'0.2',
	true
);
