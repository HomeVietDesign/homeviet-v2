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
			'_ref' => array(
				'type' => 'text',
				'label' => 'Mã tân cổ',
			),
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
				'files_ext' => array( 'png', 'jpg', 'jpeg', 'webp' ),
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
		'title'   => 'Kích thước công trình',
		'type'    => 'box',
        'options' => array(
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
			'_area_1' => array(
				'type' => 'numeric',
				'integer' => false,
				'decimals' => 1,
				'label' => 'Diện tích 1 sàn(m2)',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_area_1'
				)
			),
			'_floors' => array(
				'type' => 'numeric',
				'integer' => false,
				'decimals' => 1,
				'label' => 'Số tầng cao',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_floors'
				)
			),
        ),
    ),
    array(
		'context' => 'side',
		'title'   => 'Giá trị công trình',
		'type'    => 'box',
        'options' => array(
			'_price' => array(
				'type' => 'numeric',
				'integer' => true,
				'negative' => false,
				'size' => 'full',
				'label' => 'Đơn giá đầu tư',
				'desc'  => 'Đơn vị k/m2',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_price'
				)
			),
			'_use_general_price' => array(
				'label' => 'Dùng giá đầu tư chung?',
				'desc'  => '',
				'value'  => 'yes',
				'type'  => 'switch',
				'left-choice' => array(
			        'value' => 'no',
			        'label' => 'Không',
			    ),
			    'right-choice' => array(
			        'value' => 'yes',
			        'label' => 'Đúng',
			    ),
			    'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_use_general_price'
				)
			),
			'_total_factor' => array(
				'type'  => 'numeric',
				'integer'  => false,
				'negative' => false,
				'value' => 1,
				'label' => 'Hệ số tổng',
				'desc'  => 'Hệ số của tổng mức đầu tư',
				'fw-storage' => array(
					'type' => 'post-meta',
					'post-meta' => '_total_factor'
				)
			),
			'note' => array(
				'type' => 'html',
				'label' => 'Công thức tính',
				'html' => 'Tổng đầu tư = Diện tích 1 sàn x Số tầng cao x Đơn giá x Hệ số tổng'
			)
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
