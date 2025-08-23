<?php
namespace HomeViet;

class Admin_Price {

	private static $instance = null;


	private function __construct() {
		if(is_admin()) {
			
			require_once THEME_DIR.'/inc/admin/class-walker-price-checklist.php';

			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

			add_action( 'created_price', [$this, 'auto_slug'] );

			add_filter('manage_edit-price_columns', [$this, 'custom_column_header']);
			//add_filter('manage_price_custom_column', [$this, 'custom_column_value'], 10, 3);

			add_filter( 'wp_terms_checklist_args', [$this, 'change_check_list'], 10, 1 );
			add_filter( 'post_column_taxonomy_links', [$this, 'change_columns_links'], 10, 3 );

			
		}
	}

	public function change_columns_links($term_links, $taxonomy, $terms) {

		if($taxonomy=='price') {

			foreach ( $term_links as $key => &$link) {
				$link = preg_replace('/(<a[^>]*>)([^<]*)(<\/a>)/', "$1".esc_html($terms[$key]->name.': '.$terms[$key]->description.'')."$3", $link);
			}

		}

		return $term_links;	
	}

	public function change_check_list($args) {
		//debug_log($args);
		if($args['taxonomy']=='price') {
			$args['walker'] = new \Walker_Price_Checklist();
		}

		return $args;
	}

	public function custom_column_value($value, $column_name, $term_id) {

	}

	public function custom_column_header($columns) {
		if(isset($columns['name'])) {
			$columns['name'] = 'Tên nhóm';
		}
		if(isset($columns['description'])) {
			$columns['description'] = 'Giá trị nhiển thị';
		}
		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}
		if(isset($columns['posts'])) {
			unset($columns['posts']);
		}
		
		return $columns;
	}

	public function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => 'price-'.$term_id], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public function enqueue_scripts($hook) {
		global $taxonomy;
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='price') {
			wp_enqueue_style( 'manage-price', THEME_URI.'/assets/css/manage-price.css', [], '' );
			wp_enqueue_script('manage-price', THEME_URI.'/assets/js/manage-price.js', array('jquery'), '');
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Price::instance();