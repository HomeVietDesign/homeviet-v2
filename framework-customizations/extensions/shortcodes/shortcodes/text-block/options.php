<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	array(
		'type' => 'tab',
		'title' => __('General', 'fw'),
		'options' => array(
			'text' => array(
				'type'   => 'wp-editor',
				'label'  => __( 'Content', 'fw' ),
				'desc'   => __( 'Enter some content for this texblock', 'fw' ),
				'size'	 => 'large',
				'editor_height' => '600'
			)
		)
	),
	array(
		'type' => 'tab',
		'title' => __('Advanced', 'fw'),
		'options' => array(
			'text_color' => array(
				'type'  => 'color-picker',
				'label' => 'Màu văn bản',
			),
			'text_link_color' => array(
				'type'  => 'color-picker',
				'label' => 'Màu văn bản link',
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
