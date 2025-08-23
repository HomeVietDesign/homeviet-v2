<?php
namespace HomeViet;

final class Ads {

	private static $instance = null;

	private function __construct() {

		add_action( 'wp_footer', [$this, 'footer_event_scripts'], 100 );

		add_filter( 'order_submit', [$this, 'track_order_submit'] );
		//add_action( 'wp_footer', [$this, 'inject_order_product_listener'], 10 );

		add_action( 'wpcf7_submit', [$this, 'track_wpcf7_submit'], 10, 2 );
		//add_action( 'wp_footer', [$this, 'inject_mail_sent_listener'], 10, 2 );
		
		add_filter( 'apply_position', [$this, 'track_apply_position'] );

		add_action( 'purchase', [$this, 'track_purchase'] );
	}

	public static function footer_event_scripts() {
		?>
		<script>
		document.addEventListener("DOMContentLoaded", function() {
			if(theme.is_user_logged_in=='1') return;

			if (!getCookie("_uid")) {
				// tương tự như cookie "fb_event_" + dur nhưng là xử lý ở server cơ sở dữ liệu
				setCookie("_uid", Math.random().toString(36).substring(2) + Date.now(), 180);
			}

			const durations = [60]; // giây

			durations.forEach(dur => {

				setTimeout(function() {
					if(!getCookie("fb_event_" + dur)) { // nếu chưa gửi thì mới gửi
						fetch("<?php echo site_url('/wp-json/theme-api/v1/onsite'); ?>", {
							method: "POST",
							headers: { "Content-Type": "application/json" },
							body: JSON.stringify({
								uid: getCookie("_uid"),
								duration: dur,
								fbc: getCookie("_fbc"),
								fbp: getCookie("_fbp"),
								url: window.location.href
							})
						})
						.then(response => {
							if (!response.ok) {
								throw new Error("Network response was not ok " + response.statusText);
							}
							setTodayCookie("fb_event_" + dur, 1, 1);

							return response.json(); // chuyển kết quả thành JSON
						});
						// .then(data => {
						// 	console.log("Kết quả thành công:", data);
						// });
					}
					// else {
					// 	console.log("Event Onsite"+dur+"s already sent today");
					// }
					
				}, dur * 1000);

			});
		});
		</script>
		<?php
	}

	public static function track_apply_position($response) {

		$event_data = [
			// Lấy cookie _fbc và _fbp
			'phone' => $response['data']['phone'],
			'fbc' => isset($_COOKIE['_fbc']) ? sanitize_text_field($_COOKIE['_fbc']) : '',
			'fbp' => isset($_COOKIE['_fbp']) ? sanitize_text_field($_COOKIE['_fbp']) : '',
			'user_agent' => get_user_agent(),
			'ip_address' => get_user_ip_address(),
			'url' => home_url()
		];

		self::fb_send_event('Ứng tuyển', $event_data, '');

		return $response;
	}


	public static function track_purchase($data) {
		$events = get_post_meta($data['id'], '_events', true);

		// if(class_exists('\\FacebookPixelPlugin\\FacebookForWordpress')) {
		// 	include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-purchase.php';

		// 	\FacebookPixelPlugin\Integration\FacebookPurchase::track($events);
		// }

		self::fb_send_event('Purchase', $events['event_data']['fb'], '');

		if(class_exists('TiktokForBusiness')) {
			include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-purchase.php';
			
			\HomeViet\Tiktok_Purchase::track($events);

		}

		
		$datetime = get_the_date( 'Y-m-d H:i:s', $data['id'] );

		wp_update_post([
            'ID' => $data['id'],
            'post_status' => 'publish',
            'edit_date' => $datetime
        ]);

        update_post_meta( $data['id'], '_purchase', 1 );
	}

	public static function inject_mail_sent_listener() {
		ob_start();
	    ?>
	    <!-- Meta Pixel Event Code -->
	    <script type='text/javascript'>
	        document.addEventListener( 'wpcf7mailsent', function( event ) {
	        if( "fb_pxl_code" in event.detail.apiResponse){
	            eval(event.detail.apiResponse.fb_pxl_code);
	        }
	        }, false );
	    </script>
	    <!-- End Meta Pixel Event Code -->
        <?php
        $listener_code = ob_get_clean();
        echo $listener_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function track_wpcf7_submit( $form, $result ) {
		//debug_log($result);
		$submit_failed    = ('mail_sent' !== $result['status']);
		if ( $submit_failed ) {
			return;
		}

		$submission = \WPCF7_Submission::get_instance();
		
		$form_tags = $form->scan_form_tags();
        $form_data      = self::wpcf7_read_form_data( $form_tags );

		$events = [
			'order_data' => [
				'name' => $form_data['name'],
				'phone' => $form_data['phone'],
				'email' => $form_data['email'],
				'url' => $submission->get_meta('url'),
				'referrer' => base64_decode(isset($_COOKIE['_ref'])?$_COOKIE['_ref']:''),
				'user_agent' => get_user_agent(),
				'ip_address' => get_user_ip_address(),
				'image' => '',
				'product' => $form->name(),
				'type' => 'normal',
				'id' => $form->id(),
			],
			'event_data' => [
				'fb' => [
					// Lấy cookie _fbc và _fbp
					'phone' => $form_data['phone'],
					'fbc' => isset($_COOKIE['_fbc']) ? sanitize_text_field($_COOKIE['_fbc']) : '',
					'fbp' => isset($_COOKIE['_fbp']) ? sanitize_text_field($_COOKIE['_fbp']) : '',
					'user_agent' => get_user_agent(),
					'ip_address' => get_user_ip_address(),
					'url' => $submission->get_meta('url'),
				],
				'tt' => []
			]
		];

		self::fb_send_event('AddToCart', $events['event_data']['fb'], '');

		// if(class_exists('\\FacebookPixelPlugin\\FacebookForWordpress')) {
		// 	include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-wpcf7.php';

		// 	$fb_track = \FacebookPixelPlugin\Integration\FacebookWPCF7::track( $events['order_data'] );

		// 	$events['event_data']['fb'] = $fb_track['event_data'];
		// }

		if(class_exists('TiktokForBusiness')) {
			include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-wpcf7.php';

			$tt_track = \HomeViet\Tiktok_Wpcf7::track($events['order_data']);

			$events['event_data']['tt'] = $tt_track['event_data'];
		}

		if(function_exists('as_enqueue_async_action')) {
			as_enqueue_async_action('add_product_order', [$events], 'order');
		}

	}

	private static function wpcf7_read_form_data( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return array();
        }

        $name      = self::wpcf7_get_name( $form_tags );

        return array(
            'name'      => self::wpcf7_get_name( $form_tags ),
            'email'      => self::wpcf7_get_email( $form_tags ),
            'phone'      => self::wpcf7_get_phone( $form_tags ),
        );
    }

    private static function wpcf7_get_phone( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'tel' === $tag->basetype ) {
                return isset( $_POST[ $tag->name ] ) ? // phpcs:ignore WordPress.Security.NonceVerification.Missing
                '+'.phone_0284(sanitize_phone_number(
                    wp_unslash( $_POST[ $tag->name ] )) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                ) : null;
            }
        }

        return null;
    }

    private static function wpcf7_get_email( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'email' === $tag->basetype && isset( $_POST[ $tag->name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
                return sanitize_text_field( wp_unslash( $_POST[ $tag->name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
            }
        }

        return null;
    }

	private static function wpcf7_get_name( $form_tags ) {
        if ( empty( $form_tags ) ) {
            return null;
        }

        foreach ( $form_tags as $tag ) {
            if ( 'text' === $tag->basetype
            && strpos( strtolower( $tag->name ), 'your_name' ) !== false ) {
                return sanitize_text_field(
                        wp_unslash( $_POST[ $tag->name ] ?? null ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
                );
            }
        }

        return null;
    }

    public static function inject_order_product_listener() {
        ob_start();
	    ?>
	    <!-- Meta Pixel Event Code -->
	    <script type='text/javascript'>
	        document.addEventListener( 'orderProduct', function( event ) {
		        if( "fb_pxl_code" in event.detail){
	                if(event.detail.fb_pxl_code!='') {
		               eval(event.detail.fb_pxl_code);
	                }
		        }
	        }, false );
	    </script>
	    <!-- End Meta Pixel Event Code -->
        <?php
        $listener_code = ob_get_clean();
        echo $listener_code; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }

	public static function track_order_submit( $response ) {
		
		if($response['code']!=1) return $response;

		$events = [
			'order_data' => [
				'name' => $response['data']['name'],
				'phone' => $response['data']['phone'],
				'url' => $response['data']['url'],
				'referrer' => base64_decode($response['data']['ref']),
				'user_agent' => get_user_agent(),
				'ip_address' => get_user_ip_address(),
				'image' => $response['data']['image'],
				'type' => $response['data']['type'],
				'id' => $response['data']['id'],
			],
			'event_data' => [
				'fb' => [
					// Lấy cookie _fbc và _fbp
					'phone' => $response['data']['phone'],
					'fbc' => isset($_COOKIE['_fbc']) ? sanitize_text_field($_COOKIE['_fbc']) : '',
					'fbp' => isset($_COOKIE['_fbp']) ? sanitize_text_field($_COOKIE['_fbp']) : '',
					'user_agent' => get_user_agent(),
					'ip_address' => get_user_ip_address(),
					'url' => $response['data']['url'],
				],
				'tt' => []
			]
		];

		self::fb_send_event('AddToCart', $events['event_data']['fb'], '');

		// if(class_exists('\\FacebookPixelPlugin\\FacebookForWordpress')) {
		// 	include_once THEME_DIR.'/inc/official-facebook-pixel/class-facebook-order.php';

		// $fb_track = \FacebookPixelPlugin\Integration\FacebookOrder::track($events['order_data']);

		// 	$events['event_data']['fb'] = $fb_track['event_data'];

		// 	$response['fb_pxl_code'] = $fb_track['fb_pxl_code'];
		// }

		if(class_exists('TiktokForBusiness')) {
			include_once THEME_DIR.'/inc/tiktok-for-business/class-tiktok-order.php';

			$tt_track = \HomeViet\Tiktok_Order::track($events['order_data']);

			$events['event_data']['tt'] = $tt_track['event_data'];
		}

		if(function_exists('as_enqueue_async_action')) {
			as_enqueue_async_action('add_product_order', [$events], 'order');
		}

		return $response;
	}

	public static function init_ttclid() {
		if (isset($_GET['ttclid']) && !empty($_GET['ttclid'])) {
			setcookie('ttclid', sanitize_text_field($_GET['ttclid']), time() + 30 * DAY_IN_SECONDS, '/');
			$_COOKIE['ttclid'] = sanitize_text_field($_GET['ttclid']);
		}
	}

	public static function fb_send_event($event_name, $event_data = [], $test_event_code='') {
		$fb_pixel = fw_get_db_settings_option('fb_pixel');
		$fb_pixel_at = fw_get_db_settings_option('fb_pixel_at');

		$event = [
			'event_name'       => $event_name,
			'event_time'       => time(),
			'action_source'    => 'website',
			'event_source_url' => $event_data['url'],
			'user_data' => [
				'ph' => ($event_data['phone']!='') ? [ hash('sha256', $event_data['phone']) ] : [],
				'client_user_agent' => $event_data['user_agent'],
				'client_ip_address' => $event_data['ip_address'],
				'fbc' => $event_data['fbc'],
				'fbp' => $event_data['fbp'],
			],
		];

		if ($event_name === 'Purchase') {
			$event['custom_data'] = [
				'currency' => 'VND',
				'value'    => $value ?: 1000000, // fallback nếu không có value
			];
		}

		$payload = [ 'data' => [$event] ];

		if (!empty($test_event_code)) {
			$payload['test_event_code'] = $test_event_code;
		}

		$res = wp_remote_post(
			'https://graph.facebook.com/v20.0/'.$fb_pixel.'/events?access_token='.$fb_pixel_at,
			[
				'method'  => 'POST',
				'body'    => wp_json_encode($payload),
				'headers' => ['Content-Type' => 'application/json'],
			]
		);

		return !is_wp_error($res);
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Ads::instance();