<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'facebook' => array(
    'type' => 'tab',
		'title' => __('Cài đặt facebook'),
		'options' => array(
			'fbapp_id' => array(
				'label' => __( 'Facebook app id' ),
				'type'  => 'text',
			),
			'fbapp_secret' => array(
				'label' => __( 'Facebook app secret' ),
				'type'  => 'password',
			),
			'fbapp_auth_uri' => array(
				'label' => __( 'Facebook Authorized redirect URIs' ),
				'type'  => 'text',
			),
		),
	),
);