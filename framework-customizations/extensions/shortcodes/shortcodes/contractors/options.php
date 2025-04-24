<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(

	'number' => array(
		'label' => 'Số nhà thầu hiển thị',
		'desc' => '',
		'value' => '8',
		'type' => 'text',
	),
	'contractor_cat' => array(
		'label' => 'Hạng mục thầu',
		'desc' => '',
		'type' => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'contractor_cat',
		'limit' => 1,
	),
	'contractor_rating' => array(
		'label' => 'Nhóm đánh giá',
		'desc' => '',
		'type' => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'contractor_rating',
		'limit' => 1,
	),

);
