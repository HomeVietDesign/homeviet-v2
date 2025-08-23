<?php
namespace HomeViet;

final class Tiktok_For_Business {

	private static $instance = null;

	private function __construct() {
		
		//include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-order.php';
		//include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-wpcf7.php';
		//include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-purchase.php';
	
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Tiktok_For_Business::instance();