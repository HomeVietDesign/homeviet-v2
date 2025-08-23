<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'facebook' => array(
    'type' => 'tab',
		'title' => __('Cài đặt facebook'),
		'options' => array(
			'fb_pixel' => array(
				'label' => 'Pixel ID',
				'type'  => 'text',
			),
			'fb_pixel_at' => array(
				'label' => 'Access Token của Conversion API',
				'type'  => 'text',
			),
			
		),
	),
);