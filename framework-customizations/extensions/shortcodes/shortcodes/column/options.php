<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	array(
		'type' => 'tab',
		'title' => 'Cài đặt chung',
		'options' => array(
			// 'text_color' => array(
			// 	'type'  => 'color-picker',
			// 	'label' => 'Màu văn bản',
			// ),
			// 'text_link_color' => array(
			// 	'type'  => 'color-picker',
			// 	'label' => 'Màu văn bản link',
			// ),
			'html_class' => array(
				'label' => esc_html__('Html Class', 'fw'),
				'desc' => esc_html__('Add Html Class', 'fw'),
				'type' => 'text',
			),
			// 'no_padding' => array(
			// 	'value' => false,
			// 	'label' => esc_html__('No padding', 'fw'),
			// 	'type' => 'checkbox',
			// 	'text' => esc_html__('If selected, there will be no left and right spacing.', 'fw'),
			// ),

		)
	),
	array(
		'type' => 'tab',
		'title' => 'Định dạng cột',
		'options' => array(
			'background' => array(
				'type' => 'group',
				'options' => array(
					'background' => array(
						'type'  => 'html',
						'label' => 'BACKGROUND',
						'html'  => 'HIỆU CHỈNH MÀU NỀN, ẢNH NỀN',
					),
					'background_color' => array(
						'label' => 'Màu nền',
						'desc'  => '',
						'type'  => 'rgba-color-picker',
					),
					'background_image' => array(
						'label'   => 'Ảnh nền',
						'desc'    => '',
						'type'    => 'background-image',
						'choices' => array(//	in future may will set predefined images
						)
					),
					'background_size' => array(
						'label'   => 'Kiểu ảnh nền',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Default',
							'auto' => 'Auto',
							'contain' => 'Contain',
							'cover' => 'Cover',
						)
					),
					'background_position' => array(
						'label'   => 'Vị trí',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Mặc định',
							'left top' => 'Trên bên trái',
							'center top' => 'Bên trên',
							'right top' => 'Trên bên phải',
							'center center' => 'Chính giữa',
							'left bottom' => 'Dưới bên trái',
							'center bottom' => 'Bên dưới',
							'right bottom' => 'Dưới bên phải',
						)
					),
					'background_repeat' => array(
						'label'   => 'Lặp lại',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Mặc định lặp',
							'repeat-x' => 'Lặp chiều ngang',
							'repeat-y' => 'Lặp chiều dọc',
							'no-repeat' => 'Không lặp'
						)
					),
					// 'background_show' => array(
					// 	'type'  => 'switch',
					// 	'value' => 'full',
					// 	'label' => __('Show Background', 'fw'),
					// 	'left-choice' => array(
					// 		'value' => 'full',
					// 		'label' => __('Full', 'fw'),
					// 	),
					// 	'right-choice' => array(
					// 		'value' => 'inner',
					// 		'label' => __('Inner', 'fw'),
					// 	),
					// ),
				),
			),
			'border' => array(
				'type' => 'group',
				'options' => array(
					'border' => array(
						'type'  => 'html',
						'label' => 'BORDER',
						'html'  => 'HIỆU CHỈNH ĐƯỜNG VIỀN CỘT',
					),
					'border_width' => array(
						'type'  => 'numeric',
						'value'  => 0,
						'label' => 'Kích thước viền',
						// 'integer' => false,
						// 'decimals' => 1,
					),
					'border_color' => array(
						'type'  => 'rgba-color-picker',
						'label' => 'Màu viền',
					),
					'border_style' => array(
						'type'  => 'select',
						'label' => 'Kiểu viền viền',
						'choices' => [
							//'default' => 'Mặc định',
							'solid' => 'Nét liền',
							'dotted' => 'Nét chấm chấm',
							'dashed' => 'Nét gạch quãng',
							'double' => 'Nét liền đôi',
						]
					),
				)
			),
			'margin' => array(
				'type' => 'group',
				'options' => array(
					'margin' => array(
						'type'  => 'html',
						'label' => 'MARGIN',
						'html'  => 'HIỆU CHỈNH KHOẢNG TRỐNG GIỮA BIÊN CỘT VÀ PHẦN TỬ NGOÀI CỘT',
					),
					'margin_top' => array(
						'label'        => 'Trên',
						'type'         => 'text',
					),
					'margin_right' => array(
						'label' => 'Phải',
						'type'  => 'text',
					),
					'margin_bottom' => array(
						'label' => 'Dưới',
						'type'  => 'text',
					),
					'margin_left' => array(
						'label' => 'Trái',
						'type'  => 'text',
					)
				)
			),
			'padding' => array(
				'type' => 'group',
				'options' => array(
					'padding' => array(
						'type'  => 'html',
						'label' => 'PADDING',
						'html'  => 'HIỆU CHỈNH KHOẢNG TRỐNG GIỮA BIÊN CỘT VÀ PHẦN TỬ BÊN TRONG CỘT',
					),
					'padding_top' => array(
						'label'        => 'Trên',
						'type'         => 'text',
					),
					'padding_right' => array(
						'label' => 'Phải',
						'type'  => 'text',
					),
					'padding_bottom' => array(
						'label' => 'Dưới',
						'type'  => 'text',
					),
					'padding_left' => array(
						'label' => 'Trái',
						'type'  => 'text',
					)
				)
			),
		)
	),
	array(
		'type' => 'tab',
		'title' => 'Định dạng trong cột',
		'options' => array(
			'inner_background' => array(
				'type' => 'group',
				'options' => array(
					'inner_background' => array(
						'type'  => 'html',
						'label' => 'BACKGROUND',
						'html'  => 'HIỆU CHỈNH MÀU NỀN, ẢNH NỀN',
					),
					'inner_background_color' => array(
						'label' => 'Màu nền',
						'desc'  => '',
						'type'  => 'rgba-color-picker',
					),
					'inner_background_image' => array(
						'label'   => 'Ảnh nền',
						'desc'    => '',
						'type'    => 'background-image',
						'choices' => array(//	in future may will set predefined images
						)
					),
					'inner_background_size' => array(
						'label'   => 'Kiểu ảnh nền',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Default',
							'auto' => 'Auto',
							'contain' => 'Contain',
							'cover' => 'Cover',
						)
					),
					'inner_background_position' => array(
						'label'   => 'Vị trí',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Mặc định',
							'left top' => 'Trên bên trái',
							'center top' => 'Bên trên',
							'right top' => 'Trên bên phải',
							'center center' => 'Chính giữa',
							'left bottom' => 'Dưới bên trái',
							'center bottom' => 'Bên dưới',
							'right bottom' => 'Dưới bên phải',
						)
					),
					'inner_background_repeat' => array(
						'label'   => 'Lặp lại',
						'desc'    => '',
						'type'    => 'select',
						'choices' => array(
							'default' => 'Mặc định lặp',
							'repeat-x' => 'Lặp chiều ngang',
							'repeat-y' => 'Lặp chiều dọc',
							'no-repeat' => 'Không lặp'
						)
					),
					// 'inner_background_show' => array(
					// 	'type'  => 'switch',
					// 	'value' => 'full',
					// 	'label' => __('Show Background', 'fw'),
					// 	'left-choice' => array(
					// 		'value' => 'full',
					// 		'label' => __('Full', 'fw'),
					// 	),
					// 	'right-choice' => array(
					// 		'value' => 'inner',
					// 		'label' => __('Inner', 'fw'),
					// 	),
					// ),
				),
			),
			'inner_border' => array(
				'type' => 'group',
				'options' => array(
					'inner_border' => array(
						'type'  => 'html',
						'label' => 'BORDER',
						'html'  => 'HIỆU CHỈNH ĐƯỜNG VIỀN KHỐI',
					),
					'inner_border_width' => array(
						'type'  => 'numeric',
						'value'  => 0,
						'label' => 'Kích thước viền',
						// 'integer' => false,
						// 'decimals' => 1,
					),
					'inner_border_color' => array(
						'type'  => 'rgba-color-picker',
						'label' => 'Màu viền',
					),
					'inner_border_style' => array(
						'type'  => 'select',
						'label' => 'Kiểu viền viền',
						'choices' => [
							//'default' => 'Mặc định',
							'solid' => 'Nét liền',
							'dotted' => 'Nét chấm chấm',
							'dashed' => 'Nét gạch quãng',
							'double' => 'Nét liền đôi',
						]
					),
				)
			),
			'inner_margin' => array(
				'type' => 'group',
				'options' => array(
					'inner_margin' => array(
						'type'  => 'html',
						'label' => 'MARGIN',
						'html'  => 'HIỆU CHỈNH KHOẢNG TRỐNG GIỮA BIÊN VÀ PHẦN TỬ BÊN NGOÀI',
					),
					'inner_margin_top' => array(
						'label'        => 'Trên',
						'type'         => 'text',
					),
					'inner_margin_right' => array(
						'label' => 'Phải',
						'type'  => 'text',
					),
					'inner_margin_bottom' => array(
						'label' => 'Dưới',
						'type'  => 'text',
					),
					'inner_margin_left' => array(
						'label' => 'Trái',
						'type'  => 'text',
					)
				)
			),
			'inner_padding' => array(
				'type' => 'group',
				'options' => array(
					'inner_padding' => array(
						'type'  => 'html',
						'label' => 'PADDING',
						'html'  => 'HIỆU CHỈNH KHOẢNG TRỐNG GIỮA BIÊN VÀ PHẦN TỬ BÊN TRONG',
					),
					'inner_padding_top' => array(
						'label'        => 'Trên',
						'type'         => 'text',
					),
					'inner_padding_right' => array(
						'label' => 'Phải',
						'type'  => 'text',
					),
					'inner_padding_bottom' => array(
						'label' => 'Dưới',
						'type'  => 'text',
					),
					'inner_padding_left' => array(
						'label' => 'Trái',
						'type'  => 'text',
					)
				)
			),
		)
	),
);
