<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$shortcodes_extension = fw_ext( 'shortcodes' );


wp_enqueue_style(
	'fw-shortcode-text-boxies',
	$shortcodes_extension->locate_URI( '/shortcodes/text-boxies/static/css/styles.css' )
);

