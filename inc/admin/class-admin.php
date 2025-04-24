<?php
namespace HomeViet;

class Admin {

	private static $instance = null;

	private function __construct() {

		require_once THEME_DIR.'/inc/simplehtmldom/simple_html_dom.php';
		require_once THEME_DIR.'/inc/admin/class-select-post-export.php';
		require_once THEME_DIR.'/inc/admin/class-admin-post.php';
		require_once THEME_DIR.'/inc/admin/class-admin-contractor.php';
		require_once THEME_DIR.'/inc/admin/class-admin-media.php';
		require_once THEME_DIR.'/inc/admin/class-admin-passwords.php';
		require_once THEME_DIR.'/inc/admin/class-admin-province.php';
		require_once THEME_DIR.'/inc/admin/class-admin-contractor_page.php';
		require_once THEME_DIR.'/inc/admin/class-admin-contractor_cat.php';
		require_once THEME_DIR.'/inc/admin/class-admin-contractor_rating.php';
		require_once THEME_DIR.'/inc/admin/class-admin-contractor_class.php';
		//require_once THEME_DIR.'/inc/admin/class-admin-update-posts.php';

		if(is_admin()) {
			if( ! class_exists( 'WP_List_Table' ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
			}

			require_once THEME_DIR.'/inc/admin/class-orders-help-tabs.php';
			require_once THEME_DIR.'/inc/admin/class-orders-list-table.php';
			require_once THEME_DIR.'/inc/admin/class-admin-order.php';

			add_action( 'manage_posts_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_action( 'manage_pages_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_posts_columns', [ $this, 'add_custom_columns_header' ] );
			add_filter( 'manage_pages_columns', [ $this, 'add_custom_columns_header' ] );

			add_action( 'admin_print_styles-edit.php', array($this,'admin_edit_styles') );
			add_action( 'admin_print_scripts', [$this,'admin_print_head_scripts'] );
		}

	}

	public function admin_print_head_scripts() {
		global $post_type;
		?>
			
		<script type="text/javascript">
			function sanitize_phone_number(phone_number) {
				phone_number = phone_number.replace(/\D/g,'');
				phone_number = phone_number.replace(/^84/,'0');

				if(phone_number.match(/^0\d{9}$/)) {
					return phone_number;
				}

				return '';
			}
		</script>
		<?php
		
	}

	public function admin_edit_styles() {
		global $post_type;
		?>
		<style type="text/css">
			<?php //if(post_type_supports( $post_type, 'thumbnail' )) { ?>
			.column-thumbnail {
				width: 150px;
			}
			<?php //} ?>
		</style>
		<?php
	}

	/**
	 * giá trị các cộng thông tin mởi rộng cho đối tượng(post)
	 */
	public function custom_columns_value( $column, $post_id ) {

		switch ($column) {
			case 'thumbnail':
				if ( has_post_thumbnail( $post_id ) ) {
					the_post_thumbnail( 'medium',['style'=>'width:100%;max-width:200px;height:auto;'] );
				}
				break;

			case 'menu':
				$apply_menu = fw_get_db_post_option($post_id, 'apply_menu');
				// debug('apply_menu');
				// debug($apply_menu);
				if($apply_menu) {
					$menu = wp_get_nav_menu_object($apply_menu[0]);
					if($menu) {
						echo esc_html($menu->name);
					}
				}
				
				break;
		}
		
	}

	/**
	 * Tiêu đề các cột thông tin mở rộng cho đối tượng(post)
	 */
	public function add_custom_columns_header( $columns ) {
		global $post_type;
		

		$cb = $columns['cb'];
		unset($columns['cb']);

		//debug_log($columns);

		if(isset($columns['tags'])) {
			unset($columns['tags']);
		}

		$new_columns = ['cb' => $cb];
		//if(post_type_supports( $post_type, 'thumbnail' )) {
			$new_columns['thumbnail'] = 'Ảnh';
		//}

		$columns = array_merge($new_columns, $columns);
		
		if($post_type=='post' || $post_type=='page' || $post_type=='seo_post') {
			$columns['menu'] = 'Menu';
		}
		
		return $columns;

	}

	/*
	public static function post_code_exists($code, $exclude=0) {
		$args = array(
			'post_type'    => 'post',
			'meta_key'   => '_code',
			'meta_value'   => $code,
			'post_status' => ['publish', 'future'],
			'posts_per_page' => 1,
			'fields' => 'ids'
		);
		if($exclude) {
			$args['post__not_in'] = [$exclude];
		}

		$check = new \WP_Query( $args );
		//debug_log($check);
		if($check->have_posts()) {
			return absint($check->posts[0]);
		}
		return false;
	}
	*/

	public static function post_exists($post_title, $type = '', $status = '', $exclude=0) {
		global $wpdb;

		$post_title   = wp_unslash( sanitize_post_field( 'post_title', $post_title, 0, 'db' ) );
		$post_type    = wp_unslash( sanitize_post_field( 'post_type', $type, 0, 'db' ) );
		$post_status  = wp_unslash( sanitize_post_field( 'post_status', $status, 0, 'db' ) );
		$exclude  = absint( $exclude );

		$query = "SELECT ID FROM $wpdb->posts WHERE";
		$args  = array();
		
		$query .= ' post_title = %s';
		$args[] = $post_title;

		if ( ! empty( $type ) ) {
			$query .= ' AND post_type = %s';
			$args[] = $post_type;
		}

		if ( ! empty( $status ) ) {
			$query .= ' AND post_status = %s';
			$args[] = $post_status;
		}

		if ( $exclude > 0 ) {
			$query .= ' AND ID != %d';
			$args[] = $exclude;
		}

		if ( ! empty( $args ) ) {
			return (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );
		}

		return 0;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin::instance();