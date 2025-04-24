<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

if ( ! empty( $atts['html'] ) ) {
	echo $atts['html'];
}
