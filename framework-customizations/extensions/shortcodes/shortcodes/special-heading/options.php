<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	array(
		'type' => 'tab',
		'title' => __('General', 'fw'),
		'options' => array(
			'title'    => array(
				'type'  => 'text',
				'label' => 'Tiêu đề',
				//'desc'  => __( 'Write the heading title content', 'fw' ),
			),
			'heading' => array(
				'type'    => 'select',
				'label'   => 'Thẻ tiêu đề',
				'choices' => array(
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				)
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
		'title' => __('Advanced', 'fw'),
		'options' => array(
			'centered' => array(
				'type'    => 'switch',
				'label'   => 'Căn giữa',
			),
			'text_size' => array(
				'label' => 'Cỡ chữ (px)',
				'desc'  => 'Kích thước chữ, đơn vị px',
				'type'  => 'text',
			),
			'text_color' => array(
				'type'  => 'color-picker',
				'label' => 'Màu chữ',
			),
			'text_bold' => array(
				'type'    => 'switch',
				'label'   => 'In đậm',
			),
			'text_shadow_h' => array(
				'type'  => 'text',
				'label' => __('Text shadow left(px)', 'fw'),
			),
			'text_shadow_v' => array(
				'type'  => 'text',
				'label' => __('Text shadow top(px)', 'fw'),
			),
			'text_shadow_br' => array(
				'type'  => 'text',
				'label' => __('Text shadow blur radius(px)', 'fw'),
			),
			'text_shadow_color' => array(
				'type'  => 'rgba-color-picker',
				'label' => __('Text shadow color', 'fw'),
			)
		)
	),
);
