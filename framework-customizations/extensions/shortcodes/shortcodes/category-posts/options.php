<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	array(
		'type' => 'tab',
		'title' => 'Bài viết',
		'options' => array(
			'number' => array(
				'label' => 'Số bài viết hiển thị',
				'desc' => '',
				'value' => '8',
				'type' => 'text',
			),
			'category' => array(
				'label' => 'Chuyên mục hiển thị',
				'desc' => '',
				'type' => 'multi-select',
				'population' => 'taxonomy',
				'source' => 'category',
				'limit' => 1,
			),
			'category_exclude' => array(
				'label' => 'Chuyên mục loại trừ',
				'desc' => '',
				'type' => 'multi-select',
				'population' => 'taxonomy',
				'source' => 'category',
				//'limit' => 1,
			),
			'location' => array(
				'label' => 'Địa điểm công trình',
				'desc' => '',
				'type' => 'multi-select',
				'population' => 'taxonomy',
				'source' => 'location',
				'limit' => 1,
			),
		)
	),
	array(
		'type' => 'tab',
		'title' => 'Cài đặt',
		'options' => array(
			'title' => array(
				'label' => esc_html__('Title', 'fw'),
				'desc' => '',
				'type' => 'text',
			),
			'html_id' => array(
				'label' => esc_html__('Html ID', 'fw'),
				'desc' => esc_html__('Add Html ID', 'fw'),
				'type' => 'text',
			),
			'html_class' => array(
				'label' => esc_html__('Html Class', 'fw'),
				'desc' => esc_html__('Add Html Class', 'fw'),
				'type' => 'text',
			),
		)
	),
);
