<?php
namespace HomeViet;

class Admin_Contractor_Cat {

	private static $instance = null;

	private function __construct() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

			//add_action( 'saved_contractor_cat', [$this, 'auto_slug'] );
			add_action( 'created_contractor_cat', [$this, 'auto_slug'] );

			add_filter( 'manage_edit-contractor_cat_columns', [$this, 'manage_edit_column_header'] );
			add_action( 'manage_contractor_cat_custom_column', [$this, 'manage_edit_columns_value'], 15, 3 );

			// đồng bộ hạng mục với trang nhà thầu
			add_action( 'created_contractor_cat', [$this, 'auto_create_term_page'] );
			add_action( 'edited_contractor_cat', [$this, 'auto_update_term_page'] );
			add_action( 'delete_contractor_cat', [$this, 'auto_delete_term_page'] );

			add_action( 'update_term_meta', [$this, 'set_contractor_page_order'], 10, 4 );

			add_action( 'wp_ajax_change_contractor_page', [$this, 'ajax_change_contractor_page'] );
			add_action( 'wp_ajax_admin_change_contractor_cat', [$this, 'ajax_admin_change_contractor_cat'] );
			add_action( 'wp_ajax_admin_get_contractor_cat_parents', [$this, 'ajax_admin_get_contractor_cat_parents'] );
			add_action( 'wp_ajax_admin_change_contractor_cat_note', [$this, 'ajax_admin_change_contractor_cat_note'] );

			add_filter( 'terms_clauses', [$this, 'filter_list_table_get_terms'], 10, 3 );

		}
	}

	public function filter_list_table_get_terms($pieces, $taxonomies, $args) {

		if(is_array($taxonomies) && in_array('contractor_cat', $taxonomies) && isset($args['page'])) {
			//debug_log($pieces);
			$parent_only = isset($_GET['parent_only']) ? true : false;
			$parent = isset($_GET['parent'])?absint($_GET['parent']):0;

			if($parent>0 && $parent_only) {
				$pieces['where'] .=  " AND tt.parent = '0' AND t.term_id = '".$parent."'";
			} elseif($parent_only) {
				$pieces['where'] .=  " AND tt.parent = '0'";
			} elseif($parent>0) {
				$pieces['where'] .=  " AND (tt.parent = '".$parent."' OR t.term_id = '".$parent."')";
				//$pieces['where'] .=  " AND tt.parent = '".$parent."'";
			}
	
		}

		return $pieces;
	}

	public function set_contractor_page_order($meta_id, $object_id, $meta_key, $_meta_value) {
		
		if($meta_key=='order') {
			$contractor_cat = get_term_by( 'term_id', $object_id, 'contractor_cat' );
			if($contractor_cat) {
				$term_page_id = absint(get_term_meta( $contractor_cat->term_id, '_page', true ));
				if($term_page_id) {
					global $wpdb;
					$wpdb->update( $wpdb->posts, ['menu_order'=>$_meta_value], ['ID' => $term_page_id] );
					wp_cache_delete( $term_page_id, 'posts' );
				}
			}
		}
	}

	public function auto_delete_term_page($term_id) {
		$term_pages = get_posts([
			'post_type'=>'contractor_page',
			'numberposts'=>-1,
			'meta_key' => '_cat',
			'meta_value' => $term_id,
			'fields'=>'ids',
			'suppress_filter' => true
		]);
		if($term_pages) {
			foreach ($term_pages as $term_page_id) {
				wp_delete_post( $term_page_id, true );
			}
		}
	}

	public function auto_update_term_page($term_id) {
		$term_page_id = absint(get_term_meta($term_id, '_page', true));
		$term_page = get_post($term_page_id);
		//debug_log($term_page);
		if($term_page) {
			$term = get_term_by( 'term_id', $term_id, 'contractor_cat' );
				
			$update_data['post_parent'] = ($term->parent==0)?0:absint(get_term_meta($term->parent, '_page', true));
			
			if($term_page->post_title!=$term->name) {
				$update_data['post_title'] = $term->name;
			}

			global $wpdb;
			$wpdb->update( $wpdb->posts, $update_data, ['ID' => $term_page_id] );
			wp_cache_delete( $term_page_id, 'posts' );

		} else {
			$this->auto_create_term_page($term_id);
		}
	}
	
	public function auto_create_term_page($term_id) {
		$term = get_term_by( 'term_id', $term_id, 'contractor_cat' );
		$term_page_parent_id = absint(get_term_meta($term->parent, '_page', true));
		$term_page_id = wp_insert_post([
			'post_type' => 'contractor_page',
			'post_title' => $term->name,
			'post_name' => $term->slug,
			'post_parent' => $term_page_parent_id,
			'post_status' => 'publish'
		]);
		if( !($term_page_id instanceof \WP_Error) && $term_page_id>0 ) {
			update_term_meta($term_id, '_page', $term_page_id);
			update_post_meta($term_page_id, '_cat', $term_id);
		}
			
	}

	public function ajax_admin_change_contractor_cat_note() {
		$term_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$note = isset($_REQUEST['note']) ? sanitize_textarea_field($_REQUEST['note']) : '';

		$response = get_term_field('description', $term_id, 'contractor_cat', 'raw');

		if( check_ajax_referer('quick_edit_'.$term_id, 'nonce', false) && current_user_can('edit_term', $term_id) ) {
			
			wp_update_term( $term_id, 'contractor_cat', ['description'=>$note] );
			
			wp_cache_delete($term_id, 'terms');

			$response = $note;
		}
		wp_send_json($response);
	}

	public function ajax_change_contractor_page() {
		$term_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$_page = isset($_REQUEST['_page']) ? absint($_REQUEST['_page']) : 0;

		$response = false;

		if( check_ajax_referer('quick_edit_'.$term_id, 'nonce', false) && current_user_can('edit_term', $term_id) ) {
			update_term_meta($term_id, '_page', $_page);
			wp_cache_delete($term_id, 'terms');

			$response = true;
		}
		wp_send_json($response);
	}

	public function ajax_admin_get_contractor_cat_parents() {
		$parent = isset($_REQUEST['parent']) ? absint($_REQUEST['parent']) : 0;
		echo '<div class="alignleft">';
		wp_dropdown_categories([
			'taxonomy' => 'contractor_cat',
			'show_option_none' => '-- Cấp trên là --',
			'option_none_value' => 0,
			'depth' => 1,
			'class' => 'filter-parent',
			'name' => 'parent',
			'hierarchical' => true,
			'hide_empty' => false,
			'id' => 'cat-parent-dropdown',
			'selected' => $parent,

		]);
		echo '</div>';
		exit;
	}

	public function ajax_admin_change_contractor_cat() {
		$term_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$parent = isset($_REQUEST['parent']) ? absint($_REQUEST['parent']) : 0;

		$response = false;

		if( check_ajax_referer('quick_edit_'.$term_id, 'nonce', false) && current_user_can('edit_term', $term_id) ) {
			
			wp_update_term( $term_id, 'contractor_cat', ['parent'=>$parent] );

			wp_cache_delete($term_id, 'terms');

			//debug_log($term_id);

			$response = true;

		}
		wp_send_json($response);
	}

	public function manage_edit_columns_value($row, $column_name, $term_id) {
		$nonce = wp_create_nonce('quick_edit_'.$term_id);
		$parent = wp_get_term_taxonomy_parent_id( $term_id, 'contractor_cat' );
		if( 'order' === $column_name ) {
			echo '<input type="number" value="'.intval(get_term_meta($term_id, 'order', true)).'">';
		}
		else if('parent_cat'==$column_name) {
			echo '<div>';
			wp_dropdown_categories([
				'taxonomy' => 'contractor_cat',
				'show_option_none' => '-- Không có --',
				'option_none_value' => 0,
				'class' => 'change-parent',
				'name' => 'contractor_cat-parent',
				'hierarchical' => true,
				'hide_empty' => false,
				'id' => 'cat-'.$term_id,
				'aria_describedby' => $nonce,
				'exclude' => $term_id,
				'selected' => $parent,

			]);
			echo '</div>';
		}	
		else if('page'==$column_name) {
			$contractor_page_id = absint(get_term_meta($term_id, '_page', true));
			$contractor_page = get_post($contractor_page_id);
			if($contractor_page) {
				echo '<p><a style="display:inline-block; margin-right: 15px" href="'.esc_url(get_permalink($contractor_page)).'" target="_blank">Xem trang</a><span style="display:inline-block; margin-right: 15px">|</span><a style="display:inline-block; margin-right: 15px" href="'.esc_url(get_edit_post_link( $contractor_page )).'" target="_blank">Sửa trang</a></p>';
			}
			?>
			<!-- <input type="number" class="_page" data-nonce="<?=esc_attr($nonce)?>" data-id="<?=$term_id?>" value="<?=$contractor_page_id?>" style="width: 100%;"> -->
			<?php
		}
		else if('term_id'==$column_name) {
			echo esc_html($term_id);
		}
		else if('note'==$column_name) {
			?>
			<textarea class="term-note" rows="3" data-nonce="<?=esc_attr($nonce)?>" data-id="<?=$term_id?>"><?=esc_textarea( get_term_field( 'description', $term_id, 'contractor_cat', 'raw' ) )?></textarea>
			<?php
		}
	}

	public function manage_edit_column_header($columns) {
		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}
		if(isset($columns['description'])) {
			unset($columns['description']);
			$columns['note'] = 'Ghi chú';
		}
		if(isset($columns['posts'])) {
			unset($columns['posts']);
		}
		//$columns['term_id'] = 'ID';
		$columns['parent_cat'] = 'Cấp trên';
		$columns['page'] = 'Trang nhà thầu';
		//$columns['order'] = 'STT';
		
		return $columns;
	}

	public function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => current_time( 'U' )], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public function enqueue_scripts($hook) {
		global $taxonomy;
		// debug_log($hook);
		// debug_log($taxonomy);
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='contractor_cat') {
			wp_enqueue_style( 'manage-contractor_cat', THEME_URI.'/assets/css/manage-contractor_cat.css', [], '' );
			wp_enqueue_script('manage-contractor_cat', THEME_URI.'/assets/js/manage-contractor_cat.js', array('jquery'), '');
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Contractor_Cat::instance();