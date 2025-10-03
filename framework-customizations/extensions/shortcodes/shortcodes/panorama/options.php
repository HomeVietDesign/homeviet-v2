<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'image' => array(
		'type' => 'upload',
		'label' => 'Ảnh 360',
		'images_only' => true,
		'files_ext' => array( 'png', 'jpg', 'jpeg' ),
	),
	'ratio' => array(
		'type' => 'numeric',
		'integer' => false,
		'label' => 'Tỉ lệ kích thước (%)',
		'value' => 50
	),
);
