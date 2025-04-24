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
	'images' => array(
		'type' => 'multi-upload',
		'label' => 'Danh sách ảnh',
		'images_only' => true,
		'files_ext' => array( 'png', 'jpg', 'jpeg' ),
	),
	'per' => array(
		'label' => 'Số lượng hiển thị',
		'desc' => 'Số phần tử hiển thị trên 1 phân trang',
		'value' => '0',
		'type' => 'text',
	),
	'size' => array(
		'type' => 'select',
		'label' => 'Kích thước hiển thị',
		'value' => 'large',
		'choices' => $sizes
	),
	'layout' => array(
		'type' => 'select',
		'label' => 'Kiểu cách',
		'value' => 'grid',
		'choices' => [
			'grid' => 'Thẳng hàng',
			'masonry' => 'Linh động',
		]
	),
	'show_desktop' => array(
		'label' => 'Số cột trên desktop',
		'desc' => '',
		'value' => 'col-md-6',
		'choices' => [
			'col-md-12' => '1',
			'col-md-6' => '2',
			'col-md-4' => '3',
			'col-md-3' => '4',
			'col-md-2' => '6'
		],
		'type' => 'select',
	),
	'show_tablet' => array(
		'label' => 'Số cột trên tablet',
		'desc' => '',
		'value' => 'col-sm-6',
		'choices' => [
			'col-sm-12' => '1',
			'col-sm-6' => '2',
			'col-sm-4' => '3',
			'col-sm-3' => '4',
			'col-sm-2' => '6'
		],
		'type' => 'select',
	),
	'show_mobile' => array(
		'label' => 'Số cột trên mobile',
		'desc' => '',
		'value' => 'col-xs-12',
		'choices' => [
			'col-xs-12' => '1',
			'col-xs-6' => '2',
			'col-xs-4' => '3',
			'col-xs-3' => '4',
			'col-xs-2' => '6'
		],
		'type' => 'select',
	),
);
