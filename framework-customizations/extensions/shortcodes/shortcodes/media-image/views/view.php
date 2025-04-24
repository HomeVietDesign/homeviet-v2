<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

if ( empty( $atts['image'] ) ) {
	return;
}

$image = $atts['image']['url'];

if(!empty($atts['image'])) {
	?>
	<div class="fw-shortcode-image shortcode-image">
	<?php
	if ( !empty( $atts['link'] ) ) {
		?>
		<a class="link" href="<?=esc_attr($atts['link'])?>" target="<?=esc_attr($atts['target'])?>">
		<?php
	}
	echo wp_get_attachment_image( $atts['image']['attachment_id'], $atts['size'], false, ['alt'=>esc_attr($atts['alt'])] );

	if ( !empty( $atts['link'] ) ) {
		?>
		</a>
		<?php
	}
	?>
	</div>
	<?php
}
