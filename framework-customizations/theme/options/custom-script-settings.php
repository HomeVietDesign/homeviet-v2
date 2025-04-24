<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'custom-script' => array(
		'type' => 'tab',
		'title' => __('Custom Script'),
		'options' => array(
			'head_code' => array(
				'label' => __( 'Head Code' ),
				'desc'  => '',
				'type'  => 'code-editor',
				'value' => ''
			),
			'body_code' => array(
				'label' => __( 'Open Body Code' ),
				'desc'  => '',
				'type'  => 'code-editor',
				'value' => ''
			),
			'footer_code' => array(
				'label' => __( 'Footer Code' ),
				'desc'  => '',
				'type'  => 'code-editor',
				'value' => ''
			),
		),
	),
);