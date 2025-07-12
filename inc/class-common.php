<?php
namespace HomeViet;

final class Common {

	/**
     * Scans the HTTP headers for the first valid IP address it can find.
     *
     * @return string|null The first valid IP address found, or null if none
     *                     were found.
     */
    public static function get_ip_address() {
        $headers_to_scan = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR',
        );

        foreach ( $headers_to_scan as $header ) {
            if ( isset( $_SERVER[ $header ] ) ) {
                $ip_list = explode( ',', $_SERVER[ $header ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
                foreach ( $ip_list as $ip ) {
                    $trimmed_ip = trim( $ip );
                    if ( self::is_valid_ip_address( $trimmed_ip ) ) {
                        return $trimmed_ip;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Retrieves the User-Agent string from the HTTP request headers.
     *
     * @return string|null The User-Agent string, or null if it was not found.
     */
    public static function get_http_user_agent() {
        $user_agent = null;

        if ( ! empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
            $user_agent = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }

        return $user_agent;
    }

    /**
     * Retrieves the request URI for the current HTTP request.
     *
     * This function constructs the full request URI by considering
     * the protocol, host, and request path. If the
     * $prefer_referrer_for_event_src parameter is true and a referrer
     * URL is present in the HTTP headers, it returns the referrer URL instead.
     *
     * @param boolean $prefer_referrer_for_event_src Whether to
     * prefer the referrer URL over the current request URL.
     *
     * @return string The constructed request URI or the referrer
     * URL if preferred.
     */
    public static function get_request_uri( $prefer_referrer_for_event_src ) {
        if ( $prefer_referrer_for_event_src
        && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
            return sanitize_text_field( $_SERVER['HTTP_REFERER'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }

            $url = 'http://';
        if ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) {
            $url = 'https://';
        }

        if ( ! empty( $_SERVER['HTTP_HOST'] ) ) {
            $url .= sanitize_text_field( $_SERVER['HTTP_HOST'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }

        if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
            $url .= sanitize_text_field( $_SERVER['REQUEST_URI'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        }

        return $url;
    }

    /**
     * Retrieves the Facebook Browser ID (FBP) cookie.
     *
     * This function returns the value of the FBP cookie, which
     * is a unique identifier assigned to a user by Facebook.
     * The FBP cookie is used by the Facebook pixel to track
     * user behavior across multiple sites and sessions.
     *
     * @return string|null The value of the FBP cookie, or
     * null if the cookie is not present.
     */
    public static function get_fbp() {
      $fbp = null;

      if ( ! empty( $_COOKIE['_fbp'] ) ) {
          $fbp = sanitize_text_field( wp_unslash( $_COOKIE['_fbp'] ) );
      }

      return $fbp;
  }

    /**
     * Retrieves the Facebook Click ID (FBC) cookie or session variable.
     *
     * This function returns the value of the FBC cookie or session variable,
     * which is a unique identifier assigned to a user by Facebook. The FBC
     * cookie is used by the Facebook pixel to track user behavior across
     * multiple sites and sessions. If the FBC cookie is not present, the
     * function will attempt to generate an FBC value from the fbclid query
     * parameter, if present.
     *
     * @return string|null The value of the FBC cookie or session variable, or
     *                     null if neither is present.
     */
    public static function get_fbc() {
        $fbc = null;

        if ( ! empty( $_COOKIE['_fbc'] ) ) {
            $fbc              = sanitize_text_field(
                wp_unslash( $_COOKIE['_fbc'] )
            );
            $_SESSION['_fbc'] = $fbc;
        }

        if ( ! $fbc && isset( $_GET['fbclid'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
            $fbclid   = sanitize_text_field( wp_unslash( $_GET['fbclid'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
            $cur_time = (int) ( microtime( true ) * 1000 );
            $fbc      = 'fb.1.' . $cur_time . '.' . rawurldecode( $fbclid );
        }

        if ( ! $fbc && isset( $_SESSION['_fbc'] ) ) {
            $fbc = sanitize_text_field( $_SESSION['_fbc'] );
        }

        if ( $fbc ) {
            $_SESSION['_fbc'] = $fbc;
        }

        return $fbc;
    }

    /**
     * Validates an IP address.
     *
     * This function takes an IP address and returns true if it is valid,
     * false otherwise. The function uses the filter_var function to validate
     * the IP address, and it filters out public and reserved IP addresses.
     *
     * @param string $ip_address The IP address to validate.
     * @return bool True if the IP address is valid, false otherwise.
     */
    public static function is_valid_ip_address( $ip_address ) {
        return filter_var(
            $ip_address,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 |
            FILTER_FLAG_IPV6 |
            FILTER_FLAG_NO_PRIV_RANGE |
                FILTER_FLAG_NO_RES_RANGE
        );
    }

    public static function has_turnstile() {
        $turnstile_keys = self::get_turnstile_keys();

        if ($turnstile_keys['sitekey']!='' && $turnstile_keys['secretkey']!='') {
            return true;
        }

        return false;
    }

	public static function cf_captcha_verify($token) {
		// Get Turnstile Keys from Settings
		$turnstile_keys = self::get_turnstile_keys();

		if ($turnstile_keys['sitekey']!='' && $turnstile_keys['secretkey']!='') {

			$headers = array(
				'body' => [
					'secret' => $turnstile_keys['secretkey'],
					'response' => $token
				]
			);
			$verify = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $headers);

            if ( 200 !== wp_remote_retrieve_response_code( $verify ) ) {
                return false;
            }

			$verify = wp_remote_retrieve_body($verify);
			$response = json_decode($verify);

			//wp_mail( 'qqngoc2988@gmail.com', $_SERVER['HTTP_HOST'].' cf captcha verify', json_encode( $response ), ['Content-Type: text/html; charset=UTF-8'] );

			//debug_log($response);

			if($response->success) {
				return true;
			}
		}

		return false;
	}

    public static function get_turnstile_keys() {
        if(!function_exists('is_plugin_active')) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $sitekey = '';
        $secretkey = '';
        $ctf7_has_turnstile = false;

        if(is_plugin_active( 'contact-form-7/wp-contact-form-7.php' )) {
            $ctf7_turnstile = \WPCF7_Turnstile::get_instance();

            if($ctf7_turnstile->is_active()) {
                $sitekey = $ctf7_turnstile->get_sitekey();
                $secretkey = $ctf7_turnstile->get_secret($sitekey);
                $ctf7_has_turnstile = true;
            }
        }

        if($sitekey=='' || $secretkey=='') {
            $sitekey = fw_get_db_settings_option('cf_turnstile_key');
            $secretkey = fw_get_db_settings_option('cf_turnstile_secret');
        }

        return ['sitekey'=>$sitekey,'secretkey'=>$secretkey, 'ctf7'=>$ctf7_has_turnstile];
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
			
			//wp_mail( 'qqngoc2988@gmail.com', $_SERVER['HTTP_HOST'].' recaptcha verify', json_encode( $recaptcha_verify ), ['Content-Type: text/html; charset=UTF-8'] );

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