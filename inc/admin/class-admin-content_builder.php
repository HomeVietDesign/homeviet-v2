<?php
namespace HomeViet;

class Admin_Content_Builder {

	private static $instance = null;

	private function __construct() {

		if(is_admin()) {	

		}

		
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Content_Builder::instance();