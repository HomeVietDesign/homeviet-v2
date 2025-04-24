<?php
namespace HomeViet;

class Authentication {

	private static $instance = null;

	private function __construct() {

		add_filter( 'the_password_form', [$this, 'the_password_form'], 10, 2 );
		add_filter( 'post_password_required', [$this, 'post_password_required'], 10, 2 );

		add_filter( 'nonce_life', [$this, 'nonce_life'] );
	}

	public function nonce_life() {
		return 8 * DAY_IN_SECONDS;
	}

	public function post_password_required($required, $post) {
		global $current_password;

		$post = get_post( $post );

		if($post->post_type=="contractor_page" && !has_role('administrator')) {
			$required = true;

			if($current_password) {
				$required = false;
			}

			// if(self::check($post)) {
			// 	$required = false;
			// }
		}

		return $required;
	}

	public static function check($contractor_page) {
		global $current_password;
		if($current_password) {
			return true;
		}
		return false;
	}

	public function the_password_form($output, $post) {
		
		ob_start();
		?>
		<form action="<?=home_url( 'wp-login.php?action=postpass' )?>" class="post-password-form text-center py-4 px-3" method="post">
			<div class="d-inline-block">
				<div class="my-3">Nội dung này được bảo mật. Hãy nhập mật khẩu để xem tiếp:</div>
				<div class="input-group mb-3">
				  <input name="post_password" type="password" class="form-control" spellcheck="false">
				  <button class="btn btn-primary" type="submit">Nhập</button>
				</div>
			</div>
		</form>
		<?php
		$output = ob_get_clean();
		return $output;
	}


	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Authentication::instance();