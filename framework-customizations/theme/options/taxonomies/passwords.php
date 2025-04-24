<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * Framework options
 *
 * @var array $options Fill this array with options to generate framework settings form in backend
 */

$options = array(
	'province' => array(
		'label' => 'Tỉnh thành',
		'desc'  => '',
		'type'  => 'multi-select',
		'population' => 'taxonomy',
		'source' => 'province',
		'limit' => 1,
		'fw-storage' => array(
			'type' => 'term-meta',
			'term-meta' => 'province',
		),
	),
	
);
