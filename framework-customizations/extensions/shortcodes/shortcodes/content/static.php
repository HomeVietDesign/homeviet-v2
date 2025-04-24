<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-content',
	$shortcodes_extension->locate_URI( '/shortcodes/content/static/css/style.css' ),
	[],
	'1.0'
);
