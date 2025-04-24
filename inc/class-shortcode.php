<?php
namespace HomeViet;

class Shortcode {

	private static $instance = null;

	private function __construct() {
		
		add_shortcode( 'order_product', [$this, 'order_product_shortcode'] );
		
	}

	public function order_product_shortcode( $atts, $content ) {
		extract($atts); // attachment, code, class

		$image_src = wp_get_attachment_image_src( $attachment, 'large' );
		$src = ($image_src) ? $image_src[0] : '';

		$image_src_full = wp_get_attachment_image_src( $attachment, 'full' );
		$src_full = ($image_src_full) ? $image_src_full[0] : '';

		ob_start();
		?>
		<a class="<?php echo esc_attr($class); ?>" style="cursor: pointer;" href="#order-product" data-attachment="<?=absint($attachment)?>" data-code="<?=esc_attr(isset($code)?$code:'')?>" data-src="<?=esc_url($src_full)?>" data-src-medium="<?=esc_url($src)?>" data-type="<?=esc_attr($type)?>" data-id="<?=absint($id)?>" data-bs-toggle="modal"><?=$content?></a>
		<?php
		return ob_get_clean();

	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Shortcode::instance();