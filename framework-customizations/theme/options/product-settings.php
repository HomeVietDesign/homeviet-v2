<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'product' => array(
		'type' => 'tab',
		'title' => 'Sản phẩm',
		'options' => array(
			'product_info_heading1' => array(
				'label' => 'Tiêu đề thông tin mô tả sản phẩm 1',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_info_heading1',
				),
			),
			'product_info_heading2' => array(
				'label' => 'Tiêu đề thông tin mô tả sản phẩm 2',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_info_heading2',
				),
			),
			'single_product_footer' => [
				'type'  => 'multi-select',
				'population' => 'posts',
				'source' => 'content_builder',
				'limit' => 1,
				'label' => 'Nội dung cuối chi tiết sản phẩm',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'single_product_footer',
				),
			],

			'product_design_price' => array(
				'label' => 'Giá thiết kế chung',
				'desc'  => 'Đơn vị k/m2',
				'type'  => 'numeric',
				'integer' => true,
				'negative' => false,
				'size' => 'full',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_design_price',
				),
			),
			'product_price' => array(
				'label' => 'Giá đầu tư chung',
				'desc'  => 'Đơn vị k/m2',
				'type'  => 'numeric',
				'integer' => true,
				'negative' => false,
				'size' => 'full',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_price',
				),
			),
			
			'product_order_button_text' => array(
				'label' => 'Nhãn nút chọn mẫu ở chi tiết',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_order_button_text',
				),
			),
			'product_order_premium_button_text' => array(
				'label' => 'Nhãn nút chọn mẫu VIP ở chi tiết',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_order_premium_button_text',
				),
			),
			'product_loop_order_button_text' => array(
				'label' => 'Nhãn nút chọn mẫu ở danh sách',
				'desc'  => '',
				'type'  => 'text',
				'value' => 'CHỌN MẪU',
			),
			'product_order_popup_title' => array(
				'label' => 'Tiêu đề form chọn mẫu',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_order_popup_title',
				),
			),
			'product_order_premium_popup_title' => array(
				'label' => 'Tiêu đề form chọn mẫu VIP',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_order_premium_popup_title',
				),
			),
			'product_order_popup_desc' => array(
				'label' => 'Nội dung miêu tả form chọn mẫu',
				'desc'  => '',
				'type'  => 'wp-editor',
				'size' => 'large',
				'editor_height' => '300',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_order_popup_desc',
				),
			),
			/*
			'product_links' => array(
				'type' => 'addable-popup',
				'value' => array(),
				'label' => 'Nút link mở rộng',
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
			'product_kws_open_button_text' => array(
				'label' => 'Nhãn nút tìm kiếm',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
			),
			*/
		
		),
	),
);
