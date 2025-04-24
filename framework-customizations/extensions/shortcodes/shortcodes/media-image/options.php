<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$all_sizes = wp_get_registered_image_subsizes();
$medium_large = $all_sizes['medium_large'];
unset($all_sizes['medium_large']);
//debug_log($all_sizes);
$sizes = [];
foreach ($all_sizes as $key => $value) {
	$size_name = preg_replace('/[-|_]+/', ' ', $key);
	$width = absint( $value['width'] ); $width = ($width===0)?'Auto':$width;
	$height = absint( $value['height'] ); $height = ($height===0)?'Auto':$height;

	$sizes[$key] = $size_name.' - '.$width.' x '.$height;
	if($key=='large') {
		$width = absint( $medium_large['width'] ); $width = ($width===0)?'Auto':$width;
		$height = absint( $medium_large['height'] ); $height = ($height===0)?'Auto':$height;
		$sizes['medium_large'] = 'medium large - '.$width.' x '.$height;
	}
}
$sizes['full'] = 'Ảnh gốc';

$options = array(
	'gimage'            => array(
		'type' => 'group',
		'options' => array(
			'image' => array(
				'type'  => 'upload',
				'label' => __( 'Choose Image', 'fw' ),
				'desc'  => __( 'Either upload a new, or choose an existing image from your media library', 'fw' )
			),
			'alt'  => array(
				'type'  => 'text',
				'label' => __( 'Alt', 'fw' ),
				'value' => ''
			),
			'size' => array(
				'type' => 'select',
				'value' => 'large',
				'label' => 'Kích thước',
				'choices' => $sizes
			),
		)
	),
	'image-link-group' => array(
		'type'    => 'group',
		'options' => array(
			'link'   => array(
				'type'  => 'text',
				'label' => __( 'Image Link', 'fw' ),
				'desc'  => __( 'Where should your image link to?', 'fw' )
			),
			'target' => array(
				'type'         => 'switch',
				'label'        => __( 'Open Link in New Window', 'fw' ),
				'desc'         => __( 'Select here if you want to open the linked page in a new window', 'fw' ),
				'right-choice' => array(
					'value' => '_blank',
					'label' => __( 'Yes', 'fw' ),
				),
				'left-choice'  => array(
					'value' => '_self',
					'label' => __( 'No', 'fw' ),
				),
			),
		)
	)
);

