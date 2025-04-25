<?php
namespace HomeViet;

class Ajax {

	private static $instance = null;

	private function __construct() {
		add_action('wp_ajax_posts_masonry_loadmore', [$this, 'posts_masonry_loadmore']);
		add_action('wp_ajax_nopriv_posts_masonry_loadmore', [$this, 'posts_masonry_loadmore']);

		add_action('wp_ajax_order_product', [$this, 'order_product']);
		add_action('wp_ajax_nopriv_order_product', [$this, 'order_product']);

		add_action('wp_ajax_get_seo_post', [$this, 'ajax_get_seo_post']);
		add_action('wp_ajax_nopriv_get_seo_post', [$this, 'ajax_get_seo_post']);

		add_action('wp_ajax_url_delete_cache', [$this, 'ajax_url_delete_cache']);
		add_action('wp_ajax_nopriv_url_delete_cache', [$this, 'ajax_url_delete_cache']);

		add_action('wp_ajax_contractor_search', [$this, 'ajax_contractor_search']);
		add_action('wp_ajax_nopriv_contractor_search', [$this, 'ajax_contractor_search']);

		// add_action('wp_ajax_statistic', [$this, 'ajax_statistic']);
		// add_action('wp_ajax_nopriv_statistic', [$this, 'ajax_statistic']);
	}

	public function ajax_statistic() {
		debug_log($_REQUEST);
		die;
	}

	public function ajax_contractor_search() {
		global $current_password;
		?>
		<div class="text-center text-uppercase fw-bold fs-2 text-yellow py-3 bg-black mb-3">Kết quả tìm kiếm</div>
		<div class="contractor-search-response py-3 container-xxl">
		<?php
		if(has_role('administrator') || $current_password) {
			$kw = isset($_REQUEST['kw'])?sanitize_text_field($_REQUEST['kw']):'';
			if($kw) {
				$search_result = wp_do_shortcode('contractors', ['number'=>12]);
				if($search_result) {
					echo $search_result;
				} else {
					echo '<div class="py-4 text-center">Không có kết quả nào được tìm thấy.</div>';
				}
			} else {
				echo '<div class="py-4 text-center">Nhập từ khóa để tra cứu.</div>';
			}
		} else {
			echo '<div class="py-4 text-center">Forbidden.</div>';
		}
		?>
		</div>
		<?php
		exit;
	}

	public function ajax_url_delete_cache() {
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
		if($url) wp_remote_request($url, ['method'=>'PURGE']);
		exit;
	}

	public function ajax_get_seo_post() {
		$response = [
			'results' => []
		];

		$search = isset($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : '';

		if($search!='' && mb_strlen($search)>1) {
	
			$args = [
				'post_type' => 'seo_post',
				'post_status' => 'publish',
				'numberposts' => -1,
				's' => $search,
				'custom_search' => 1,
				'fields' => 'ids',
				'suppress_filters' => 0
				
			];

			$posts = get_posts($args);

			if(!is_wp_error( $posts ) && $posts) {

				foreach ($posts as $id) {
					$response['results'][] = [
						'id' => get_permalink( $id ),
						'text' => get_post_field( 'post_title', $id )
					];
				}

			}
		}

		wp_send_json( $response );
	}

	public function order_product() {

		$attachment = isset($_REQUEST['attachment']) ? absint($_REQUEST['attachment']) : 0;
		$code = isset($_REQUEST['code']) ? sanitize_text_field($_REQUEST['code']) : '';
		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$name = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : '';
		$phone = isset($_REQUEST['phone']) ? sanitize_text_field($_REQUEST['phone']) : '';
		$type = isset($_REQUEST['ctype']) ? sanitize_text_field($_REQUEST['ctype']) : 'normal';
		$url = isset($_REQUEST['url']) ? esc_url(base64_decode($_REQUEST['url'])) : '';
		$ref = isset($_COOKIE['_ref'])?$_COOKIE['_ref']:'';
		$referrer = urldecode(base64_decode($ref).','.$url);
		$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';

		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [
				'code' => $code,
				'id' => $id,
				'name' => $name,
				'phone' => $phone
			],
			'fb_pxl_code' => ''
		];

		if( \HomeViet\Common::cf_captcha_verify($token) ) {

			if($attachment && $id && ''!=$code && ''!=$name && ''!=$phone) {

				$attachment_img = wp_get_attachment_url( $attachment );

				$mail_to = [
					get_bloginfo('admin_email'),
				];

				$admin2_email = \HomeViet\Common::get_admin2_email();

				if(!empty($admin2_email)) {
					$mail_to = array_merge($mail_to, $admin2_email);
				}

				//$mail_to = 'qqngochv@gmail.com';

				$mail_headers = array('Content-Type: text/html; charset=UTF-8');

				ob_start();

				$subject = '[ '.$phone.' ] Chọn mẫu';

				?>
				<p style='font-weight:bold;'><?php
				switch ($type) {
					case 'premium':
						echo esc_html(get_option('product_order_premium_popup_title', ''));
						break;
					
					default:
						echo esc_html(get_option('product_order_popup_title', ''));
						break;
				}
				?></p>
				<p>Họ tên: <?=esc_html($name)?></p>
				<p>Số điện thoại: <?=esc_html($phone)?></p>
				<p>Mã mẫu: <?=esc_html($code)?></p>
				<p>Ảnh mẫu:</p>
				<p><img src="<?=esc_url($attachment_img)?>" style='width:100%;height:auto;'></p>
				<p>-------------</p>
				<p>Email gửi từ website: <?=esc_url(home_url())?></p>
				
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
				if(preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches)) {
					echo esc_html(str_replace('+', ' ', $matches[1]));
				}
				?>
				</p>
				<p>Thiết bị: <?=esc_html($_SERVER['HTTP_USER_AGENT'])?></p>
				<?php
				$body = ob_get_clean();

				//$response['msg'] = $body;
				
				$send = wp_mail( $mail_to, $subject, $body, $mail_headers );
				
				//$send = true;

				if($send) {
					
					if(function_exists('as_enqueue_async_action')) {
						as_enqueue_async_action('count_order_sent', [$id], 'order');
						as_enqueue_async_action('add_customer_order', [['id'=>$id, 'title'=>get_the_title($id), 'image'=>$attachment_img, 'phone'=>$phone, 'name'=>$name, 'type'=>$type, 'url'=>$url, 'ref'=>$ref, 'user_agent'=>$_SERVER['HTTP_USER_AGENT']]], 'order');
					}
					
					$response['code'] = 1;
					$response['msg'] = '<p><strong>Yêu cầu của Quý khách đã được gửi đi.</strong> Trợ lý của KTS. Trần Sơn sẽ liên hệ tư vấn trong thời gian sớm nhất.</p><p>Xin cảm ơn!</p>';
				} else {
					$response['code'] = -3;
					$response['msg'] = 'Yêu cầu chưa được gửi đi! Vui lòng liên hệ với ban quản trị về sự cố này.';
				}
			} else {
				$response['code'] = -2;
				$response['msg'] = 'Thông tin đã nhập không hợp lệ! Xin thử lại.';
			}
			
		} else {
			$response['code'] = -1;
			$response['msg'] = 'Chưa xác minh! Xin thử lại.';
		}

		$response = apply_filters( 'order_submit', $response );
		

		wp_send_json($response);
		
		die;
	}

	public function posts_masonry_loadmore() {
		$cat = isset($_REQUEST['cat']) ? absint($_REQUEST['cat']) : 0;
		$location = isset($_REQUEST['local']) ? absint($_REQUEST['local']) : 0;
		$catexc = isset($_REQUEST['catexc']) ? array_map('absint', $_REQUEST['catexc']) : [];
		$page = isset($_REQUEST['page']) ? absint($_REQUEST['page']) : 0;
		$per = isset($_REQUEST['per']) ? absint($_REQUEST['per']) : 12;
		$ex = isset($_REQUEST['ex']) ? absint($_REQUEST['ex']) : 0;

		$args = [
			'post_type' => 'post',
			'posts_per_page' => $per,
			'paged' => $page,
			'post_status' => 'publish'
		];

		if($cat) {
			$args['cat'] = $cat;
		}

		if($location) {
			$args['tax_query'] = [
				'location' => [
					'taxonomy' => 'location',
					'field' => 'term_id',
					'terms' => $location
				]
			];
		}

		if($catexc) {
			$args['category__not_in'] = $catexc;
		}

		if($ex) {
			$args['post__not_in'] = [$ex];
		}

		//debug_log($args);

		$query = new \WP_Query($args);

		//debug_log($query->request);

		if($query->have_posts()) {
			while($query->have_posts()) {
				$query->the_post();
				get_template_part('post', 'loop');
			}
		}
		wp_reset_postdata();
		die;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Ajax::instance();