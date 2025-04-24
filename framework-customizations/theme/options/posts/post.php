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
		'title'   => 'Đặc tính',
		'type'    => 'box',
        'options' => array(
        	'_featured' => array(
				'label' => 'Nổi bật?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'right-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_featured',
				),
			),
        	'_allow_order' => array(
				'label' => 'Nút Chọn mẫu?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'no',
			        'label' => 'Tắt',
			    ),
			    'right-choice' => array(
			        'value' => 'yes',
			        'label' => 'Bật',
			    ),
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_allow_order',
				),
			),
			/*
			'_get_premium' => array(
				'label' => 'Nút Tư vấn VIP?',
				'desc'  => 'Khi nút này được bật sẽ thay thế cho nút Chọn mẫu',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'no',
			        'label' => 'Tắt',
			    ),
			    'right-choice' => array(
			        'value' => 'yes',
			        'label' => 'Bật',
			    ),
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_get_premium',
				),
			),
			*/
		),
    ),
    array(
    	'context' => 'advanced',
		'title'   => 'Các ảnh slide',
		'type'    => 'box',
        'options' => array(
        	'_images' => array(
				'type' => 'multi-upload',
				'label' => 'Danh sách ảnh',
				'images_only' => true,
				'files_ext' => array( 'png', 'jpg', 'jpeg' ),
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_images'
				)
			),
        )
    ),
    array(
    	'context' => 'advanced',
		'title'   => 'Mô tả công năng',
		'type'    => 'box',
        'options' => array(
        	'_functions' => array(
				'label' => 'Tóm tắt',
				'desc'  => '',
				'type'  => 'wp-editor',
				'value' => '',
				'size' => 'large',
				'editor_height' => '200',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_functions'
				)
			),
        )
    ),
	array(
		'context' => 'side',
		'title'   => 'Thông tin công trình',
		'type'    => 'box',
        'options' => array(
        	// '_location' => array(
			// 	'type' => 'text',
			// 	'label' => 'Địa điểm',
			// 	'fw-storage' => array(
			// 		'type' => 'post-meta',
			// 		'post-meta' => '_location'
			// 	)
			// ),
			'_breadth' => array(
				'type' => 'text',
				'label' => 'Rộng mặt tiền(m)',
				// 'integer' => false,
				// 'decimals' => 1,
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_breadth'
				)
			),
			'_length' => array(
				'type' => 'text',
				'label' => 'Chiều sâu(m)',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_length'
				)
			),
			'_area' => array(
				'type' => 'text',
				'label' => 'Diện tích(m2)',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_area'
				)
			),
			'_total_amount' => array(
				'type'  => 'numeric',
				'integer'  => false,
				'value' => '',
				'label' => 'Tổng mức đầu tư (tỷ)',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_total_amount'
				)
			),
			'_design_fee' => array(
				'type'  => 'numeric',
				'integer'  => false,
				'value' => '',
				'label' => 'Phí thiết kế',
				'desc'  => 'Đơn vị là % của tổng mức đầu tư',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_design_fee'
				)
			),
			'_show_general_design_fee' => array(
				'label' => 'Dùng phí TK chung?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_show_general_design_fee'
				)
			),
			'_design_cost' => array(
				'type' => 'text',
				'label' => 'Giá thiết kế',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_design_cost'
				)
			),
			'_show_general_design_cost' => array(
				'label' => 'Dùng giá TK chung?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_show_general_design_cost'
				)
			),
			'_sale_off' => array(
				'type' => 'text',
				'label' => 'Giảm giá',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_sale_off'
				)
			),
			'_show_general_sale_off' => array(
				'label' => 'Dùng giảm giá chung?',
				'desc'  => '',
				'value'  => 'no',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
			    'right-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_show_general_sale_off'
				)
			),
        ),
    ),
	array(
		'context' => 'side',
		'title'   => 'VIDEO',
		'type'    => 'box',
        'options' => array(
        	'video' => array(
				'type'  => 'upload',
				'value' => '',
				'label' => 'Tải lên Video',
				'desc' => 'Sẽ ưu tiên dùng Video URL bên dưới trước nếu nó có.',
				'images_only' => false,
				'files_ext' => array( 'mp4' ),

			),
			'video_url' => array(
				'type'  => 'text',
				'value' => '',
				'label' => 'Video URL',
				'desc' => 'Ưu tiên dùng trước.',
			),
			'video_youtube' => array(
				'type'  => 'oembed',
				'value' => '',
				'label' => 'URL YT Video',
				'preview' => [
					'keep_ratio' => true
				]
			),
        ),
    ),
    array(
		'context' => 'advanced',
		'title'   => 'Cài đặt',
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
			'_footer_content' => array(
				'label' => 'Hiện nội dung chân trang?',
				'desc'  => 'Nội dung cuối chi tiết bài viết công trình được cài đặt ở Theme Settings.',
				'value'  => 'yes',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'right-choice' => array(
			        'value' => 'yes',
			        'label' => 'Có',
			    ),
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_footer_content',
				),
			),
		),
    ),
);
