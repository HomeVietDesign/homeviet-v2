<?php
namespace HomeViet;

class Admin_Province {
	
	private static $instance = null;

	protected function __construct() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
			add_filter( 'manage_edit-province_columns', [$this, 'manage_edit_column_header'] );
			add_action( 'manage_province_custom_column', [$this, 'manage_edit_columns_value'], 15, 3 );

			add_action( 'created_province', [$this, 'auto_slug'] );
		}
	}

	public function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => current_time( 'U' )], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
	}

	public function manage_edit_columns_value($row, $column_name, $term_id) {
		if($column_name=='stt') {
			echo intval(get_term_meta($term_id, 'order', true));
		}
	}

	public function manage_edit_column_header($columns) {
		if(isset($columns['description'])) {
			unset($columns['description']);
		}
		if(isset($columns['slug'])) {
			unset($columns['slug']);
		}
		if(isset($columns['posts'])) {
			$columns['posts'] = 'Đếm';
		}
		$columns['stt'] = 'STT';
		return $columns;
	}

	public function enqueue_scripts($hook) {
		global $taxonomy;
		// debug_log($hook);
		// debug_log($taxonomy);
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='province') {
			wp_enqueue_style( 'manage-province', THEME_URI.'/assets/css/manage-province.css', [], '' );
			wp_enqueue_script('manage-province', THEME_URI.'/assets/js/manage-province.js', array('jquery'), '');
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}
Admin_Province::instance();