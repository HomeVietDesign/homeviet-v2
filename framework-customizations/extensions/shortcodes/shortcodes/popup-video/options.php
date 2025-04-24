<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$image_sizes = get_intermediate_image_sizes();
//debug_log($image_sizes);
$sizes = [];
foreach ($image_sizes as $key => $value) {
	$sizes[$value] = $value;
}
$sizes['full'] = 'Ảnh gốc';

$options = array(
	'video'    => array(
		'type'  => 'upload',
		'value' => array(
			/*
			'attachment_id' => '9',
			'url' => '//site.com/wp-content/uploads/2014/02/whatever.jpg'
			*/
			// if value is set in code, it is not considered and not used
			// because there is no sense to set hardcode attachment_id
		),
		'label' => __('Video', 'fw'),
		'desc'  => __('Tải video lên', 'fw'),
		'images_only' => false,
		/**
		* An array with allowed files extensions what will filter the media library and the upload files.
		*/
		'files_ext' => array( 'mp4', 'avi', 'webm', 'vob' ),
		/**
		* An array with extra mime types that is not in the default array with mime types from the javascript Plupload library.
		* The format is: array( '<mime-type>, <ext1> <ext2> <ext2>' ).
		* For example: you set rar format to filter, but the filter ignore it , than you must set
		* the array with the next structure array( '.rar, rar' ) and it will solve the problem.
		*/
		//'extra_mime_types' => array( 'audio/x-aiff, aif aiff' )
	),
	'thumbnail' => array(
		'type' => 'group',
		'options' => array(
			'image' => array(
				'type'  => 'upload',
				'images_only' => true,
				'label' => __( 'Ảnh thumbnail', 'fw' ),
			),
			// 'alt'  => array(
			// 	'type'  => 'text',
			// 	'label' => __( 'Alt thumbnail', 'fw' ),
			// 	'value' => ''
			// ),
			'size' => array(
				'type' => 'select',
				'value' => 'large',
				'label' => 'Kích thước thumbnail',
				'choices' => $sizes
			),
		)
	),
	
	'ratio' => array(
		'type'  => 'select',
		'value' => '16x9',
		'attr'  => array( 'class' => 'custom-class', 'data-foo' => 'bar' ),
		'label' => __('Ratio', 'fw'),
		'choices' => array(
			'16x9' => __('16 x 9', 'fw'),
			'1x1' => __('1 x 1', 'fw'),
			'21x9' => __('21 x 9', 'fw'),
			'4x3' => __('4 x 3', 'fw')
		)
	)
);
