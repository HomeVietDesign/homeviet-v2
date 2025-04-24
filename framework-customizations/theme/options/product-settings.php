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
			'product_total_percent' => array(
				'label' => 'Hệ số % tổng đầu tư',
				'desc'  => "Dùng để nhân với tổng đầu tư gốc tính ra tổng đầu tư mới. Đơn vị là %.",
				'type'  => 'numeric',
				//'integer'  => false,
				'value' => 100,
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_total_percent',
				),
			),
			'product_design_fee' => array(
				'label' => 'Phí thiết kế',
				'desc'  => "Đơn vị là % của tổng mức đầu tư",
				'type'  => 'numeric',
				'integer'  => false,
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_design_fee',
				),
			),
			'product_design_cost' => array(
				'label' => 'Giá thiết kế chung',
				'desc'  => 'Đơn vị k/m2',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_design_cost',
				),
			),
			'product_sale_off' => array(
				'label' => 'Sale off',
				'desc'  => '',
				'type'  => 'text',
				'value' => '',
				'fw-storage' => array(
					'type' => 'wp-option',
					'wp-option' => 'product_sale_off',
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
