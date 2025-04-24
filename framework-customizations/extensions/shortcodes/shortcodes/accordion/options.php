<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'tabs' => array(
		'type'          => 'addable-popup',
		'label'         => __( 'Tabs', 'fw' ),
		'popup-title'   => __( 'Add/Edit Tabs', 'fw' ),
		'desc'          => __( 'Create your tabs', 'fw' ),
		'template'      => '{{=tab_title}}',
		'size'	=> 'large',
		'popup-options' => array(
			'tab_title'   => array(
				'type'  => 'text',
				'label' => __('Title', 'fw')
			),
			'tab_content' => array(
				'type'  => 'wp-editor',
				'size'	=> 'large',
				'label' => __('Content', 'fw')
			)
		)
	)
);