<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'images' => array(
		'type' => 'multi-upload',
		'label' => 'Danh sách ảnh',
		'images_only' => true,
		'files_ext' => array( 'png', 'jpg', 'jpeg' ),
	),
	'autoplay' => array(
		'label' => 'Tự động chạy?',
		'desc'  => '',
		'value'  => 'yes',
		'type'  => 'switch',
		'left-choice' => array(
			'value' => 'no',
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 'yes',
			'label' => 'Có',
		),
	),
	'interval' => array(
		'label' => 'Thời gian tự động',
		'value' => 5,
		'desc'  => 'Thời lượng tự động chuyển giữa các ảnh, tính bằng giây (chỉ có tác dụng khi tự động chuyển ảnh được bật).',
		'type'  => 'numeric'
	),
	'dots' => array(
		'label' => 'Chấm vuông chuyển ảnh?',
		'desc'  => '',
		'value'  => 'yes',
		'type'  => 'switch',
		'left-choice' => array(
			'value' => 'no',
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 'yes',
			'label' => 'Có',
		),
	),
	'navs' => array(
		'label' => 'Nút trái/phải chuyển ảnh?',
		'desc'  => '',
		'value'  => 'yes',
		'type'  => 'switch',
		'left-choice' => array(
			'value' => 'no',
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 'yes',
			'label' => 'Có',
		),
	),
	
);
