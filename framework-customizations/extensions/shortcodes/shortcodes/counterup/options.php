<?php if (!defined('FW')) {
	die('Forbidden');
}

$options = array(
	'items' => array(
		'type' => 'addable-popup',
		'label' => 'Danh sách',
		'template' => '{{=title}}',
		'popup-title' => 'Phần tử',
		'size' => 'small', // small, medium, large
		'limit' => 0, // limit the number of popup`s that can be added
		'add-button-text' => 'Thêm',
		'sortable' => true,
		'popup-options' => array(
			'title' => array(
				'label' => 'Tiêu đề',
				'type' => 'text',
				'desc' => 'Tiêu đề phần tử trong danh sách',
			),
			'numbers' => array(
				'label' => 'Số lượng',
				'type' => 'number',
			),
			
		),
	),
);
