<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-media-image',
	$shortcodes_extension->locate_URI( '/shortcodes/media-image/static/css/style.css' ),
	[],
	'1.0'
);
