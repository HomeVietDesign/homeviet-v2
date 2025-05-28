<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(is_single()) {
	global $post;

	$allow_order = get_post_meta($post->ID, '_allow_order', true);
	$product_order_button_text = get_option('product_order_button_text', '');

	$attachment = 0;
	if(has_post_thumbnail($post)) {
		$attachment = get_post_thumbnail_id($post);
	}

	if($allow_order=='yes' && $attachment && $product_order_button_text!='') {
	?>
	<div class="fw-shortcode-buy-button">
		<?php
		echo wp_do_shortcode( 'order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-lg btn-danger order-product d-block fw-bold'], $product_order_button_text );
		?>	
	</div>
	<?php
	}
}