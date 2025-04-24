<?php
if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}


$options = array(
	'general' => array(
		'type' => 'tab',
		'title' => 'Cài đặt chung',
		'options' => array(
			'hotline' => array(
				'label' => 'Số hotline',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'hotline_label' => array(
				'label' => 'Nhãn hotline',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'zalo' => array(
				'label' => 'Số zalo',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'zalo_label' => array(
				'label' => 'Nhãn zalo',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'admin2_email' => array(
				'label' => 'Admin2 email address',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			/*
			'divider_popup_image' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt popup hình ảnh tự động mở</strong>',
				'size' => 'large',
			),
			'popup_image' => array(
				'label' => 'Ảnh popup',
				'desc'  => '',
				'type'  => 'upload',
				'images_only' => true,
			),
			'popup_target_url' => array(
				'label' => 'URL mục tiêu popup',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'popup_button_text' => array(
				'label' => 'Nhãn nút mở popup',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'popup_timeout' => array(
				'label' => 'Thời gian mở popup (giây)',
				'desc'  => '',
				'type'  => 'numeric',
				'value' => 120
			),
			*/
			'divider_popup_content' => array(
				'label' => '',
				'desc'  => '',
				'type'  => 'html',
				'html' => '<strong style="text-transform:uppercase;">Cài đặt popup nội dung tùy biến</strong>',
				'size' => 'large',
			),
			'popup_content' => array(
				'label' => 'Nội dung popup',
				'desc'  => '',
				'type'  => 'wp-editor',
				'value' => '',
				'size' => 'large',
				'editor_height' => '600'
			),
			'popup_content' => array(
				'label' => 'Nội dung popup',
				'desc'  => '',
				'type'  => 'wp-editor',
				'value' => '',
				'size' => 'large',
				'editor_height' => '600'
			),
			'popup_content_timeout' => array(
				'label' => 'Thời gian mở popup (giây)',
				'desc'  => '',
				'type'  => 'numeric',
				'value' => 120
			),
			'popup_content_button_text' => array(
				'label' => 'Nhãn nút mở popup',
				'desc'  => '',
				'type'  => 'text',
				'value' => ''
			),
			'thank_you_page' => [
				'type'  => 'multi-select',
				'population' => 'posts',
				'source' => 'page',
				'limit' => 1,
				'label' => 'Trang sự kiện gửi số điện thoại',
			],
		),
	),
);