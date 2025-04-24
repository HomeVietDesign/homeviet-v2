<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['images'])) {
	$owlcarousel_id = wp_unique_id('owlcarousel-');

	$interval = isset($atts['interval']) ? 1000*absint($atts['interval']) : 5000;
	$autoplay = isset($atts['autoplay']) ? $atts['autoplay'] : 'no';
	$dots = isset($atts['dots']) ? $atts['dots'] : 'no';
	$navs = isset($atts['navs']) ? $atts['navs'] : 'no';
	?>
	<div id="<?=esc_attr($owlcarousel_id)?>" class="fw-shortcode-owlcarousel">
		<div class="owl-carousel" data-options="<?php echo esc_attr(json_encode(['dots'=>($dots=='yes')?1:0,'navs'=>($navs=='yes')?1:0,'autoplay'=>($autoplay=='yes')?1:0,'autoplayTimeout'=>$interval])); ?>">
			<?php
			foreach ($atts['images'] as $key => $image) {
				?>
				<img class="owl-lazy" data-src="<?php echo esc_url(wp_get_attachment_url( $image['attachment_id'] )); ?>">
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
