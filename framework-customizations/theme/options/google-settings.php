<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'google' => array(
    'type' => 'tab',
		'title' => __('Cài đặt google'),
		'options' => array(
			'map_api_key' => array(
				'label' => __( 'Google map api key' ),
				'type'  => 'text',
			),

			'recaptcha_key' => array(
				'label' => __( 'Recaptcha key' ),
				'type'  => 'text',
			),
			'recaptcha_secret' => array(
				'label' => __( 'Recaptcha secret' ),
				'type'  => 'text',
			),

		),
	),
);