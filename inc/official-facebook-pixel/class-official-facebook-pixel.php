<?php
namespace HomeViet;

final class Official_Facebook_Pixel {

	private static $instance = null;

	private function __construct() {
		add_action( 'init', [$this, 'remove_ofp_wpcf7_integration'], 10 );
		
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-wpcf7.php';
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-order.php';
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-eclick.php';
		include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-apply-position.php';
	}

	public function remove_ofp_wpcf7_integration() {
		// global $wp_filter;
		// debug_log($wp_filter);

		remove_action(
            'wpcf7_submit',
            array( 'FacebookPixelPlugin\Integration\FacebookWordpressContactForm7', 'trackServerEvent' ),
            10,
            2
        );

        remove_action(
            'wp_footer',
            array( 'FacebookPixelPlugin\Integration\FacebookWordpressContactForm7', 'injectMailSentListener' ),
            10,
            2
        );
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Official_Facebook_Pixel::instance();