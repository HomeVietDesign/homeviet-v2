<?php
$shortcodes_extension = fw_ext( 'shortcodes' );

wp_enqueue_style(
	'fw-shortcode-youtube-video',
	$shortcodes_extension->locate_URI( '/shortcodes/youtube-video/static/css/style.css' ),
	['bootstrap']
);
