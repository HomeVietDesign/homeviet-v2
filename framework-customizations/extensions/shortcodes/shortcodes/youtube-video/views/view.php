<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

//global $wp_embed;

if($atts['vid']!='') {
	$ratio = ( !empty( $atts['ratio'] ) ) ? sanitize_html_class($atts['ratio']) : '16x9';

	//$iframe = '<iframe class="embed-responsive-item" width="300" height="200" src="https://www.youtube.com/embed/'.esc_attr($atts['vid']).'?feature=oembed" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>';

	$iframe_id = wp_unique_id('video-');

	$start = ($atts['start']!='')?absint($atts['start']):'';
	$end = ($atts['end']!='')?absint($atts['end']):'';

	$settings = [
		'autoplay' => absint($atts['autoplay']),
		'controls' => absint($atts['controls']),
		'loop' => absint($atts['loop']),
		'fs' => absint($atts['fs']),
		'enablejsapi' => 1,
		'modestbranding' => 1,
		'playsinline' => 1,
		'rel' => 0,
	];
	if($start!='') {
		$settings['start'] = $start;
	}
	if($end!='') {
		$settings['end'] = $end;
	}

	$embed_src = add_query_arg($settings, 'https://www.youtube.com/embed/'.$atts['vid']);
	?>
	<div class="video-wrapper shortcode-container">
		<div class="ratio ratio-<?=esc_attr($ratio)?>">
			<div id="<?=esc_attr($iframe_id)?>" class="yt-video-iframe" data-id="<?=esc_attr($atts['vid'])?>" data-settings="<?=esc_attr(json_encode($settings))?>"></div>
		</div>

	</div>
	<?php
}
