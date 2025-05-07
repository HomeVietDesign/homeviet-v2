<?php
namespace HomeViet;

class Footer {
	private static $instance = null;

	private function __construct() {
		add_action( 'template_redirect', [$this, 'display_footer_html'] );
		add_action( 'wp_footer', [$this, 'custom_scripts'], 100 );

		// remove_action( 'wp_footer', 'snow_fall_print_web_component', 100 );
		// add_action( 'wp_footer', [$this, 'snow_fall_print_web_component'], 100 );
	}

	public function snow_fall_print_web_component() {
		?>
		<is-land on:media="(prefers-reduced-motion: no-preference)" on:idle>
			<snow-fall text="❄︎" style="--snow-fall-color: rebeccapurple"></snow-fall>
		</is-land>
		<?php
	}

	public function logout_post_password() {
		global $current_password;

		if(is_singular('contractor_page')) {
			?>
			<div class="contractor-actions-fixed position-fixed d-flex">
			<?php
			if($current_password) {
				?>
				<button class="logout-post-password btn btn-primary me-1" data-hash="<?=esc_attr(COOKIEHASH)?>" data-url="<?=esc_url(fw_current_url())?>" title="Nhập lại pass">
					<div class="d-flex align-items-center"><span class="dashicons dashicons-unlock"></span><span class="ms-1"><?php echo esc_html($current_password->description); ?></span></div>
				</button>
				<?php		
			}
			?>
			</div>
			<?php
			
		}
	}

	public function site_footer() {
	
		?>
		<footer id="site-footer" class="py-5">
		<?php
		
		\HomeViet\Footer::display_widgets();
		
		?>
		</footer>
		<?php
	}

	public function order_product_modal() {
		?>
		<div class="modal fade" id="order-product" tabindex="-1" role="dialog" aria-labelledby="order-product-label">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="order-product-label">
							<span class="title-normal"><?=esc_html(get_option('product_order_popup_title', ''))?></span>
							<span class="title-premium hide"><?=esc_html(get_option('product_order_premium_popup_title', ''))?></span>
						</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<form id="frm-order-product" class="modal-body" method="POST" action="">
						<input type="hidden" id="product_attachment" name="product_attachment" value="0" required>
						<input type="hidden" id="product_code" name="product_code" value="" required>
						<input type="hidden" id="product_id" name="product_id" value="" required>
						<input type="hidden" id="ctype" name="ctype" value="normal" required>

						<?php
						$product_order_popup_desc = get_option('product_order_popup_desc', '');
						if($product_order_popup_desc!='') {
							?>
							<div class="mb-3 product-order-popup-desc"><?=wp_get_the_content($product_order_popup_desc)?></div>
							<?php
						}
						?>
						<div class="mb-3">
							<input type="text" id="product_customer_name" name="product_customer_name" maxlength="60" class="form-control" placeholder="Họ tên" required>
							<div class="invalid-feedback"></div>
						</div>
						<div class="mb-3">
							<input type="tel" id="product_customer_phone" name="product_customer_phone" placeholder="Số điện thoại của bạn" class="form-control" aria-label="Số điện thoại của bạn" required>
						</div>
						<div class="d-none">
							<div id="cf-turnstile-order" class="cf-turnstile" data-sitekey="<?=esc_attr(fw_get_db_settings_option('cf_turnstile_key'))?>"></div>
						</div>
						<div class="mb-3">
							<button type="submit" class="btn btn-lg btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100" id="order-product-submit" disabled>Bấm gửi đi</button>
							<div class="invalid-feedback"></div>
						</div>
						<div id="order-product-message"></div>
						<div id="order-product-preview" class="position-relative"></div>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	public function custom_scripts() {
		$custom_script = fw_get_db_settings_option('footer_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
		?>
		<!-- <script type="text/javascript">
			window.onloadTurnstileCallback = function () {
				turnstile.render("#example-container", {
				sitekey: "<YOUR_SITE_KEY>",
				callback: function (token) {
				console.log(`Challenge Success ${token}`);
				},
				});
			};
		</script> -->
		<?php
	}

	public function footer_fixed() {
		global $has_contractor_actions;

		$popup_content = fw_get_db_settings_option('popup_content', '');
		$popup_content_button_text = fw_get_db_settings_option('popup_content_button_text', '');

		$popup_image = fw_get_db_settings_option('popup_image', '');
		$popup_target_url = fw_get_db_settings_option('popup_target_url', '');
		//$popup_button_text = fw_get_db_settings_option('popup_button_text', '');

		$zalo = fw_get_db_settings_option('zalo','');
		$zalo_label = fw_get_db_settings_option('zalo_label','');

		$hotline = fw_get_db_settings_option('hotline','');
		$hotline_label = fw_get_db_settings_option('hotline_label','');
		?>
		<div id="footer-buttons-fixed" class="position-fixed d-flex justify-content-center">
			<div class="w-100">
				<?php
					if(is_single()) {
						$post = get_queried_object();
						$attachment = 0;
						if(has_post_thumbnail($post)) {
							$attachment = get_post_thumbnail_id($post);
						}

						$get_premium = get_post_meta($post->ID, '_get_premium', true);
						$allow_order = get_post_meta($post->ID, '_allow_order', true);

						$product_order_button_text = get_option('product_order_button_text', '');
						$product_order_premium_button_text = get_option('product_order_premium_button_text', '');

						if($attachment) {
							?>
							<div class="actions-fixed hide">
								
								<?php
								
								if($allow_order=='yes' && $product_order_button_text!='') {
									echo '<div class="my-1">'.wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger d-block order-product'], esc_html($product_order_button_text)).'</div>';
								}
								if($get_premium=='yes' && $product_order_premium_button_text!='') {
									echo '<div class="my-1">'.wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'premium', 'class'=>'btn btn-danger d-block order-premium-product'], $product_order_premium_button_text).'</div>';	
								}
								
								?>
								
							</div>
							<?php
						}
					}
					?>
				<?php
				$footer_links = fw_get_db_settings_option('footer_links');
				if($footer_links) {
					foreach ($footer_links as $key => $value) {
					?>
					<div class="my-2">
						<a class="btn btn-danger d-block w-100 fw-bold" style="color:#ff0;" target="_blank" href="<?=esc_url($value['url'])?>"><?=esc_html($value['name'])?></a>
					</div>
					<?php
					}
				}
				
				if($popup_content!='' && $popup_content_button_text != '') {
				?>
				<!-- <div class="my-1">
					<button type="button" class="btn-popup-open btn-popup-content-open btn btn-danger d-block w-100 fw-bold" style="color:#ff0;" data-bs-toggle="modal" data-bs-target="#modal-popup"><?=esc_html($popup_content_button_text)?></button>
				</div> -->
				<?php
				}

				if($hotline!='' || $zalo!='' || ($popup_content!='' && $popup_content_button_text != '')) {
					?>
					<div class="hotline d-flex align-items-center mt-1 justify-content-end">
						<?php if($popup_content!='' && $popup_content_button_text != '' && !is_singular( 'contractor_page' )) { ?>
							<button type="button" class="btn-popup-open btn-popup-content-open btn btn-lg btn-danger d-block flex-grow-1 fw-bold" style="color:#ff0;" data-bs-toggle="modal" data-bs-target="#modal-popup"><?=esc_html($popup_content_button_text)?></button>
						<?php } ?>
						<?php if($zalo!='') { ?>
						<a class="zalo-button btn btn-danger d-block ms-2 btn-lg fw-bold text-yellow <?php //echo (!$has_contractor_actions)?'flex-grow-1':''; ?>flex-grow-1" href="https://zalo.me/<?=esc_attr($zalo)?>"><?=esc_html($zalo_label)?></a>
						<?php } ?>
						<?php if($hotline!='') { ?>
						<a class="alo-phone-img-circle d-block ms-2" href="tel:<?php echo esc_attr($hotline); ?>" title="<?php echo esc_attr($hotline_label); ?>"></a>
						<?php } ?>
					</div>
					<?php
				}
				
				?>
			</div>
		</div>

		<!-- modal -->
		<?php
		if($popup_content!='' && !is_singular( 'contractor_page' )) {
			?>
			<div class="modal fade" id="modal-popup" tabindex="-1">
				<div class="modal-dialog modal-lg modal-dialog-centered">
					<div class="modal-content rounded-0">
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						<div class="modal-body"><?php echo wp_format_content($popup_content); ?></div>
					</div>
				</div>
			</div>
			<?php
		}

		if($popup_image && $popup_target_url && !(is_home()||is_front_page())) {
			?>
			<div class="modal fade" id="modal-popup-image" tabindex="-1">
				<div class="modal-dialog modal-xl modal-dialog-centered">
					<div class="modal-content rounded-0">
						<button type="button" class="btn-close p-0" data-bs-dismiss="modal" aria-label="Close"><span class="dashicons dashicons-dismiss"></span></button>
						<div class="modal-body p-0">
							<a href="<?php echo esc_url($popup_target_url); ?>">
							<?php
								echo wp_get_attachment_image( $popup_image['attachment_id'], 'full', false );
							?>
							</a>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		?>
		<div class="modal" id="modal-video-player" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content rounded-0">
					<div class="modal-body p-0">
						<button type="button" class="btn-close p-0 position-absolute text-red" data-bs-dismiss="modal" aria-label="Đóng lại"><span class="dashicons dashicons-no-alt"></span></button>
						<div id="video-player">
							<div class="ratio ratio-16x9"></div>
						</div>
						<div id="video-link" class="text-center p-2 position-absolute w-100 start-0"></div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function display_widgets() {
		?>
		<div class="site-footer-inner container-xl">
			<div class="row">
				<?php if(is_active_sidebar( 'footer-1' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-1'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-2' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-2'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-3' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-3'); ?></div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	public function display_footer_html() {
		global $popup;
		if( !$popup ) {
			add_action('wp_footer', [$this, 'site_footer'], 10);
			add_action('wp_footer', [$this, 'footer_fixed'], 20);
			add_action('wp_footer', [$this, 'order_product_modal'], 20);
			add_action('wp_footer', [$this, 'logout_post_password'], 15);
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Footer::instance();