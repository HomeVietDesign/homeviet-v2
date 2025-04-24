<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(

	'video' => array(
		'type'  => 'upload',
		'value' => '',
		'label' => 'Tải lên Video',
		//'desc' => 'Video tải lên có dung lượng tối đa 100MB.',
		'images_only' => false,
		'files_ext' => array( 'mp4' ),
	),
	'video_url'    => array(
		'type'  => 'text',
		'label' => 'Video URL(mp4)',
		'desc'  => 'Nếu trường này có thì sẽ được ưu tiên dùng thay cho video tải lên ở trên.'
	),
	'thumbnail' => array(
		'type'  => 'upload',
		'label' => 'Ảnh đại diện',
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
	/*
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
	*/
);
