<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'url'    => array(
		'type'  => 'text',
		'label' => __( 'Insert Video URL', 'fw' ),
		'desc'  => __( 'Insert Video URL to embed this video', 'fw' )
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
	),
	'autoplay' => array(
		'type'  => 'switch',
		'value' => 0,
		'label' => 'Tự động chạy',
		'left-choice' => array(
			'value' => 0,
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 1,
			'label' => 'Có',
		),
	),
	'controls' => array(
		'type'  => 'switch',
		'value' => 0,
		'label' => 'Thanh điều khiển',
		'left-choice' => array(
			'value' => 0,
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 1,
			'label' => 'Có',
		),
	),
	'loop' => array(
		'type'  => 'switch',
		'value' => 0,
		'label' => 'Chạy liên tục',
		'left-choice' => array(
			'value' => 0,
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 1,
			'label' => 'Có',
		),
	),
	'start'    => array(
		'type'  => 'number',
		'label' => 'Bắt đầu tại',
		'desc'  => 'Tham số này khiến trình phát bắt đầu phát video ở số giây đã cho từ đầu video. Giá trị thông số là một số nguyên dương.'
	),
	'end'    => array(
		'type'  => 'text',
		'label' => 'Kết thúc tại',
		'desc'  => 'Tham số này chỉ định thời gian, được tính bằng giây kể từ khi bắt đầu video, khi trình phát dừng phát video. Giá trị thông số là một số nguyên dương. Lưu ý, thời gian được đo từ đầu video chứ không phải từ giá trị của thông số trình phát start hoặc thông số startSeconds (dùng trong các hàm API Trình phát YouTube) để tải hoặc xếp hàng đợi video.'
	),
	'fs' => array(
		'type'  => 'switch',
		'value' => 0,
		'label' => 'Nút toàn màn hình',
		'left-choice' => array(
			'value' => 0,
			'label' => 'Không',
		),
		'right-choice' => array(
			'value' => 1,
			'label' => 'Có',
		),
	),
	
);
