<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['image'])) {
	// Tạo ID ngẫu nhiên để tránh trùng khi dùng nhiều lần
	$id = 'panorama_' . wp_generate_password( 6, false, false );
	?>
	<div class="fw-shortcode-panorama">
		<div class="panorama-container" style="padding-bottom:<?php echo esc_attr($atts['ratio']); ?>%;">
			<div class="panorama-viewer" id="<?php echo esc_attr($id); ?>" data-src="<?=esc_url(wp_get_attachment_url( $atts['image']['attachment_id'] ))?>"></div>
		</div>
	</div>
	<?php
}
