<?php
namespace HomeViet;

/**
 * 
 */
class WPCF7 {
	
	private static $instance = null;

	private function __construct() {
		
		/**
		 * Disables REFILL function in WPCF7 if Recaptcha is in use
		 */
		//add_action('wp_enqueue_scripts', [$this, 'wpcf7_recaptcha_no_refill'], 15, 0);

		//add_action( 'wpcf7_init', [$this, 'add_form_tag_referer'] );

		add_action( 'wpcf7_before_send_mail', [$this, 'wpcf7_before_send_mail'] );
		
		add_action( 'wpcf7_mail_sent', [$this, 'wpcf7_mail_sent'] );

		add_filter( 'wpcf7_posted_data_tel*', [$this, 'wpcf7_convert_phone_number'], 10, 3 );

	}

	public function wpcf7_convert_phone_number($value, $value_orig, $tag) {
		$value = '+'.phone_0284(sanitize_phone_number($value));

		return $value;
	}

	public function wpcf7_mail_sent($contact_form) {
		//$props = $contact_form->get_properties();

		$submission = \WPCF7_Submission::get_instance();
		//debug_log($submission);
		//$ref = base64_decode(isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'');
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';

		if(function_exists('as_enqueue_async_action')) {
			as_enqueue_async_action('add_customer_order', [ [ 'id'=>0, 'title'=>'Gửi số tư vấn', 'image'=>'', 'phone'=>$submission->get_posted_data('your_phone'), 'name'=>$submission->get_posted_data('your_name'), 'type'=>'wpcf7', 'url'=>$submission->get_meta('url'), 'ref'=>$ref, 'user_agent'=>$submission->get_meta('user_agent') ] ], 'order');
		}

	}

	public function wpcf7_before_send_mail($contact_form) {
		$submission = \WPCF7_Submission::get_instance();
		//debug_log($submission);

		$props = $contact_form->get_properties();
		//$props['mail']['body'] .= '<br>Referer: '.esc_url($_SERVER['HTTP_REFERER']).'<br>User agent: '.esc_html($_SERVER['HTTP_USER_AGENT']);
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$referrer = urldecode(base64_decode($ref).','.$submission->get_meta('url'));
		ob_start();
		?>
		<p>Nguồn: 
		<?php
		if(strpos($referrer, 'facebook')!==false || strpos($referrer, 'fbclid')!==false) {
			echo 'Facebook';
		} elseif (strpos($referrer, 'google')!==false || strpos($referrer, 'gclid')!==false) {
			echo 'Google';
		} elseif (strpos($referrer, 'zalo')!==false) {
			echo 'Zalo';
		} else {
			echo '(Không xác định)';
		}
		?>
		</p>
		<p>Quảng cáo: 
		<?php
		if( preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches) ) {
				echo esc_html(str_replace('+', ' ', $matches[1]));
			}
		?>
		</p>
		<?php
		$props['mail']['body'] .= ob_get_clean();
		
		$contact_form->set_properties($props);
	}

	public function add_form_tag_referer() {
		wpcf7_add_form_tag( 'user_agent', [$this, 'user_agent_form_tag_handler'] );
		wpcf7_add_form_tag( 'referer', [$this, 'referer_form_tag_handler'] );
	}

	public function user_agent_form_tag_handler($tag) {
		$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '-';
		return '<input type="hidden" name="user_agent" value="'.esc_attr($user_agent).'">';
	}

	public function referer_form_tag_handler($tag) {
		//debug_log($tag);
		// [_site_admin_email]
		// Cc:nguyencuong.hv2017@gmail.com
		// Cc:qqngochv@gmail.com
		$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '-';
		return '<input type="hidden" name="referer" value="'.esc_url($referer).'">';
	}

	public function wpcf7_recaptcha_no_refill() {
		$service = \WPCF7_RECAPTCHA::get_instance();

		if ( ! $service->is_active() ) {
			return;
		}
		//debug_log($service);
		wp_add_inline_script('contact-form-7', 'wpcf7.cached = 0;', 'before' );
	}


	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

WPCF7::instance();