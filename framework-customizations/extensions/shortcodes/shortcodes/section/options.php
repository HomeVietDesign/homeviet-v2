<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	array(
		'type' => 'tab',
		'title' => __('General', 'fw'),
		'options' => array(
			'is_fullwidth' => array(
				'label'        => __('Full Width', 'fw'),
				'type'         => 'switch',
			),
			'left_right_padding' => array(
				'type'         => 'switch',
				'label'        => 'Khoảng lề?',
				'value'		   => 'yes',
				'right-choice' => array(
					'value' => 'yes',
					'label' => 'Có',
				),
				'left-choice'  => array(
					'value' => 'no',
					'label' => 'Không',
				),
			),
			'show_less' => array(
				'type'         => 'switch',
				'label'        => 'Hiển thị ẩn bớt?',
				'right-choice' => array(
					'value' => 'yes',
					'label' => 'Có',
				),
				'left-choice'  => array(
					'value' => 'no',
					'label' => 'Không',
				),
			),
			'show_row' => array(
				'label' => 'Số hàng hiển thị',
				'desc'  => 'Số hàng hiển thị ban đầu. Các hàng sau sẽ hiển thị khi nhấn nút xem thêm',
				'type'  => 'number',
			),
			'html_id' => array(
				'label' => __('HTML id', 'fw'),
				'desc'  => __('Thuộc tính id của thẻ html', 'fw'),
				'type'  => 'text',
			),
		)
	),
	array(
		'type' => 'tab',
		'title' => __('Background', 'fw'),
		'options' => array(
			'background_color' => array(
				'label' => __('Background Color', 'fw'),
				'desc'  => __('Please select the background color', 'fw'),
				'type'  => 'color-picker',
				'value' => ''
			),
			'background_overlay_color' => array(
				'label' => __('Background Overlay Color', 'fw'),
				'desc'  => __('Please select the background overlay color', 'fw'),
				'type'  => 'rgba-color-picker',
				'value' => ''
			),
			'background_image' => array(
				'label'   => __('Background Image', 'fw'),
				'desc'    => __('Please select the background image', 'fw'),
				'type'    => 'background-image',
				'choices' => array(//	in future may will set predefined images
				)
			),
			'background_size' => array(
				'label'   => __('Background Size', 'fw'),
				'desc'    => __('Please select the background size', 'fw'),
				'type'    => 'select',
				'choices' => array(
					'default' => 'Default',
					'auto' => 'Auto',
					'contain' => 'Contain',
					'cover' => 'Cover',
				)
			),
			'background_position' => array(
				'label'   => __('Background Position', 'fw'),
				'desc'    => __('Please select the background position', 'fw'),
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
				'label'   => __('Background Repeat', 'fw'),
				'desc'    => __('Please select the background repeat', 'fw'),
				'type'    => 'select',
				'choices' => array(
					'default' => 'Mặc định lặp',
					'repeat-x' => 'Lặp chiều ngang',
					'repeat-y' => 'Lặp chiều dọc',
					'no-repeat' => 'Không lặp'
				)
			),
			
		)
	),
	array(
		'type' => 'tab',
		'title' => __('Margin', 'fw'),
		'options' => array(
			'margin_top' => array(
				'label'        => __('Top', 'fw'),
				'type'         => 'text',
			),
			'margin_right' => array(
				'label' => __('Right', 'fw'),
				'type'  => 'text',
			),
			'margin_bottom' => array(
				'label' => __('Bottom', 'fw'),
				'type'  => 'text',
			),
			'margin_left' => array(
				'label' => __('Left', 'fw'),
				'type'  => 'text',
			)
		)
	),
	array(
		'type' => 'tab',
		'title' => __('Padding', 'fw'),
		'options' => array(
			'padding_top' => array(
				'label'        => __('Top', 'fw'),
				'type'         => 'text',
			),
			'padding_right' => array(
				'label' => __('Right', 'fw'),
				'type'  => 'text',
			),
			'padding_bottom' => array(
				'label' => __('Bottom', 'fw'),
				'type'  => 'text',
			),
			'padding_left' => array(
				'label' => __('Left', 'fw'),
				'type'  => 'text',
			)
		)
	),
	
);
