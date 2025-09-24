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

	$iframe_index = wp_unique_id();

	$start = ($atts['start']!='')?absint($atts['start']):'';
	$end = ($atts['end']!='')?absint($atts['end']):'';

	$settings = [
		'autoplay' => absint($atts['autoplay']),
		'mute' => 1,
		'controls' => absint($atts['controls']),
		'loop' => absint($atts['loop']),
		'fs' => absint($atts['fs']),
		'enablejsapi' => 1,
		'modestbranding' => 1,
		'playsinline' => 1,
		'iv_load_policy' => 3,
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
	<div class="shortcode-youtube-video shortcode-container">
		<div class="ratio ratio-<?=esc_attr($ratio)?>">
			<div id="video-<?=esc_attr(uniqid($iframe_index))?>" class="yt-video-iframe" data-id="<?=esc_attr($atts['vid'])?>" data-settings="<?=esc_attr(json_encode($settings))?>"></div>
			<div class="play" data-index="<?=esc_attr($iframe_index-1)?>"><svg height="48" version="1.1" viewBox="0 0 68 48" width="68"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f03"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg></div>
			<div class="pause" data-index="<?=esc_attr($iframe_index-1)?>"></div>
		</div>
	</div>
	<?php
}
