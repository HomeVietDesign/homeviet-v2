<?php
namespace HomeViet;

class Custom_Types {

	private static $instance = null;

	private function __construct() {

		if ( is_admin() ) {
			add_action( 'admin_menu', [$this, '_admin_action_rename_menu'], 99 );
		}
		
		add_action( 'init', [$this, '_theme_action_register_taxonomy'], 10 );

		// đặt thứ tự hook là 10 để các plugin có thể nhận được các type tùy biến
		add_action( 'init', [$this, '_theme_action_register_custom_type_10'], 10 );

		// đặt thứ tự hook là 9999 để có thể đảm bảo lần chỉnh cuối nhất
		add_action( 'init', [$this, '_theme_action_change_object_content_labels'], 9999 );
	
	}

	/**
	 * Changes the labels value od the posts type: post from Post to Blog Post
	 * @internal
	 */
	public function _theme_action_change_object_content_labels() {
		global $wp_post_types, $wp_taxonomies;
		
		if( isset($wp_post_types['post']) && is_object( $wp_post_types['post']) && !empty($wp_post_types['post']->labels)) {
			$wp_post_types['post']->labels->name               = 'Sản phẩm';
			$wp_post_types['post']->labels->singular_name      = 'Sản phẩm';
			$wp_post_types['post']->labels->add_new            = 'Thêm Sản phẩm';
			$wp_post_types['post']->labels->add_new_item       = 'Thêm Sản phẩm mới';
			$wp_post_types['post']->labels->all_items          = 'Tất cả các Sản phẩm';
			$wp_post_types['post']->labels->edit_item          = 'Sửa Sản phẩm';
			$wp_post_types['post']->labels->name_admin_bar     = 'Sản phẩm';
			$wp_post_types['post']->labels->menu_name          = 'Sản phẩm';
			$wp_post_types['post']->labels->new_item           = 'Sản phẩm mới';
			$wp_post_types['post']->labels->not_found          = 'Không có Sản phẩm nào';
			$wp_post_types['post']->labels->not_found_in_trash = 'Không có Sản phẩm nào trong thùng rác';
			$wp_post_types['post']->labels->search_items       = 'Tìm Sản phẩm';
			$wp_post_types['post']->labels->view_item          = 'Xem Sản phẩm';
		}

		if( isset($wp_taxonomies['category']) && is_object( $wp_taxonomies['category']) && !empty($wp_taxonomies['category']->labels) ) {
			$wp_taxonomies['category']->label = 'Phân loại';
			$wp_taxonomies['category']->labels->name = 'Phân loại';
			$wp_taxonomies['category']->labels->singular_name = 'Phân loại';
			$wp_taxonomies['category']->labels->add_new = 'Thêm phân loại';
			$wp_taxonomies['category']->labels->add_new_item = 'Thêm phân loại';
			$wp_taxonomies['category']->labels->edit_item = 'Sửa phân loại';
			$wp_taxonomies['category']->labels->new_item = 'Phân loại';
			$wp_taxonomies['category']->labels->view_item = 'Xem phân loại';
			$wp_taxonomies['category']->labels->search_items = 'Tìm phân loại';
			$wp_taxonomies['category']->labels->not_found = 'Không có phân loại nào được tìm thấy';
			$wp_taxonomies['category']->labels->not_found_in_trash = 'Không có phân loại nào trong thùng rác';
			$wp_taxonomies['category']->labels->all_items = 'Tất cả phân loại';
			$wp_taxonomies['category']->labels->menu_name = 'Phân loại';
			$wp_taxonomies['category']->labels->name_admin_bar = 'Phân loại';
		}

		if( isset($wp_taxonomies['post_tag']) && is_object( $wp_taxonomies['post_tag']) && !empty($wp_taxonomies['post_tag']->labels) ) {
			$wp_taxonomies['post_tag']->label = 'Đặc điểm';
			$wp_taxonomies['post_tag']->labels->name = 'Đặc điểm';
			$wp_taxonomies['post_tag']->labels->singular_name = 'Đặc điểm';
			$wp_taxonomies['post_tag']->labels->add_new = 'Thêm đặc điểm';
			$wp_taxonomies['post_tag']->labels->add_new_item = 'Thêm đặc điểm';
			$wp_taxonomies['post_tag']->labels->edit_item = 'Sửa đặc điểm';
			$wp_taxonomies['post_tag']->labels->new_item = 'Loại nhà';
			$wp_taxonomies['post_tag']->labels->view_item = 'Xem đặc điểm';
			$wp_taxonomies['post_tag']->labels->search_items = 'Tìm đặc điểm';
			$wp_taxonomies['post_tag']->labels->not_found = 'Không có đặc điểm nào được tìm thấy';
			$wp_taxonomies['post_tag']->labels->not_found_in_trash = 'Không có đặc điểm nào trong thùng rác';
			$wp_taxonomies['post_tag']->labels->all_items = 'Tất cả đặc điểm';
			$wp_taxonomies['post_tag']->labels->menu_name = 'Đặc điểm';
			$wp_taxonomies['post_tag']->labels->name_admin_bar = 'Đặc điểm';
		}

		
		
	}

	public function _theme_action_register_custom_type_10() {

		$labels = array(
			'name'               => 'Nội dung',
			'singular_name'      => 'Nội dung',
			'add_new'            => 'Thêm mới Nội dung',
			'add_new_item'       => 'Thêm mới Nội dung',
			'edit_item'          => 'Sửa Nội dung',
			'new_item'           => 'Nội dung mới',
			'view_item'          => 'Xem Nội dung',
			'search_items'       => 'Tìm Nội dung',
			'not_found'          => 'Không có Nội dung nào',
			'not_found_in_trash' => 'Không có Nội dung nào trong Thùng rác',
			'parent_item_colon'  => 'Nội dung cha:',
			'menu_name'          => 'Nội dung',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-post',
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false, // ẩn bài viết ở front-end
			'exclude_from_search' => true, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => false,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'revisions',
			),
		);
	
		register_post_type( 'content_builder', $args );

		$labels = array(
			'name'               => 'Trang SEO',
			'singular_name'      => 'Trang SEO',
			'add_new'            => 'Thêm mới Trang SEO',
			'add_new_item'       => 'Thêm mới Trang SEO',
			'edit_item'          => 'Sửa Trang SEO',
			'new_item'           => 'Trang SEO mới',
			'view_item'          => 'Xem Trang SEO',
			'search_items'       => 'Tìm Trang SEO',
			'not_found'          => 'Không có Trang SEO nào',
			'not_found_in_trash' => 'Không có Trang SEO nào trong Thùng rác',
			'parent_item_colon'  => 'Trang SEO cha:',
			'menu_name'          => 'Trang SEO',
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => true,
			//'description'         => 'description',
			//'taxonomies'          => array(),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 12,
			'menu_icon'           => 'dashicons-edit-page',
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true, // ẩn bài viết ở front-end
			'exclude_from_search' => true, // loại khỏi kết quả tìm kiếm
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => ['slug'=>'dich-vu'],
			'capability_type'     => 'page',
			'supports'            => array(
				'title',
				'editor',
				//'author',
				'thumbnail',
				'excerpt',
				//'custom-fields',
				//'trackbacks',
				//'comments',
				'revisions',
				'page-attributes',
				//'post-formats',
			),
		);
	
		register_post_type( 'seo_post', $args );
	}

	/**
	 * Changes the name in admin menu from Post to Blog Post
	 * @internal
	 */
	public function _admin_action_rename_menu() {
		global $menu, $submenu;

		remove_menu_page( 'edit-comments.php' ); // ẩn menu Comments
		
		if ( isset( $menu[5] ) ) {
			$menu[5][0] = 'Sản phẩm';
		}
		//debug_log($submenu);
		if ( isset( $submenu['edit.php'] ) ) {
			$submenu['edit.php'][5][0] = 'Xem tất cả';
			$submenu['edit.php'][10][0] = 'Tạo Sản phẩm mới';
			if(isset($submenu['edit.php'][16]))
				unset($submenu['edit.php'][16]);
		}
	}

	public function _theme_action_register_taxonomy() {
		//global $wp_taxonomies;

		// if ( taxonomy_exists( 'post_tag'))
		// 	unset( $wp_taxonomies['post_tag']);
		// unregister_taxonomy('post_tag');

		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => 'Địa điểm',
			'singular_name'     => 'Địa điểm',
			'search_items'      => 'Tìm Địa điểm',
			'all_items'         => 'Tất cả Địa điểm',
			'edit_item'         => 'Sửa Địa điểm',
			'update_item'       => 'Cập nhật Địa điểm',
			'add_new_item'      => 'Thêm Địa điểm mới',
			'new_item_name'     => 'Địa điểm mới',
			'menu_name'         => 'Địa điểm',
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => ['slug'=>'dia-diem'],
			'public' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud' => false,
		);
		register_taxonomy( 'location', 'post', $args ); // our new 'format' taxonomy

		
		// $default_province = [
		// 	'name' => 'Toàn quốc',
		// 	'slug' => 'toan-quoc',
		// 	'description' => 'Mặc định'
		// ];
		// $default = (int) get_option( 'default_term_province', -1 );

		//delete_option( 'default_term_province' );

		$labels = array(
			'name'              => 'Giá thiết kế',
			'singular_name'     => 'Giá thiết kế',
			'search_items'      => 'Tìm Giá thiết kế',
			'all_items'         => 'Tất cả Giá thiết kế',
			'edit_item'         => 'Sửa Giá thiết kế',
			'update_item'       => 'Cập nhật Giá thiết kế',
			'add_new_item'      => 'Thêm Giá thiết kế mới',
			'new_item_name'     => 'Giá thiết kế mới',
			'menu_name'         => 'Giá thiết kế',
		);
		$default_price = [
			'name' => 'Giá chung',
			'slug' => 'gia-chung',
			'description' => ''
		];
		$default = (int) get_option( 'default_term_price', -1 );
		// delete_option( 'default_term_prices' );
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'rewrite'           => false,
			'public' => false,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => false,
			'default_term' => ($default>0)?$default:$default_price
			
		);
		register_taxonomy( 'price', 'post', $args );

	}
	
	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Custom_Types::instance();