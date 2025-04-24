<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'footer' => array(
		'type' => 'tab',
		'title' => __('Footer'),
		'options' => array(
			'divider_footer_link' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Các nút link cố định cuối trang, bên trên nút gọi.</strong>',
				'size' => 'large',
			),
			'footer_links' => array(
				'type' => 'addable-popup',
				'value' => array(),
				'label' => 'Nút link',
				'desc'  => '',
				'template' => '{{=name}}',
				'popup-title' => 'Thêm link',
				'size' => 'small', // small, medium, large
				'limit' => 0, // limit the number of popup`s that can be added
				'add-button-text' => 'Thêm',
				'sortable' => true,
				'popup-options' => array(
					'name' => array(
						'label' => 'Nhãn nút',
						'type' => 'text',
						'value' => '',
					),
					'url' => array(
						'label' => 'URL',
						'type' => 'text',
						'desc' => 'Đường dẫn chuyển đến khi click vào nút.',
						'value' => '',
					),
				),
			),
			'divider_footer_color' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt màu cuối trang</strong>',
				'size' => 'large',
			),
			'footer_bg_color' => [
				'type'  => 'color-picker',
				'value' => '#000000',
				'label' => __('Footer background'),
			],
			'footer_color' => [
				'type'  => 'color-picker',
				'value' => '#ffffff',
				'label' => __('Footer text color'),
			]
		),
	),
);