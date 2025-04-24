<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	array(
		'context' => 'advanced',
		'title'   => 'Cài đặt',
		'type'    => 'box',
        'options' => array(
        	'is_general' => array(
				'label' => 'Là nhóm chung?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Đúng',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Sai',
			    ),
			),
			
		),
    ),

);
