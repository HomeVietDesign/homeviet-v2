<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
//$html_id = uniqid('popup-video-shortcode');

$ratio = ( !empty( $atts['ratio'] ) ) ? sanitize_html_class($atts['ratio']) : '16x9';

?>
<div class="popup-video-wrapper shortcode-container">
	<a href="javascript:;" data-bs-toggle="modal" data-bs-target="#popup-video-shortcode-modal" data-video="<?=esc_attr(json_encode($atts['video']))?>" data-ratio="<?=esc_attr($ratio)?>">
		<?php
		echo wp_get_attachment_image( $atts['image']['attachment_id'], $atts['size'], false );
		?>
		<span class="icon-play"><i></i></span>
	</a>
</div>

