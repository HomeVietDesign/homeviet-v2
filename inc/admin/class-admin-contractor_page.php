<?php
namespace HomeViet;

class Admin_Contractor_Page {

	private static $instance = null;

	private function __construct() {

		if(is_admin()) {	
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

			add_filter( 'disable_months_dropdown', [$this, 'disable_months_dropdown'], 10, 2 );

			add_action( 'add_meta_boxes', [$this, 'switch_boxes'] );

			add_action( 'manage_contractor_page_posts_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_contractor_page_posts_columns', [ $this, 'add_custom_columns_header' ] );

			add_action( 'wp_ajax_change_contractor_cat', [$this, 'ajax_change_contractor_cat'] );

			add_action( 'save_post_contractor_page', [$this, 'save_contractor_page_10'], 10, 3 );
		}

	}

	public function switch_boxes() {

		remove_meta_box(
			'tagsdiv-passwords',
			'contractor_page',
			'side'
		);


	}

	public function save_contractor_page_10($post_id, $post, $update) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$term_id = absint(get_post_meta( $post_id, '_cat', true ));
		$term = get_term_by( 'term_id', $term_id, 'contractor_cat' );
		if($term) {
			global $wpdb;
			
			if($term->name!=$post->post_title) {
				$wpdb->update( $wpdb->terms, ['name' => $post->post_title], ['term_id' => $term_id] );
			}

			$parent = ($post->post_parent==0)?0:absint(get_post_meta($post->post_parent, '_cat', true));
			
			$tt_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT tt.term_taxonomy_id FROM $wpdb->term_taxonomy AS tt INNER JOIN $wpdb->terms AS t ON tt.term_id = t.term_id WHERE tt.taxonomy = %s AND t.term_id = %d", $term->taxonomy, $term_id ) );
		
			$wpdb->update( $wpdb->term_taxonomy, ['parent' => $parent], ['term_taxonomy_id' => $tt_id] );

			wp_cache_delete( $term_id, 'terms' );
		}
	}

	public function ajax_change_contractor_cat() {
		$post_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$cat = isset($_REQUEST['cat']) ? absint($_REQUEST['cat']) : 0;

		$response = false;

		if( check_ajax_referer('quick_edit_'.$post_id, 'nonce', false) && current_user_can('edit_post', $post_id) ) {
			update_post_meta($post_id, '_cat', $cat);
			wp_cache_delete($post_id, 'posts');

			$response = true;
		}
		wp_send_json($response);
	}

	public function custom_columns_value($column, $post_id) {
		$nonce = wp_create_nonce('quick_edit_'.$post_id);

		switch ($column) {
			case 'contractor_cat':
				$contractor_cat_id = absint(get_post_meta( $post_id, '_cat', true ));
				?>
				<!-- <input type="number" class="_cat" data-nonce="<?=esc_attr($nonce)?>" data-id="<?=$post_id?>" value="<?=$contractor_cat_id?>" style="width: 100%;"> -->
				<?php
				$contractor_cat = get_term_by( 'term_id', $contractor_cat_id, 'contractor_cat' );
				if($contractor_cat) echo esc_html($contractor_cat->name.'('.$contractor_cat_id.')');
				break;
			case 'menu_order':
				$post = get_post($post_id);
				echo $post->menu_order;
				break;
			case 'page_id':
				echo $post_id;
				break;
		}
	}

	public function add_custom_columns_header($columns) {
		// $cb = $columns['cb'];
		// unset($columns['cb']);
		// $title = $columns['title'];
		// unset($columns['title']);

		// $new_columns = [
		// 	'cb' => $cb,
		// 	'title' => $title,
		// 	'contractor_cat' => 'Hạng mục',
		// 	'menu_order' => 'STT',
		// ];

		// $columns = array_merge($new_columns, $columns);

		// $columns['page_id'] = 'ID';

		$columns['contractor_cat'] = 'Hạng mục';
		$columns['menu_order'] = 'STT';

		return $columns;

	}
	
	public function disable_months_dropdown($disabled, $post_type) {
		if($post_type=='contractor_page') {
			$disabled = true;
		}
		return $disabled;
	}

	public function enqueue_scripts($hook) {
		global $post_type;
		// debug_log($hook);
		// debug_log($post_type);
		if($hook=='edit.php' && $post_type=='contractor_page') {
			wp_enqueue_style( 'edit-contractor_page', THEME_URI.'/assets/css/edit-contractor_page.css', [], '' );
			wp_enqueue_script('edit-contractor_page', THEME_URI.'/assets/js/edit-contractor_page.js', array('jquery'), '');
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Contractor_Page::instance();