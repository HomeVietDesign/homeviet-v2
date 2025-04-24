<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	array(
		'type' => 'tab',
		'title' => 'Form',
		'options' => array(
			'position' => array(
				'label' => 'Vị trí ứng tuyển',
				'desc'  => '',
				'type'  => 'text',
			),
			'submit_text' => array(
				'label' => 'Văn bản nút gửi',
				'desc'  => '',
				'type'  => 'text',
			),
		)
	),
);
