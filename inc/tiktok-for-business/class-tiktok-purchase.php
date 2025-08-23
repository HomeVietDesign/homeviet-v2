<?php
namespace HomeViet;

final class Tiktok_Purchase {

	private static $instance = null;

	private function __construct() {
	
	}

	 public static function track($events) {
        //debug_log($data);

        $fields = \Tt4b_Pixel_Class::pixel_event_tracking_field_track( __METHOD__ );
		if ( 0 === count( $fields ) ) {
			return;
		}

		if(isset($events['event_data']['tt']) && !empty($events['event_data']['tt'])) {

			$content_id = ''.$events['order_data']['id'];

			$event_id   = uniqid($content_id.'_');

			// Payload TikTok
			$payload = [
				'pixel_code' => $fields['pixel_code'],
				'event'      => 'Purchase',
				'event_id'   => $event_id,
				'timestamp'  => date('c'),
				'context'    => $events['event_data']['tt']['context'],
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

			$body = wp_remote_retrieve_body( $res );

			//debug_log($body);
		
	    }
    }

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
