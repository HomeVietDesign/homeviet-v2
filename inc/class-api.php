<?php
namespace HomeViet;

class API {

	private static $instance = null;

	private function __construct() {
		add_action( 'rest_api_init', [__CLASS__, 'rest_api_init'] );
	}

	public static function rest_api_init() {
		register_rest_route('theme-api', '/timer_event', [
			'methods' => 'POST',
			'callback' => [__CLASS__, 'send_timer_event'],
			'permission_callback' => '__return_true',
		]);
	}

	public static function send_timer_event($request) {
		$data = json_decode($request->get_body(), true);

		$response = apply_filters( 'track_timer', ['event' => $data['event'], 'fb_pxl_code'=>''] );

		return new \WP_REST_Response($response, 200);
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
API::instance();