<?php
namespace HomeViet;

class Assets {

	private static $instance = null;

	private function __construct() {
		//add_filter( 'tiny_mce_before_init', [$this, 'tiny_mce_before_init'] );
		//add_filter( 'after_setup_theme', [$this, 'editor_style'] );

		add_action('wp_enqueue_scripts', [$this, 'enqueue_styles'], 50);
		add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts'], 50);
		add_action('wp_enqueue_scripts', [$this, 'recaptcha_script'], 21);

	}

	public function editor_style() {
    	$font_url = str_replace( ',', '%2C', '//fonts.googleapis.com/css2?family=Arizonia&family=Fahkwang:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Great+Vibes&family=Inter:wght@400;500;600;700&family=Marmelad&family=Mea+Culpa&family=Water+Brush&display=swap' );
    	add_editor_style( $font_url );
	}

	public function tiny_mce_before_init( $initArray ) {
		if(!isset($initArray['font_formats'])) $initArray['font_formats'] = '';
		$initArray['font_formats'] .= 'Arizonia=Arizonia;Fahkwang=Fahkwang;Great Vibes=Great Vibes;Marmelad=Marmelad;Mea Culpa=Mea Culpa;Water Brush=Water Brush;Inter=Inter;Arial=arial,helvetica,sans-serif;Georgia=georgia,palatino;Helvetica=helvetica;Times New Roman=times new roman,times;';
		return $initArray;
	}

	public static function enqueue_styles() {

		wp_dequeue_style( 'font-awesome' );

		//wp_register_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Arizonia&family=Fahkwang:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Great+Vibes&family=Inter:wght@400;500;600;700&family=Marmelad&family=Mea+Culpa&family=Water+Brush&display=swap' );
		
		wp_register_style( 'bootstrap', THEME_URI.'/libs/bootstrap/css/bootstrap.min.css', [], '5.1.3' );

		wp_register_style( 'owlcarousel', THEME_URI.'/libs/owlcarousel/assets/owl.carousel.min.css', [], '2.3.4' );
		wp_register_style( 'select2', THEME_URI.'/libs/select2/dist/css/select2.min.css', [], '4.0.13' );

		$deps = ['bootstrap','dashicons','select2'];
		if(is_single()) {
			$deps[] = 'owlcarousel';
		}

		wp_enqueue_style( 'TranSon', THEME_URI.'/assets/css/main.css', $deps, date('YmdHis', filemtime(THEME_DIR . '/assets/css/main.css')) );
	}

	public static function recaptcha_script() {
		$recaptcha_keys = Common::get_recaptcha_keys();

		if(!$recaptcha_keys['ctf7'] && $recaptcha_keys['sitekey']!='' && !wp_script_is('google-recaptcha', 'registered')) {
			wp_enqueue_script( 'google-recaptcha',
				add_query_arg(
					[ 'render' => $recaptcha_keys['sitekey'] ],
					'https://www.google.com/recaptcha/api.js'
				),
				[],
				'3.0',
				true
			);
		}


	}

	public static function enqueue_scripts() {
		// wp_scripts()->add_data( 'jquery', 'group', 1 );
		// wp_scripts()->add_data( 'jquery-core', 'group', 1 );
		// wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );

		wp_dequeue_style( 'wp-block-library' );
	    wp_dequeue_style( 'wp-block-library-theme' );
	    wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS

	    //wp_enqueue_script('lodash');

		$recaptcha_keys = Common::get_recaptcha_keys();

		wp_register_script( 'bootstrap', THEME_URI.'/libs/bootstrap/js/bootstrap.bundle.min.js', ['jquery'], '5.1.3', true);

		wp_register_script( 'owlcarousel', THEME_URI.'/libs/owlcarousel/owl.carousel.min.js', ['jquery'], '2.3.4', true);
		wp_register_script( 'select2', THEME_URI.'/libs/select2/dist/js/select2.full.min.js', ['jquery'], '4.0.13', true);
		wp_register_script( 'isotope', THEME_URI.'/libs/isotope/isotope.pkgd.min.js', ['jquery'], '3.0.6', true);

		$deps = [
			'jquery',
			'bootstrap',
			'imagesloaded',
			'isotope',
			'select2',
			//'lodash',
		];
		if(is_single()) {
			$deps[] = 'owlcarousel';
		}
	
		wp_enqueue_script( 'TranSon', THEME_URI.'/assets/js/main.js', $deps, date('YmdHis', filemtime(THEME_DIR . '/assets/js/main.js')), true);

		// $kws = fw_get_db_settings_option('product_keywords');
		// $data_kws = [];
		// if(!empty($kws)) {

		// 	foreach ($kws as $key => $value) {
		// 		if(!empty($value['page'])) {
		// 			$data_kws[] = [
		// 				'id' => get_permalink( $value['page'][0] ),
		// 				'text' => $value['name']
		// 			];
		// 		}
		// 	}
		// }

		//$thank_you_page = fw_get_db_settings_option('thank_you_page', '');

		$provinces = get_terms([
			'taxonomy' => 'province',
			'fields' => 'id=>name',
			'hide_empty' => false,
		]);

		$a_provinces = [];

		if($provinces) {
			foreach ($provinces as $id => $name) {
				$a_provinces[] = [
					'id' => $id,
					'text' => $name
				];
			}
		}

		$data = [
			'home_url'=>esc_url(home_url()), 
			'ajax_url'=>esc_url(admin_url('admin-ajax.php')),
			'sitekey'=>$recaptcha_keys['sitekey'],
			'cf_sitekey'=>fw_get_db_settings_option('cf_turnstile_key'),
			'is_user_logged_in' => (is_user_logged_in())?1:0,
			'preview' => (isset($_GET['preview']))?1:0,
			'popup_content_timeout' => absint(fw_get_db_settings_option('popup_content_timeout', 120)),
			'nonce' => wp_create_nonce( 'global' ),
			'provinces' => $a_provinces

		];

		wp_localize_script( 'jquery', 'theme', $data );
		wp_add_inline_script( 'jquery-core', self::get_inline_scripts(), 'before' );

	}

	public static function get_inline_scripts() {
		ob_start();
		?>
		<script type="text/javascript">
			/*
			function remove_savedlist() {
				setCookie('savedlist', '', -1);
			}

			function remove_from_savedlist(id) {
				id = ''+id;
				let savedlist = get_savedlist();
				const index = savedlist.indexOf(id);
				if(index!==-1) {
					savedlist.splice(index, 1);
				}
				setCookie('savedlist', savedlist, 14);
			}

			function add_to_savedlist(id) {
				let savedlist = get_savedlist();
				id = ''+id;
				if(savedlist.indexOf(id)===-1) {
					savedlist.push(id);
				}
				setCookie('savedlist', savedlist, 14);
			}

			function get_savedlist() {
				let savedlist = getCookie('savedlist');
				if(savedlist!='') {
					savedlist = savedlist.split(',');
					//if(typeof savedlist == 'string') savedlist = [savedlist];
				} else {
					savedlist = [];
				}
				return savedlist;
			}
			*/
			const isValidUrl = urlString=> {
				let httpRegex = /^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/;
				return httpRegex.test(urlString);
			}

			function is_mobile() {
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					return true;
				}
				return false;
			}

			function setCookie(cname, cvalue, exdays) {
				const d = new Date();
				d.setTime(d.getTime() + (exdays*24*60*60*1000));
				let expires = "expires="+ d.toUTCString();
				document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
			}

			function getCookie(cname) {
				let name = cname + "=";
				let decodedCookie = decodeURIComponent(document.cookie);
				let ca = decodedCookie.split(';');
				for(let i = 0; i <ca.length; i++) {
					let c = ca[i];
					while (c.charAt(0) == ' ') {
						c = c.substring(1);
					}
					if (c.indexOf(name) == 0) {
						return c.substring(name.length, c.length);
					}
				}
				return "";
			}

			function deleteCookie(name) {
				document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
			}

			function add_query_url(key,value,url) {
				let new_url = new URL(url);
				let search_params = new_url.searchParams;
				search_params.append(key, value);
				new_url.search = search_params.toString();
				return new_url.toString();
			}

			function remove_query_url(key,url) {
				let new_url = new URL(url);
				let search_params = new_url.searchParams;
				search_params.delete(key);
				new_url.search = search_params.toString();
				return new_url.toString();
			}

			function debounce(func, wait = 250) { // Default wait time of 250ms
				let timeout;
				return function executedFunction(...args) {
					clearTimeout(timeout); // Clear any previous timeout
					timeout = setTimeout(() => func.apply(this, args), wait);
				};
			}

			function throttle(func, wait = 250) {
				let isWaiting = false;
				return function executedFunction(...args) {
					if (!isWaiting) {
						func.apply(this, args);
						isWaiting = true;
						setTimeout(() => {
							isWaiting = false;
						}, wait);
					}
				};
			}

			async function CopyToClipboard(text) {
				try {
					await navigator.clipboard.writeText(text);
					//console.log('Content copied to clipboard');
					return true
				} catch (err) {
					//console.error('Failed to copy: ', err);
					return false;
				}
			}

			let ref = getCookie('_ref');
			if(ref=='') {
				ref = window.btoa((document.referrer=='')?window.location.href:document.referrer);
				setCookie('_ref', ref, 1);
			} else {
				let aref = window.atob(ref).split(',');
				if(document.referrer!=aref[aref.length-1]) {
					ref = window.btoa(window.atob(ref)+','+document.referrer);
				}
				setCookie('_ref', ref, 1);
			}
		</script>
		<?php
		return trim( preg_replace( '#<script[^>]*>(.*)</script>#is', '$1', ob_get_clean() ) );
	}
	
	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Assets::instance();