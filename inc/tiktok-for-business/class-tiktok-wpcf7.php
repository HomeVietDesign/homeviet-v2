<?php
namespace HomeViet;

final class Tiktok_Wpcf7 {

	private static $instance = null;

	private function __construct() {
	
	}

	public static function track( $order_data ) {
		
		$return = [
			'event_data' => [],
		];

		$fields = \Tt4b_Pixel_Class::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return $return;
		}

		$content_id = ''.$order_data['id'];
		$event_id   = uniqid($content_id.'_');

		// Lấy ttclid từ cookie
		$ttclid = isset($_COOKIE[\Tt4b_Pixel_Class::TTCLID_COOKIE]) ? sanitize_text_field($_COOKIE[\Tt4b_Pixel_Class::TTCLID_COOKIE]) : '';

		$hashed_phone = hash_sha256($order_data['phone']);
		$hashed_ip    = hash_sha256($order_data['ip_address']);

		$context = [
			'user' => [
				'phone' => [$hashed_phone],
				'ip'    => [$hashed_ip],
				'user_agent' => $order_data['user_agent']
			],
			'page' => [
				'url'      => esc_url_raw($order_data['url'])
			],
			'ad' => [
				'callback' => $ttclid // Gửi ttclid
			]
		];

		// Payload TikTok
		$payload = [
			'pixel_code' => $fields['pixel_code'],
			'event'      => 'AddToCart',
			'event_id'   => $event_id,
			'timestamp'  => date('c'),
			'context'    => $context,
			'properties' => [
				'contents' => [
					[
						'content_id' => $content_id,
						'quantity'   => 1,
						'price'      => 1
					]
				],
				'currency' => 'VND',
				'value'    => 1
			],
			//'test_event_code' => 'TEST91138'
		];

		$res = wp_remote_post('https://business-api.tiktok.com/open_api/v1.3/pixel/track/', [
			'headers' => [
				'Content-Type' => 'application/json',
				'Access-Token' => $fields['access_token']
			],
			'body' => json_encode($payload)
		]);

		if($res instanceof \WP_Error) return $return;

		$body = json_decode(wp_remote_retrieve_body( $res ), true);

		if($body['message']!='OK') return $return;

		$event_data = [
			'event_name' => 'AddToCart',
			'event_time' => time(),
			'event_source_url' => $order_data['url'],
			'event_id'=>$event_id,
			//'user'=>$user,
			'context'=>$context,
		];

		$return['event_data'] = $event_data;

		return $return;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Tiktok_Wpcf7::instance();