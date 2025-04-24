<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	array(
		'type' => 'tab',
		'title' => __('General', 'fw'),
		'options' => array(
			'boxies' => array(
				'type' => 'addable-popup',
				'label' => __('Boxies'),
				'width'  => 'full',
				'size' => 'small', // small, medium, large
				'template'      => '{{=title}}',
				'popup-title' => 'Box text',
				'popup-options' => array(
					'title' => array(
						'label' => __( 'Title' ),
						'desc'  => '',
						'type'  => 'text'
					),
					'text' => array(
						'type'   => 'wp-editor',
						'label'  => __( 'Content', 'fw' ),
						'desc'   => __( 'Enter some content for this texblock', 'fw' ),
						'size'	 => 'large',
						'editor_height' => '400'
					),
					'html_class' => array(
						'label' => __( 'HTML Class' ),
						'desc'  => '',
						'type'  => 'text'
					),
				),
				'sortable' => true,

			),
			'box_radius' => array(
				'label' => __('Box radius', 'fw'),
				'type'  => 'text',
			),
			'box_width' => array(
				'label' => __('Box width', 'fw'),
				'type'  => 'text',
				'value' => '250px'
			),
			'box_height' => array(
				'label' => __('Box height', 'fw'),
				'type'  => 'text',
				'value' => '250px'
			),
			'v_align' => array(
				'label' => __('Vertical align', 'fw'),
				'type'  => 'select',
				'choices' => array(
					'top' => 'Top',
					'middle' => 'Middle',
					'bottom' => 'Bottom',
				),
				'value' => 'top'
			),
			'html_id' => array(
				'label' => __('HTML id', 'fw'),
				'desc'  => __('Thuộc tính id của thẻ html', 'fw'),
				'type'  => 'text',
			)
		)
	),
	array(
		'type' => 'tab',
		'title' => __('Background', 'fw'),
		'options' => array(
			'background_color' => array(
				'label' => __('Background Color', 'fw'),
				'desc'  => __('Please select the background color', 'fw'),
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
					'default' => 'Mặc định',
					'auto' => 'Nguyên bản',
					'contain' => 'Cả khung',
					'cover' => 'Vừa khung',
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
			)
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
	array(
		'type' => 'tab',
		'title' => __('Box shadow'),
		'options' => array(
			'box_shadow_h' => array(
				'type'  => 'text',
				'label' => __('Text shadow left(px)', 'fw'),
			),
			'box_shadow_v' => array(
				'type'  => 'text',
				'label' => __('Text shadow top(px)', 'fw'),
			),
			'box_shadow_br' => array(
				'type'  => 'text',
				'label' => __('Text shadow blur radius(px)', 'fw'),
			),
			'box_shadow_color' => array(
				'type'  => 'rgba-color-picker',
				'label' => __('Text shadow color', 'fw'),
			)
		)
	)
);
