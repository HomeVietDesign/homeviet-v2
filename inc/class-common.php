<?php
namespace HomeViet;

final class Common {

	public static function cf_captcha_verify($token) {
		// Get Turnstile Keys from Settings
		$key = sanitize_text_field(fw_get_db_settings_option('cf_turnstile_key'));
		$secret = sanitize_text_field(fw_get_db_settings_option('cf_turnstile_secret'));

		if ($key && $secret) {

			$headers = array(
				'body' => [
					'secret' => $secret,
					'response' => $token
				]
			);
			$verify = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $headers);
			$verify = wp_remote_retrieve_body($verify);
			$response = json_decode($verify);

			wp_mail( 'qqngoc2988@gmail.com', $_SERVER['HTTP_HOST'].' cf captcha verify', json_encode( $response ), ['Content-Type: text/html; charset=UTF-8'] );

			//debug_log($response);

			if($response->success) {
				return true;
			}
		}

		return false;
	}

	public static function recaptcha_verify($token, $score=0.5) {
		$recaptcha_keys = self::get_recaptcha_keys();

		if($recaptcha_keys['secretkey']!='') {
			$check_captcha = wp_remote_post(
				"https://www.google.com/recaptcha/api/siteverify",
				array(
					'body'=>array(
						'secret' => $recaptcha_keys['secretkey'],
						'response' => $token
					)
				)
			);

			$recaptcha_verify = json_decode(wp_remote_retrieve_body($check_captcha), true);
			
			wp_mail( 'qqngoc2988@gmail.com', $_SERVER['HTTP_HOST'].' recaptcha verify', json_encode( $recaptcha_verify ), ['Content-Type: text/html; charset=UTF-8'] );

			//debug_log($recaptcha_verify);

			if(boolval($recaptcha_verify["success"]) && $recaptcha_verify["score"] >= $score) {
				return true;
			}
		} else {
			return true;
		}

		return false;
	}

	public static function get_recaptcha_keys() {
		if(!function_exists('is_plugin_active')) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$sitekey = '';
		$secretkey = '';
		$ctf7_has_recaptcha = false;

		if(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )) {
			$ctf7_recaptcha = \WPCF7_RECAPTCHA::get_instance();

			if($ctf7_recaptcha->is_active()) {
				$sitekey = $ctf7_recaptcha->get_sitekey();
				$secretkey = $ctf7_recaptcha->get_secret($sitekey);
				$ctf7_has_recaptcha = true;
			}
		}

		if($sitekey=='' || $secretkey=='') {
			$sitekey = fw_get_db_settings_option('recaptcha_key');
			$secretkey = fw_get_db_settings_option('recaptcha_secret');
		}

		return ['sitekey'=>$sitekey,'secretkey'=>$secretkey, 'ctf7'=>$ctf7_has_recaptcha];
	}

	public static function get_admin2_email() {
		$admin2_email = explode(',',fw_get_db_settings_option('admin2_email'));
		return array_map('sanitize_email', $admin2_email);
	}

}