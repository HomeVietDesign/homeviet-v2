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
		'title'   => 'Cài đặt nâng cao',
		'type'    => 'box',
        'options' => array(
        	'apply_menu' => array(
				'label' => 'Menu hiển thị',
				'desc'  => '',
				'type'  => 'multi-select',
				'population' => 'taxonomy',
				'source' => 'nav_menu',
				'limit' => 1
			),
			'display_menu' => array(
				'label' => 'Hiển thị menu?',
				'desc'  => '',
				'value'  => 'yes',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			),
		),
    ),

);
