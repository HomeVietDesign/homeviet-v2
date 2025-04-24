<?php
namespace HomeViet;

class Admin_Contractor {

	private static $instance = null;

	private function __construct() {

		if(is_admin()) {

			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
			
			add_action( 'add_meta_boxes', [$this, 'switch_boxes'] );
			
			add_filter( 'disable_months_dropdown', [$this, 'disable_months_dropdown'], 10, 2 );

			add_action( 'restrict_manage_posts', [$this, 'filter_post_type_by_taxonomy'] );

			add_filter( 'parse_query', [$this, 'taxonomy_parse_filter'] );

			if(unyson_exists()) {
				add_action( 'fw_save_post_options', [$this, 'save_contractor_15'], 15, 2 );
			} else {
				add_action( 'save_post_contractor', [$this, 'save_contractor_15'], 15, 2 );
			}

			add_action( 'wp_ajax_check_contractor_exists', [$this, 'ajax_check_contractor_exists'] );

			add_action( 'manage_contractor_posts_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_contractor_posts_columns', [ $this, 'add_custom_columns_header' ] );

		}

		//add_action( 'create_default_contractor_order_terms', [$this, 'async_create_default_contractor_order_terms'] );

	}

	public function async_create_default_contractor_order_terms($post_id) {
		$passwords = get_terms([
			'taxonomy' => 'passwords',
			'hide_empty' => false,
			'fields' => 'ids',
		]);
		if($passwords) {
			$default = (int) get_option( 'default_term_passwords', -1 );
			foreach ($passwords as $pass_id) {
				if($pass_id==$default) {
					update_post_meta($post_id, 'term_orderby_'.$pass_id, 1);
				} else {
					update_post_meta($post_id, 'term_orderby_'.$pass_id, 0);
				}
			}
		}
	
	}

	public function custom_columns_value($column, $post_id) {
		switch ($column) {
			case 'phone_number':
				echo esc_html(get_post_meta( $post_id, '_phone_number', true ));
				break;
		}
	}

	public function add_custom_columns_header($columns) {
		
		$columns['phone_number'] = 'Số điện thoại';
		
		return $columns;
	}

	public static function check_contractor_exists($phone_number, $id=0) {
		$args = [
			'post_type'=>'contractor',
			'posts_per_page'=>1,
			'fields'=>'ids',
			'meta_key'=>'_phone_number',
			'meta_value'=>$phone_number,
			'post_status'=>['publish']
		];
		if($id>0) {
			$args['post__not_in'] = [$id];
		}

		$check = new \WP_Query( $args );

		if($check->have_posts()) {
			return true;
		}

		return false;
	}

	public function ajax_check_contractor_exists() {
		$id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$phone_number = isset($_POST['phone_number']) ? sanitize_phone_number($_POST['phone_number']) : '';

		$phone_number_exists = false;

		if(''!=$phone_number) {
			$phone_number_exists = self::check_contractor_exists($phone_number, $id);
		}
		
		wp_send_json($phone_number_exists);

		die;
	}

	public function save_contractor_15($post_id, $post) {
		if ($post->post_type!='contractor') return;

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if(isset($_POST['fw_options']['_phone_number'])) {
			global $wpdb;
			$phone_number = sanitize_phone_number($_POST['fw_options']['_phone_number']);

			update_post_meta( $post_id, '_phone_number', $phone_number );
			
			if($post->post_status=='publish') {
				// không được đăng đối tượng với số điện thoại đã tồn tại. trường xác định là _phone_number hoặc không có thông tin nhận dạng
				if( $phone_number!='' && self::check_contractor_exists($phone_number, $post->ID) ) {
				//if( empty($phone_number) || ($phone_number!='' && self::check_contractor_exists($phone_number, $post->ID)) ) {
					$wpdb->update( $wpdb->posts, ['post_status' => 'draft'], ['ID' => $post_id] );
					wp_cache_delete( $post_id, 'posts' );
				}
			}
			
		}
	}

	public function enqueue_scripts($hook) {
		global $post_type;
		if($hook=='post.php' && $post_type=='contractor') {
			wp_enqueue_style( 'contractor', THEME_URI.'/assets/css/admin-contractor.css', [], '' );
			wp_enqueue_script( 'contractor', THEME_URI.'/assets/js/admin-contractor.js', array('jquery'), '', false );
			wp_localize_script( 'contractor', 'adminContractor', [
				'contractor_rating_top'=>get_option('contractor_rating_top')
			] );
		}
	}

	public function taxonomy_parse_filter( $query ) {
		//modify the query only if it admin and main query.
		if( !(is_admin() AND $query->is_main_query()) ){ 
			return $query;
		}

		$post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
	
		if($post_type=='contractor') {
			$tax_query = [];

			$cc = isset($_GET['cc']) ? intval($_GET['cc']) : 0;
			if($cc!=0) {
				$tax_query['cc'] = ['taxonomy' => 'contractor_cat'];
				if($cc>0) {
					$tax_query['cc']['field'] = 'term_id';
					$tax_query['cc']['terms'] = $cc;
				} else {
					$tax_query['cc']['operator'] = 'NOT EXISTS';
				}

			}
			
			$cr = isset($_GET['cr']) ? intval($_GET['cr']) : 0;
			if($cr!=0) {
				$tax_query['cr'] = ['taxonomy' => 'contractor_rating'];
				if($cr>0) {
					$tax_query['cr']['field'] = 'term_id';
					$tax_query['cr']['terms'] = $cr;
				} else {
					$tax_query['cr']['operator'] = 'NOT EXISTS';
				}
			}

			$pwd = isset($_GET['pwd']) ? intval($_GET['pwd']) : 0;
			if($pwd!=0) {
				$tax_query['pwd'] = ['taxonomy' => 'passwords'];
				if($pwd>0) {
					$tax_query['pwd']['field'] = 'term_id';
					$tax_query['pwd']['terms'] = $pwd;
				} else {
					$tax_query['pwd']['operator'] = 'NOT EXISTS';
				}
			}

			if(!empty($tax_query)) {
				$query->set('tax_query', $tax_query);
			}
		}

		return $query;
	}

	public function filter_post_type_by_taxonomy($post_type) {
		
		if ($post_type == 'contractor') {
			wp_dropdown_categories(array(
				'show_option_all' => '- Hạng mục -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'contractor_cat',
				'name'            => 'cc',
				'orderby'         => 'name',
				'selected'        => isset($_GET['cc']) ? intval($_GET['cc']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

			wp_dropdown_categories(array(
				'show_option_all' => '- Đánh giá -',
				'show_option_none' => '- Chưa có -',
				'taxonomy'        => 'contractor_rating',
				'name'            => 'cr',
				'orderby'         => 'name',
				'selected'        => isset($_GET['cr']) ? intval($_GET['cr']) : 0,
				'show_count'      => true,
				'hide_empty'      => true,
				'value_field'	  => 'term_id'
			));

			// wp_dropdown_categories(array(
			// 	'show_option_all' => '- Chủ đầu tư -',
			// 	'show_option_none' => '- Chưa có -',
			// 	'taxonomy'        => 'passwords',
			// 	'name'            => 'pwd',
			// 	'orderby'         => 'name',
			// 	'selected'        => isset($_GET['pwd']) ? intval($_GET['pwd']) : 0,
			// 	'show_count'      => true,
			// 	'hide_empty'      => true,
			// 	'value_field'	  => 'term_id'
			// ));
		};
	}

	public function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='contractor') {
			$disabled = true;
		}
		return $disabled;
	}


	public function post_content_editor($post) {
		wp_editor( self::unescape($post->post_content), 'content', [
			'tinymce' => true,
			'textarea_rows' => 15,
		] );
	}

	public function switch_boxes() {

		remove_meta_box(
			'slugdiv',
			'contractor',
			'normal'
		);

		remove_meta_box(
			'pageparentdiv',
			'contractor',
			'side'
		);

		remove_meta_box(
			'passwordsdiv',
			'contractor',
			'side'
		);

		remove_meta_box(
            'postexcerpt' // ID
        ,   'contractor'            // Screen, empty to support all post types
        ,   'normal'      // Context
        );

        add_meta_box(
            'postexcerpt2'     // Reusing just 'postexcerpt' doesn't work.
        ,   __( 'Excerpt' )    // Title
        ,   array ( $this, 'postexcerpt2' ) // Display function
        ,   'contractor'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'core'            // Priority
        );
	}

	public function postexcerpt2( $post ) {
    ?>
        <label class="screen-reader-text" for="excerpt"><?php
        _e( 'Excerpt' )
        ?></label>
        <?php
        // We use the default name, 'excerpt', so we don’t have to care about
        // saving, other filters etc.
        wp_editor(
            self::unescape( $post->post_excerpt ),
            'excerpt',
            array (
	            'textarea_rows' => 20,
	            'media_buttons' => false,
	            'teeny'         => false,
	            'tinymce'       => true
            )
        );
    }

	public static function unescape( $str ) {
		return html_entity_decode( $str, ENT_QUOTES, 'UTF-8' );
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Contractor::instance();