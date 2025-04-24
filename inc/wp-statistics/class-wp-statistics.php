<?php
namespace HomeViet;

/**
 * 
 */
class WP_Statistics {
	
	private static $instance = null;

	private function __construct() {
		
		add_filter( 'wp_statistics_metabox_list', [$this, 'wp_statistics_meta_box_class'], 10, 1 );
	}

	public function wp_statistics_meta_box_class($meta_boxes) {
		
		//debug_log($meta_boxes);
		
		$key = array_search('WP_Statistics\Service\Admin\Metabox\Metaboxes\PostVisitorsLocked', $meta_boxes);
		if($key!==false) {
			unset($meta_boxes[$key]);
		}

		return $meta_boxes;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

WP_Statistics::instance();