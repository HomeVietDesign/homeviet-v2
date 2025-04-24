<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	'quotes' => array(
		'context' => 'advanced',
		'title'   => 'Báo giá',
		'type'    => 'box',
        'options' => array(
        	'_quotes' => array(
				'type'  => 'addable-popup',
			    'value' => [],
			    'label' => 'Báo giá cho khách hàng',
			    'size' => 'large',
			    'popup-options' => array(
			        'title' => array(
			        	'label' => 'Tiêu đề',
			        	'type' => 'text'
			        ),
			        'customer' => array(
			        	'label' => 'Khách hàng',
						'desc'  => '',
						'type'  => 'multi-select',
						'population' => 'taxonomy',
						'source' => 'passwords',
						'limit' => 1
			        ),
			        // 'drawing_url' => array(
			        // 	'label' => 'URL bản vẽ',
			        // 	'type' => 'text'
			        // ),
			        'document_url' => array(
			        	'label' => 'URL dự toán',
			        	'type' => 'text'
			        ),
			        'requirement' => array(
			        	'label' => 'Phương án',
			        	'type' => 'wp-editor',
			        	'size' => 'large',
						'editor_height' => '400',
			        ),
			    ),
			    'template' => '{{- title }}', // box title
			    'limit' => 0, // limit the number of boxes that can be added
			    'add-button-text' => 'Thêm',
			    'sortable' => true,
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_quotes',
				),
			),
			
		),
	),
	'info'=>array(
		'context' => 'advanced',
		'title'   => 'Cài đặt nâng cao',
		'type'    => 'box',
        'options' => array(
			'_phone_number' => array(
				'label' => 'Số điện thoại',
				'type' => 'text',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_phone_number',
				),
			),
			'_external_url' => array(
				'label' => 'Link nút Xem',
				'type' => 'text',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_external_url',
				),
			),
			'_best' => array(
				'label' => 'Là nhà thầu tốt?',
				'desc'  => '',
				'value'  => 'false',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'true',
			        'label' => 'Đúng',
			    ),
			    'right-choice' => array(
			        'value' => 'false',
			        'label' => 'Không',
			    ),
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_best',
				),
			),
		),
    ),

);
