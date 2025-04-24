<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

$video = isset($atts['video'])?$atts['video']:false;
$video_url = isset($atts['video_url'])?$atts['video_url']:false;

$thumbnail = isset($atts['thumbnail'])?$atts['thumbnail']:false;
$autoplay = isset($atts['autoplay'])?$atts['autoplay']:false;
$controls = isset($atts['controls'])?$atts['controls']:false;
$loop = isset($atts['loop'])?$atts['loop']:false;

if($video_url || $video) {
	$video_html = '<video class="lazyvideo" width="100%" preload="none" playsinline data-src="'.esc_url( ($video_url)?$video_url:wp_get_attachment_url( $video['attachment_id'] ) ).'" type="video/mp4"';
	if($thumbnail) {
		$video_html .= ' data-poster="'.esc_url(wp_get_attachment_url($thumbnail['attachment_id'])).'"';
	}
	if($autoplay) {
		$video_html .= ' autoplay muted';
	}

	if(!$autoplay || $controls){
		$video_html .= ' controls';
	}

	if($loop) {
		$video_html .= ' loop';
	}

	$video_html .= '>';
	$video_html .= '</video">';
?>
<div class="video-wrapper shortcode-container">
	<?php echo $video_html; ?>
</div>
<?php
}