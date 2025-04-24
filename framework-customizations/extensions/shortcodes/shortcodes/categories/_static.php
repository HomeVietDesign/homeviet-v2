<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-categories',
	$shortcodes_extension->locate_URI( '/shortcodes/categories/static/css/style.css' ),
	[],
	'1.0'
);
