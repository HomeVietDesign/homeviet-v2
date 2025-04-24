<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	'number' => array(
		'label' => 'Số Nhà thầu hiển thị',
		'desc' => 'Số lượng Nhà thầu hiển thị trên một phần trang.',
		'value' => '8',
		'type' => 'text',
	),
	'contractor_cat' => array(
		'label' => 'Hạng mục',
		'desc' => '',
		'type' => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'contractor_cat',
		'limit' => 1,
	),
	
);
