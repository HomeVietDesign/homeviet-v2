<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Apply_Position extends FW_Shortcode
{

	/**
	 * @internal
	 */
	public function _init()
	{

		add_action('wp_ajax_apply_position', [$this, 'ajax_apply_position']);
		add_action('wp_ajax_nopriv_apply_position', [$this, 'ajax_apply_position']);
	}

	public function ajax_apply_position() {
		$name = isset($_REQUEST['name']) ? sanitize_text_field($_REQUEST['name']) : '';
		$phone = isset($_REQUEST['phone']) ? sanitize_text_field($_REQUEST['phone']) : '';
		$position = isset($_REQUEST['position']) ? sanitize_text_field($_REQUEST['position']) : 'Không rõ';
		$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
		$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
		$ref = isset($_REQUEST['ref']) ? $_REQUEST['ref'] : '';

		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [
				'name' => $name,
				'phone' => $phone,
				'position' => $position,
			],
			'fb_pxl_code' => ''
		];


		if(\HomeViet\Common::recaptcha_verify($token, 0.5)) {

			if(''!=$name && ''!=$phone) {
				$mail_to = [
					get_bloginfo('admin_email'),
				];

				$admin2_email = \HomeViet\Common::get_admin2_email();

				if(!empty($admin2_email)) {
					$mail_to = array_merge($mail_to, $admin2_email);
				}

				//$mail_to = 'qqngoc2988@gmail.com';

				$mail_headers = array('Content-Type: text/html; charset=UTF-8');

				$subject = '['.$name.'] Ứng tuyển';
				
				ob_start();
				?>
				<p style='font-weight:bold;'>THÔNG TIN ỨNG VIÊN</p>
				<p>Họ tên: <?=esc_html($name)?></p>
				<p>Số điện thoại: <?=esc_html($phone)?></p>
				<p>Vị trí: <?=esc_html($position)?></p>
				<p>-------------</p>
				<p>Email gửi từ website: <?=esc_url(home_url())?></p>
				<p>Quá trình truy cập:<br>
				<?php
				if($ref!='') {
					$ref = base64_decode($ref);
					$referrers = explode(',', $ref);
					if(!empty($referrers)) {
						foreach ($referrers as $key => $value) {
							echo esc_html($value).'<br>';
						}
					}
				}
				echo esc_html($url).'<br>';
				?>
				</p>
				<p>Thiết bị: <?=esc_html($_SERVER['HTTP_USER_AGENT'])?></p>
				<?php
				$body = ob_get_clean();

				$send = wp_mail( $mail_to, $subject, $body, $mail_headers );

				//$send = true;

				if($send) {
					$response['code'] = 1;
					$response['msg'] = '<p>Chúng tôi đang tiếp nhận thông tin của bạn</p><p>Xin cảm ơn!</p>';
					
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
			$response['msg'] = 'Chưa vượt qua kiểm tra spam. Xin thử lại!';
		}
		
		$response = apply_filters( 'apply_position', $response );

		wp_send_json($response);
	}

}
