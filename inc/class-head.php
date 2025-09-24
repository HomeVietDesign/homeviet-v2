<?php
namespace HomeViet;

class Head {

	private static $instance = null;

	private function __construct() {
		add_action('wp_head', [$this, 'head_scripts'], 50);
		//add_action('wp_head', [$this, 'noindex'], 10);
		//add_action('wp_head', [$this, 'product_open_graph'], 10);
	}

	public function product_open_graph() {
		
		if(is_single()) {
			global $post;
			//debug_log($post);
			$_functions = get_post_meta($post->ID, '_functions', true);
		?>
		<meta property="og:title" content="<?=esc_attr($post->post_title)?>">
		<meta property="og:description" content="<?=esc_attr(strip_tags($_functions))?>">
		<meta property="og:url" content="<?php echo esc_url(get_permalink($post)); ?>">
		<meta property="og:image" content="<?php echo esc_url(get_the_post_thumbnail_url( $post, 'full' )); ?>">
		<meta property="product:brand" content="TranSon">
		<meta property="product:availability" content="in stock">
		<meta property="product:condition" content="new">
		<meta property="product:price:amount" content="0">
		<meta property="product:price:currency" content="VND">
		<meta property="product:retailer_item_id" content="<?=absint($post->ID)?>">
		<meta property="product:item_group_id" content="0">
		<?php
		}
	}

	public function noindex() {
		if(is_singular( 'contractor' ) || is_singular( 'contractor_page' ) || is_tax('contractor_cat')) {
		?>
		<meta name="robots" content="noindex, nofollow" />
		<?php
		}
	}

	public static function head_scripts() {

		$footer_bg_color = fw_get_db_settings_option('footer_bg_color', '#000');
		$footer_color = fw_get_db_settings_option('footer_color', '#fff');
		if($footer_bg_color) {
			?>
			<style type="text/css">
			#site-footer {
				background-color: <?=$footer_bg_color?>;
				color: <?=$footer_color?>;
			}
			</style>
			<?php
		}

		?>
		<style type="text/css">
			/*.grecaptcha-badge {
				right: -999999px!important;
			}*/

			/*@media (min-width: 576px) {
				
			}*/
		</style>
		<script type="text/javascript">
			window.addEventListener('DOMContentLoaded', function(){
				const root = document.querySelector(':root');
				let footer_buttons_fixed_height = 0, site_header_height = 0;
				let footer_buttons_fixed = document.getElementById('footer-buttons-fixed');
				let site_header = document.getElementById('site-header');

				if(footer_buttons_fixed) footer_buttons_fixed_height = footer_buttons_fixed.clientHeight;
				if(site_header) site_header_height = site_header.clientHeight;

				root.style.setProperty('--footer-buttons-fixed--height', footer_buttons_fixed_height+'px');
				root.style.setProperty('--site-header--height', site_header_height+'px');

				window.addEventListener('resize', function(){
					root.style.setProperty('--footer-buttons-fixed--height', footer_buttons_fixed_height+'px');
					root.style.setProperty('--site-header--height', site_header_height+'px');
				});
			});

			<?php if(Common::has_turnstile()) { ?>

			function cf_turnstile_order_callback(token) {
				let $submit_button = jQuery('#order-product-submit');
				$submit_button.prop('disabled', false);
				$submit_button.text('Bấm gửi đi');
			}

			function cf_turnstile_order_error_callback() {
				let $submit_button = jQuery('#order-product-submit');
				// alert('Kiểm tra SPAM thất bại!');
				// window.location.reload();
				$submit_button.prop('disabled', true);
			}

			function cf_turnstile_order_expired_callback() {
				let $submit_button = jQuery('#order-product-submit');
				// alert('Kiểm tra SPAM hết hạn!');
				// window.location.reload();
				$submit_button.prop('disabled', true);
			}

			document.addEventListener('orderProduct', function(e){
				turnstile.reset();
			});

			<?php } ?>
		</script>
		<?php
		$custom_script = fw_get_db_settings_option('head_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}

	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Head::instance();