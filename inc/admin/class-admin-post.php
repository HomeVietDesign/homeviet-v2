<?php
namespace HomeViet;

class Admin_Post {

	private static $instance = null;

	private $product_total_percent = 100;

	private function __construct() {
		$this->product_total_percent = floatval(get_option('product_total_percent'));

		if(is_admin()) {
			
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
			
			//add_action( 'admin_print_scripts', [$this,'admin_print_head_scripts'] );
			
			add_action( 'admin_print_styles-edit.php', array($this,'admin_edit_styles') );
			add_action( 'admin_print_footer_scripts-edit.php', array($this,'admin_edit_footer_scripts') );

			add_action( 'admin_print_styles-post.php', [$this,'admin_head_post_styles'] );
			add_action( 'admin_print_styles-post-new.php', [$this,'admin_head_post_styles'] );

			add_action( 'admin_print_footer_scripts-post.php', [$this,'admin_footer_post_scripts'] );
			add_action( 'admin_print_footer_scripts-post-new.php', [$this,'admin_footer_post_scripts'] );

			add_action( 'save_post', array($this, 'ajax_save_post'), 999999, 2 );

			add_action( 'manage_post_posts_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_post_posts_columns', [ $this, 'add_custom_columns_header' ] );

			add_action( 'save_post', [$this, 'save_post_15'], 15, 3 );
			
			add_filter( 'admin_notices', [$this, 'admin_notice'] );

			add_action( 'add_meta_boxes', [$this, 'switch_boxes'] );

			add_filter( 'disable_months_dropdown', [$this, 'disable_months_dropdown'], 10, 2 );

			// add_filter( 'bulk_actions-edit-post', [$this, 'register_custom_bulk_actions'] );
			// add_filter( 'handle_bulk_actions-edit-post', [$this, 'custom_bulk_action_handler'], 10, 3 );

			// add_filter( 'disable_categories_dropdown', '__return_true' );

			add_action( 'post_submitbox_misc_actions', [$this, 'custom_search_suggestion'] );

			add_action( 'wp_ajax_change_allow_order', [$this, 'ajax_change_allow_order'] );
			add_action( 'wp_ajax_change_up_nha88', [$this, 'ajax_change_up_nha88'] );
			add_action( 'wp_ajax_change_footer_content', [$this, 'ajax_change_footer_content'] );

			add_action( 'wp_ajax_change_breadth', [$this, 'ajax_change_breadth'] );
			add_action( 'wp_ajax_change_length', [$this, 'ajax_change_length'] );
			add_action( 'wp_ajax_change_area_1', [$this, 'ajax_change_area_1'] );
			add_action( 'wp_ajax_change_floors', [$this, 'ajax_change_floors'] );
			add_action( 'wp_ajax_change_design_price', [$this, 'ajax_change_design_price'] );
			add_action( 'wp_ajax_change_use_general_design_price', [$this, 'ajax_change_use_general_design_price'] );
			add_action( 'wp_ajax_change_use_general_price', [$this, 'ajax_change_use_general_price'] );
			add_action( 'wp_ajax_change_price', [$this, 'ajax_change_price'] );
			add_action( 'wp_ajax_change_total_factor', [$this, 'ajax_change_total_factor'] );

			add_action( 'wp_ajax_change_featured', [$this, 'ajax_change_featured'] );

			add_filter( 'manage_edit-post_sortable_columns', [$this, 'custom_column_sortable'] );
			add_action( 'pre_get_posts', [$this, 'sort_query'] );

			//add_action( 'admin_footer', [$this, 'test'] );
		}
		
	}

	public function test() {
		global $wp_query;

		debug($wp_query->request);
	}

	public function custom_search_suggestion($post) {
		if($post->post_type=='seo_post') {
			$value = get_post_meta($post->ID, '_search_suggested', true);

			echo '<div class="misc-pub-section misc-pub-search_suggested" style="border-top:1px solid #dcdcde;">
			<span>'
			. '<label><input type="checkbox"' . (!empty($value) ? ' checked="checked" ' : null) . 'value="1" name="_search_suggested" /> Gợi ý tìm kiếm sẵn</label>'
			.'</span></div>';
		}
	}

	public function sort_views_posts_clauses($clauses, $query) {
		global $wpdb;

		//debug_log($clauses);
		if(class_exists('WP_Statistics')) {
			$table_join = \WP_STATISTICS\DB::table('pages');

			$clauses['join'] .= "LEFT JOIN {$table_join} ON({$wpdb->posts}.ID={$table_join}.id)";
			$clauses['groupby'] .= "{$wpdb->posts}.ID";
			$clauses['fields'] .= ", SUM({$table_join}.count) as views";
			$clauses['orderby'] = "views {$query->get('order')}";
		}

		return $clauses;
	}

	public function sort_query($query) {
		if (!is_admin() || !$query->is_main_query()) {
			return;
		}

		if ($query->get('orderby') == 'views') {
			add_filter( 'posts_clauses', [$this, 'sort_views_posts_clauses'], 10, 2 );
		} elseif ($query->get('orderby') == 'order_count') {
			$query->set('meta_key','_order_count');
			$query->set('orderby',['meta_value_num' => $query->get('order'), 'date'=>'DESC']);
		}
	}

	public function custom_column_sortable($sortable_columns) {
		if(class_exists('WP_Statistics')) {
			$sortable_columns['views'] = ['views', true];
		}

		$sortable_columns['order_count'] = ['order_count', true];

		return $sortable_columns;
	}

	public function ajax_change_featured() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$featured = isset($_POST['featured']) ? sanitize_key($_POST['featured']) : '';

		if($featured !== 'yes') $featured = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_featured', $featured);
		}

		$response = false;
		$featured = get_post_meta($post_id, '_featured', true);
		if($featured == 'yes') $response = true;

		wp_send_json($response);
	}

	public function ajax_change_use_general_price() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$use_general_price = isset($_POST['use_general_price']) ? sanitize_key($_POST['use_general_price']) : '';

		if($use_general_price !== 'yes') $use_general_price = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_use_general_price', $use_general_price);
		}

		$response = false;
		$use_general_price = get_post_meta($post_id, '_use_general_price', true);
		if($use_general_price == 'yes') $response = true;

		wp_send_json($response);
	}

	public function ajax_change_use_general_design_price() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$use_general_design_price = isset($_POST['use_general_design_price']) ? sanitize_key($_POST['use_general_design_price']) : '';

		if($use_general_design_price !== 'yes') $use_general_design_price = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_use_general_design_price', $use_general_design_price);
		}

		$response = false;
		$use_general_design_price = get_post_meta($post_id, '_use_general_design_price', true);
		if($use_general_design_price == 'yes') $response = true;

		wp_send_json($response);
	}

	public function ajax_change_total_factor() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$total_factor = isset($_POST['total_factor']) ? sanitize_text_field($_POST['total_factor']) : '';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_total_factor', $total_factor);
		}

		$response = get_post_meta($post_id, '_total_factor', true);
		
		wp_send_json($response);

	}

	public function ajax_change_price() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$price = isset($_POST['price']) ? sanitize_text_field($_POST['price']) : '';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_price', $price);
		}

		$response = get_post_meta($post_id, '_price', true);
		
		wp_send_json($response);

	}

	public function ajax_change_design_price() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$design_price = isset($_POST['design_price']) ? sanitize_text_field($_POST['design_price']) : '';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_design_price', $design_price);
		}

		$response = get_post_meta($post_id, '_design_price', true);
		
		wp_send_json($response);

	}

	public function ajax_change_floors() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$floors = isset($_POST['floors']) ? sanitize_text_field($_POST['floors']) : '';

		if($floors!='') $floors = floatval($floors);

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_floors', $floors);
		}

		$response = get_post_meta($post_id, '_floors', true);
		
		wp_send_json($response);

	}

	public function ajax_change_area_1() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$area_1 = isset($_POST['area_1']) ? sanitize_text_field($_POST['area_1']) : '';

		if($area_1!='') $area_1 = floatval($area_1);

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_area_1', $area_1);
		}

		$response = get_post_meta($post_id, '_area_1', true);
		
		wp_send_json($response);

	}

	public function ajax_change_length() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$length = isset($_POST['length']) ? sanitize_text_field($_POST['length']) : '';

		if($length!='') $length = floatval($length);

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_length', $length);
		}

		$response = get_post_meta($post_id, '_length', true);
		
		wp_send_json($response);

	}

	public function ajax_change_breadth() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$breadth = isset($_POST['breadth']) ? sanitize_text_field($_POST['breadth']) : '';

		if($breadth!='') $breadth = floatval($breadth);

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_breadth', $breadth);
		}

		$response = get_post_meta($post_id, '_breadth', true);
		
		wp_send_json($response);

	}

	public function ajax_change_allow_order() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$allow_order = isset($_POST['allow_order']) ? sanitize_key($_POST['allow_order']) : '';

		if($allow_order !== 'yes') $allow_order = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_allow_order', $allow_order);
		}

		$response = false;
		$allow_order = get_post_meta($post_id, '_allow_order', true);
		if($allow_order == 'yes') $response = true;

		wp_send_json($response);
	}

	public function ajax_change_up_nha88() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$up_nha88 = isset($_POST['up_nha88']) ? sanitize_key($_POST['up_nha88']) : '';

		if($up_nha88 !== 'yes') $up_nha88 = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			fw_set_db_post_option($post_id, 'up_nha88', $up_nha88);
		}

		$response = false;
		$up_nha88 = fw_get_db_post_option($post_id, 'up_nha88', 'no');
		if($up_nha88 == 'yes') $response = true;

		wp_send_json($response);
	}

	public function ajax_change_footer_content() {
		$post_id = isset($_POST['id']) ? absint($_POST['id']) : 0;
		$footer_content = isset($_POST['footer_content']) ? sanitize_key($_POST['footer_content']) : '';

		if($footer_content !== 'yes') $footer_content = 'no';

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_footer_content', $footer_content);
		}

		$response = false;
		$footer_content = get_post_meta($post_id, '_footer_content', true);
		if($footer_content == 'yes') $response = true;

		wp_send_json($response);
	}

	public function custom_bulk_action_handler( $redirect_to, $doaction, $post_ids ) {
		
		if ( $doaction == 'allow_order' || $doaction == 'deny_order' ) {
			if($doaction == 'allow_order') {
				foreach ( $post_ids as $post_id ) {
					update_post_meta($post_id, '_allow_order', 'yes');
				}
			} elseif ($doaction == 'deny_order') {
				foreach ( $post_ids as $post_id ) {
					update_post_meta($post_id, '_allow_order', 'no');
				}
			}
			
			$redirect_to = add_query_arg( 'batps', count( $post_ids ), $redirect_to );
			$redirect_to = add_query_arg( 'act', $doaction, $redirect_to );
		}

		return $redirect_to;
	}

	public function register_custom_bulk_actions($bulk_actions) {

		$bulk_actions['allow_order'] = 'Cho chọn';
		$bulk_actions['deny_order'] = 'Ẩn chọn';

  		return $bulk_actions;
	}

	public function disable_months_dropdown($disable, $post_type) {
		if('post'==$post_type) {
			$disable = true;
		}
		return $disable;
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
			'post',
			'normal'
		);

		remove_meta_box(
			'pageparentdiv',
			'post',
			'side'
		);

		remove_meta_box(
			'tagsdiv-post_tag',
			'post',
			'side'
		);

		remove_meta_box(
			'tagsdiv-amenities',
			'post',
			'side'
		);

		remove_meta_box(
			'passwordsdiv',
			'post',
			'side'
		);

		remove_meta_box(
            'postexcerpt' // ID
        ,   'post'            // Screen, empty to support all post types
        ,   'normal'      // Context
        );

        add_meta_box(
            'postexcerpt2'     // Reusing just 'postexcerpt' doesn't work.
        ,   __( 'Excerpt' )    // Title
        ,   array ( $this, 'postexcerpt2' ) // Display function
        ,   'post'              // Screen, we use all screens with meta boxes.
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
	            'textarea_rows' => 15,
	            'media_buttons' => false,
	            'teeny'         => false,
	            'tinymce'       => true
            )
        );
    }

	public static function unescape( $str ) {
		return html_entity_decode( $str, ENT_QUOTES, 'UTF-8' );
	}

	public function admin_notice() {
		global $post, $pagenow;

		// Don't show an initial warning on a new post.
		if ( 'post.php' !== $pagenow ) {
			return;
		}

		if ( ! empty( $_REQUEST['batps'] ) ) {
			$count = intval( $_REQUEST['batps'] );
			$act = $_REQUEST['act'];
			switch ($act) {

				case 'allow_order':
				case 'deny_order':
					echo '<div id="message" class="updated fade"><p>'.(($act=='allow_order')?'&#x22;Cho chọn&#x22;':'&#x22;Ẩn chọn&#x22;').' cho <strong>' . $count . '</strong> bài viết.</p></div>';
					break;

			}
			
		}

		// Show no warning, when the title is empty.
		if ( empty( $post->post_title ) ) {
			return;
		}


	}

	public function save_post_15($post_id, $post, $update) {

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
    	
    	if($post->post_type === 'seo_post' && isset($_POST['_search_suggested'])) {
    		update_post_meta($post_id, '_search_suggested', $_POST['_search_suggested']);
    	}

		if(!$update) {
			global $wpdb;
			$wpdb->update( $wpdb->posts, ['post_name' => date('Ymd-His', strtotime($post->post_date))], ['ID' => $post_id] );
			
			wp_cache_delete( $post_id, 'posts' );
		}

	}

	public function ajax_save_post($post_id, $post) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if ( wp_is_post_revision( $post_id ) ) {
        	return;
        }
    	
		if($post->post_type === 'post' || $post->post_type === 'page' || $post->post_type === 'seo_post' || 'contractor' === $post->post_type || 'contractor_page' === $post->post_type) {
			
			if(isset($_POST['ajax_save_post']) && intval($_POST['ajax_save_post']) === 1){
				wp_send_json_success( null, 200 );
			}
		}
		return $post_id;
	}

	public function enqueue_scripts($hook) {
		if($hook=='edit.php') {
			//add_thickbox();
			wp_enqueue_script('jquery-input-number', THEME_URI.'/libs/jquery-input-number/jquery-input-number.js', array('jquery'), '', false);
		}

	}

	public function admin_print_head_scripts() {
		?>
		<script type="text/javascript">
			
		</script>
		<?php
		
	}

	public function admin_edit_styles() {
		global $post_type;
		?>
		<style type="text/css">
			
			<?php if($post_type=='post'): ?>
			.column-title {
				width: 250px;
			}
			.costs.column-costs label,
			.dimensions.column-dimensions label {
				display: flex;
				align-items: center;
				margin-bottom: 3px;
			}
			.costs.column-costs label span,
			.dimensions.column-dimensions label span {
				display: block;
			}
			.dimensions.column-dimensions label span:first-child {
				width: 65px;
			}
			.costs.column-costs label span:first-child {
				width: 80px;
			}
			.costs.column-costs label span.ultimate {
				padding: 5px;
				font-weight: bold;
				border-radius: 3px;
			}
			.costs.column-costs input,
			.dimensions.column-dimensions input {
				width: 80px;
				padding-right: 0;
			}
			.code.column-code input,
			.price.column-price input {
				max-width: 90%;
				padding-right: 0;
			}
			<?php endif; ?>
		</style>
		<?php
	}

	public function admin_edit_footer_scripts() {
		global $post_type;

		?>
		<script type="text/javascript">
			jQuery(function($){
				let post_type = '<?=$post_type?>';
				if(post_type=='post') {
					
					$(document).on('click', '.row-actions .editinline', function(e){
						let _this = $(this),
							tr = _this.parents('tr'),
							id = tr.find('input[name="post[]"]').val();

						setTimeout(function(){
							$('body').find('tr#edit-'+id).find('input[name="post_name"]').attr('readonly', true);

							let $category_checklist = $('body').find('tr#edit-'+id).find('ul.category-checklist');
							$('<span class="input-text-wrap"><input class="find-list-category" type="text" placeholder="Tìm..."></span>').insertBefore($category_checklist);

							let $location_checklist = $('body').find('tr#edit-'+id).find('ul.location-checklist');
							$('<span class="input-text-wrap"><input class="find-list-location" type="text" placeholder="Tìm..."></span>').insertBefore($location_checklist);
						}, 100);
					});

					$(document).on('keyup change', 'input.find-list-category', function(e){
						let that = $(this),
							s = that.val().toLowerCase();
						$('ul.category-checklist li').filter(function() {
							$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
						});
					});

					$(document).on('keyup change', 'input.find-list-location', function(e){
						let that = $(this),
							s = that.val().toLowerCase();
						$('ul.location-checklist li').filter(function() {
							$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
						});
					});

					$('.change_featured').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), featured = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_featured', featured: featured},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.change_allow_order').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), allow_order = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_allow_order', allow_order: allow_order},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.change_up_nha88').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), up_nha88 = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_up_nha88', up_nha88: up_nha88},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.change_footer_content').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), footer_content = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_footer_content', footer_content: footer_content},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.change_use_general_price').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), use_general_price = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_use_general_price', use_general_price: use_general_price},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.change_use_general_design_price').on('click', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), use_general_design_price = (_this.prop('checked'))?'yes':'no';
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_use_general_design_price', use_general_design_price: use_general_design_price},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								if(response) {
									_this.prop('checked', true);
								} else {
									_this.prop('checked', false);
								}
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._breadth').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), breadth = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_breadth', breadth: breadth},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._floors').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), floors = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_floors', floors: floors},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._area_1').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), area_1 = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_area_1', area_1: area_1},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._length').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), length = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_length', length: length},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._design_price').on('change', function(e){
						console.log(e);
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), design_price = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_design_price', design_price: design_price},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._price').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), price = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_price', price: price},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

					$('.quick-edit-field._total_factor').on('change', function(e){
						let _this = $(this), id = _this.data('id'), nonce = _this.data('nonce'), total_factor = _this.val();
						$.ajax({
							url: ajaxurl,
							type: 'post',
							dataType: 'json',
							data: {id: id, nonce: nonce, action:'change_total_factor', total_factor: total_factor},
							beforeSend: function() {
								_this.prop('disabled', true);
							},
							success: function(response) {
								//console.log(response);
								_this.val(response);
							},
							complete: function() {
								_this.prop('disabled', false);
							}
						});
					});

				}
				
			});
		</script>
		<?php

	}

	/**
	 * giá trị các cộng thông tin mởi rộng cho đối tượng(post)
	 */
	public function custom_columns_value( $column, $post_id ) {
		$quick_edit_nonce = wp_create_nonce('quick_edit_'.$post_id);

		switch ($column) {
			
			case 'tasks':
				$footer_content = get_post_meta($post_id, '_footer_content', 'no');
				$use_general_design_price = get_post_meta($post_id, '_use_general_design_price', 'no');
				$use_general_price = get_post_meta($post_id, '_use_general_price', 'no');
				?>
				
				<p>
					<label><input type="checkbox" class="change_footer_content" <?php checked( $footer_content, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Hiện nội dung dưới cuối?</label>
				</p>
				<p>
					<label><input type="checkbox" class="change_use_general_design_price" <?php checked( $use_general_design_price, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Dùng giá TK chung?</label>
				</p>
				<p>
					<label><input type="checkbox" class="change_use_general_price" <?php checked( $use_general_price, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Dùng giá ĐT chung?</label>
				</p>
				<?php
				break;

			case 'feature':
				
				$featured = get_post_meta($post_id, '_featured', 'no');
				$allow_order = get_post_meta($post_id, '_allow_order', 'no');
				$up_nha88 = fw_get_db_post_option($post_id, 'up_nha88', 'no');
				
				?>
				<p>
					<label><input type="checkbox" class="change_featured" <?php checked( $featured, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Nổi bật?</label>
				</p>
				<p>
					<label><input type="checkbox" class="change_allow_order" <?php checked( $allow_order, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Chọn mẫu?</label>
				</p>
				<p>
					<label><input type="checkbox" class="change_up_nha88" <?php checked( $up_nha88, 'yes', true); ?> data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>"> Up Nha88?</label>
				</p>
				<?php
				break;
			
			case 'dimensions':
				$_breadth = floatval(get_post_meta($post_id, '_breadth', true));
				$_length = floatval(get_post_meta($post_id, '_length', true));
				$_area_1 = floatval(get_post_meta($post_id, '_area_1', true));
				$_floors = floatval(get_post_meta($post_id, '_floors', true));

				if($_breadth==0) $_breadth = '';
				if($_length==0) $_length = '';
				if($_area_1==0) $_area_1 = '';
				if($_floors==0) $_floors = '';
				?>
				<label><span>Mặt tiền:</span><input type="text" class="quick-edit-field _breadth" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_breadth)?>"><span>m</span></label>
				<label><span>Chiều sâu:</span><input type="text" class="quick-edit-field _length" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_length)?>"><span>m</span></label>
				<label><span>DT 1 sàn:</span><input type="text" class="quick-edit-field _area_1" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_area_1)?>"><span>m2</span></label>
				<label><span>Số tầng:</span><input type="text" class="quick-edit-field _floors" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_floors)?>"></label>
				<?php
				break;
			case 'costs':
				$_design_price = get_post_meta($post_id, '_design_price', true);
				$_price = get_post_meta($post_id, '_price', true);
				$_total_factor = get_post_meta($post_id, '_total_factor', true);

				?>
				<label><span>Giá thiết kế:</span><input type="text" class="quick-edit-field _design_price" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_design_price)?>"><span>k/m2</span></label>
				<label><span>Giá đầu tư:</span><input type="text" class="quick-edit-field _price" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_price)?>"><span>k/m2</span></label>
				<label><span>Hệ số đầu tư:</span><input type="text" class="quick-edit-field _total_factor" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>" value="<?=esc_attr($_total_factor)?>"><span></span></label>
				<?php
				break;

			case 'views':
				if(class_exists('WP_Statistics')) {
					echo absint(wp_statistics_pages('total', '', $post_id));
				}
				break;

			case 'ID':
				$prefix = '';
				switch (strtolower($_SERVER['HTTP_HOST'])) {
					case 'transonarchi.com':
						$prefix = 'HD';
						break;
					
					case 'ktstranson.com':
						$prefix = 'TC';
						break;
				}
				echo esc_html($prefix.$post_id);
				break;
			case 'RID':
				echo esc_html(fw_get_db_post_option($post_id, '_ref'));
				break;

			case 'order_count':
				echo absint(get_post_meta( $post_id, '_order_count', true ));
				break;

			case 'slider':
				$_images = get_post_meta( $post_id, '_images', true );
				if(empty($_images)) {
					echo 'Chưa có ảnh slide';
				}
				break;
			case 'format':
				$video_local = fw_get_db_post_option($post_id, 'video');
				$video_url = fw_get_db_post_option($post_id, 'video_url');
				$video_youtube = fw_get_db_post_option($post_id, 'video_youtube');
				$_images = get_post_meta($post_id, '_images', true);

				if($video_youtube!='' || $video_url!='' || !empty($video_local)) {
					echo 'Video';
				} else if(!empty($_images)) {
					echo 'Slide ảnh';
				} else {
					echo 'Văn bản';
				}
				break;
		}
		
	}

	/**
	 * Tiêu đề các cột thông tin mở rộng cho đối tượng(post)
	 */
	public function add_custom_columns_header( $columns ) {
		
		if(isset($columns['tags'])) {
			unset($columns['tags']);
		}

		$columns['format'] = 'Loại nội dung';
		$columns['ID'] = 'ID';
		$columns['RID'] = 'RID';
		$columns['feature'] = 'Đặc tính';
		$columns['tasks'] = 'Tác vụ';
		$columns['dimensions'] = 'Kích thước';
		$columns['costs'] = 'Chi phí';

		if(class_exists('WP_Statistics')) {
			$columns['views'] = 'Lượt xem';
		}

		$columns['order_count'] = 'Lượt gửi sđt';
		//$columns['slider'] = 'Slider';
		
		return $columns;

	}

	public function admin_head_post_styles() {
		global $post_type;
		?>
		<style type="text/css">
			<?php if('post' === $post_type){ ?>
				#edit-slug-box {
					display: none;
				}
			<?php } ?>

			<?php if('post' === $post_type || 'page' === $post_type || 'seo_post' === $post_type || 'contractor' === $post_type || 'contractor_page' === $post_type){ ?>
				#ajax-save {
					float:right;
					margin-top:-5px;
					margin-right:10px;
				}
			<?php } ?>
		</style>
		<?php
	}

	/**
	 * 
	 */
	public function admin_footer_post_scripts() {
		global $post_type;
		
		if('post' === $post_type || 'page' === $post_type || 'seo_post' === $post_type || 'contractor_page' === $post_type){
		?>
		<script type="text/javascript">
			let post_type = '<?=$post_type?>';
			(function ($, fwe) {

				function ajax_save() {
					var post_new_status = $('#post_status').val();
					var post_status = $('#original_post_status').val();
					
					var button1 = $('#publish');

					if(post_status!='private' && post_status!=post_new_status) {
						return true;
					}

					var button2 = $('#ajax-save');
					var postURL = '<?=admin_url('post.php')?>';

                    //Collate all post form data
                    var data = $('form#post').serializeArray();

                    //Set a trigger for our save_post action
                    data.push({name:'ajax_save_post', value: 1});
                    
                    var btntext1 = button1.val();
                    
                    var btntext2 = button2.val();
                    button1.val('Đang lưu..').prop( "disabled", true );
                    button2.val('Đang lưu..').prop( "disabled", true );
                    
                    $.ajax({
                    	url:postURL,
                    	type: 'POST',
                    	data:data,
                    	error: function(xhr) {
                    		alert('Lỗi! Thay đổi chưa được lưu.');
                    	},
                    	complete:function(xhr) {
							button1.val(btntext1).prop( "disabled", false );
							button2.val(btntext2).prop( "disabled", false );
                    	}
                    })
                   
					return false;
				}
				
				fwe.on('fw:option-type:builder:init', function (data) {
					var fw_builder_header_tools = $('.fw-builder-header-tools');
					if(fw_builder_header_tools.length>0) {
						fw_builder_header_tools.append('<input type="submit" name="ajax_save" id="ajax-save" class="button button-primary" value="'+($('#publish').attr('name')=='publish'?'Đăng':'Cập nhật')+'">');
					}
				});

				$(document).on('click', '#ajax-save', function(e){
					ajax_save();
					return false;
				});

				<?php if('post' === $post_type) { ?>
					$('<input id="find-categorychecklist" type="text" placeholder="Tìm..." style="width:100%">').insertBefore($('#category-tabs'));
					$(document).on('keyup', '#find-categorychecklist', function(){
						let that = $(this),
							s = that.val().toLowerCase();
						$('#categorychecklist li').filter(function() {
							$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
						});
					});

					$('<input id="find-locationchecklist" type="text" placeholder="Tìm..." style="width:100%">').insertBefore($('#location-tabs'));
					$(document).on('keyup', '#find-locationchecklist', function(){
						let that = $(this),
							s = that.val().toLowerCase();
						$('#locationchecklist li').filter(function() {
							$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
						});
					});
				<?php } ?>
			})(jQuery, fwEvents);
		</script>
		<?php
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Post::instance();