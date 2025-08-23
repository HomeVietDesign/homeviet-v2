<?php
namespace HomeViet;

class API {

	private static $instance = null;

	private function __construct() {
		add_action( 'rest_api_init', [__CLASS__, 'rest_api_init'] );
	}

	public static function rest_api_init() {
		register_rest_route('theme-api/v1', '/onsite', [
			'methods'  => 'POST',
			'callback' => [__CLASS__, 'handle_onsite_event'],
			'permission_callback' => '__return_true',
		]);
	}

	public static function handle_onsite_event( \WP_REST_Request $request ) {
		global $wpdb;

		$fb_pixel = fw_get_db_settings_option('fb_pixel');
		$fb_pixel_at = fw_get_db_settings_option('fb_pixel_at');

		$uid      = sanitize_text_field($request->get_param('uid'));
		$duration = intval($request->get_param('duration'));
		$fbc      = sanitize_text_field($request->get_param('fbc'));
		$fbp      = sanitize_text_field($request->get_param('fbp'));
		$url      = sanitize_url($request->get_param('url'));

		$ip       = get_user_ip_address();
		$ua       = get_user_agent();
		$today    = date('Y-m-d');

		$table = $wpdb->prefix . "fb_event_logs";

		$event_name = "Onsite{$duration}s";

		// Kiểm tra trong ngày đã tồn tại chưa
		$exists = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(*) FROM $table WHERE uid=%s AND duration=%d AND event_date=%s",
		$uid, $duration, $today
		));

		if (!$exists) {

			// Ghi log
			$wpdb->insert($table, [
			    'uid'        => $uid,
			    'ip'         => $ip,
			    'ua'         => $ua,
			    'event_name' => $event_name,
			    'duration'   => $duration,
			    'event_date' => $today,
			    'fbc'        => $fbc,
			    'fbp'        => $fbp,
			    'url'        => $url,
			]);

			// Gửi sự kiện CAPI
			$event = [
			    "event_name" => $event_name,
			    "event_time" => time(),
			    "action_source" => "website",
			    "event_source_url" => $url,
			    "user_data" => [
			        "client_ip_address" => $ip,
			        "client_user_agent" => $ua,
			        "fbc" => $fbc,
			        "fbp" => $fbp
			    ],
			    "custom_data" => [
			        "duration" => $duration
			    ]
			];

			$payload = [
			    "data" => [ $event ],
			    //"test_event_code" => "TEST54379" // bỏ khi chạy thật
			];

			$response = wp_remote_post("https://graph.facebook.com/v23.0/$fb_pixel/events?access_token=$fb_pixel_at", [
			    'headers' => ['Content-Type' => 'application/json'],
			    'body'    => wp_json_encode($payload),
			]);
		}

		return [ "status" => "ok" ];
	}
	
	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
API::instance();