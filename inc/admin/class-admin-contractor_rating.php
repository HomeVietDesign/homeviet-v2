<?php
namespace HomeViet;

class Admin_Contractor_Rating {
	
	private static $instance = null;

	protected function __construct() {
		if(is_admin()) {
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
			add_filter( 'manage_edit-contractor_rating_columns', [$this, 'manage_edit_column_header'] );

			add_action( 'created_contractor_rating', [$this, 'auto_slug'] );
		}
	}

	public function auto_slug($term_id) {
		global $wpdb;
		$wpdb->update( $wpdb->terms, ['slug' => current_time( 'U' )], ['term_id' => $term_id] );
		wp_cache_delete( $term_id, 'terms' );
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
		return $columns;
	}

	public function enqueue_scripts($hook) {
		global $taxonomy;
		// debug_log($hook);
		// debug_log($taxonomy);
		if(($hook=='edit-tags.php' || $hook=='term.php') && $taxonomy=='contractor_rating') {
			wp_enqueue_style( 'manage-contractor_rating', THEME_URI.'/assets/css/manage-contractor_rating.css', [], '' );
			wp_enqueue_script('manage-contractor_rating', THEME_URI.'/assets/js/manage-contractor_rating.js', array('jquery'), '');
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}
Admin_Contractor_Rating::instance();