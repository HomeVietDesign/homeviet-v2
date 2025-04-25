<?php
namespace HomeViet;

class Head {

	private static $instance = null;

	private function __construct() {
		//add_action('wp_head', [$this, 'youtube_api_scripts'], 10);
		add_action('wp_head', [$this, 'head_scripts'], 50);
		add_action('wp_head', [$this, 'noindex'], 10);
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

	public function youtube_api_scripts() {
		?>
		<script>
		      // This code loads the IFrame Player API code asynchronously.
		      var tag = document.createElement('script');

		      tag.src = "https://www.youtube.com/iframe_api";
		      var firstScriptTag = document.getElementsByTagName('script')[0];
		      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

		 </script>
		<?php
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
			.grecaptcha-badge {
				right: -999999px!important;
			}

			/*@media (min-width: 576px) {
				
			}*/
		</style>
		<script type="text/javascript">
			window.addEventListener('DOMContentLoaded', function(){
				const root = document.querySelector(':root');
				root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
				root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
				//console.log(document.getElementById('footer-buttons-fixed').clientHeight);
				window.addEventListener('resize', function(){
					root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
					root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
					//console.log(document.getElementById('footer-buttons-fixed').clientHeight);
				});
			});
		</script>
		<?php
		$custom_script = fw_get_db_settings_option('head_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
		?>
		<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
		<?php

	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Head::instance();