<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	fw()->theme->get_options( 'general-settings' ),
	fw()->theme->get_options( 'footer-settings' ),
	fw()->theme->get_options( 'google-settings' ),
	//fw()->theme->get_options( 'facebook-settings' ),
	fw()->theme->get_options( 'product-settings' ),
	fw()->theme->get_options( 'contractor-settings' ),
	fw()->theme->get_options( 'custom-script-settings' ),
);
