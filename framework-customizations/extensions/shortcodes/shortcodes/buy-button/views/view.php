<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(is_single()) {
	global $post;

	$allow_order = get_post_meta($post->ID, '_allow_order', true);

	$attachment = 0;
	if(has_post_thumbnail($post)) {
		$attachment = get_post_thumbnail_id($post);
	}

	$html_id = uniqid('fw-buy-button-');

	?>
	<div id="<?=$html_id?>" class="fw-shortcode-buy-button">
		<?php
		if($allow_order=='yes' && $attachment) {
			echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger order-product d-block rounded-0'], wp_format_content( $atts['text'] ));	
		}
		?>
	</div>
	<?php
}