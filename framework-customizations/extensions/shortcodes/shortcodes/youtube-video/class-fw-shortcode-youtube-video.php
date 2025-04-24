<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Youtube_Video extends FW_Shortcode
{

	public function _init()
	{
		// require thư viện youtube_api_scripts

		add_action( 'wp_footer', [$this, 'youtube_api'] );
	}

	public function youtube_api() {
		?>
		<script type="text/javascript">
		// This code loads the IFrame Player API code asynchronously.
		var tag = document.createElement('script');

		tag.src = "https://www.youtube.com/iframe_api";
		var firstScriptTag = document.getElementsByTagName('script')[0];
		firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		
		function onPlayerReady(event) {
			let settings = JSON.parse(event.target.g.dataset.settings);
			if(settings.autoplay) {
				event.target.mute();
				event.target.playVideo();
			}
		}

		function onYouTubeIframeAPIReady() {

		//window.YT.ready(function() {
			let yt_players = [],
				yt_frames = document.querySelectorAll('.yt-video-iframe');

			if(yt_frames.length>0) {
				yt_frames.forEach(function(el){
					//console.log(JSON.parse(el.dataset.settings));
					let player = new YT.Player(el.id, {
						height: '720',
						width: '1280',
						videoId: el.dataset.id,
						playerVars: JSON.parse(el.dataset.settings),
						events: {
							'onReady': onPlayerReady
						}
					});
					yt_players.push(player);
				});
			}

			// function onPlayerStateChange() {

			// }

			if ("IntersectionObserver" in window) {
				let videoObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(video) {
						if (!video.isIntersecting) {
							video.target.contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
						}	
					});
				}, {rootMargin: "0px",threshold: 0.5});

				document.querySelectorAll('iframe.yt-video-iframe').forEach(function(video) {
					videoObserver.observe(video);
				});
			}

		//});
		}
		</script>
		<?php
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		$atts['vid'] = self::get_youtube_id(trim( $atts['url'] ));

		$this->enqueue_static();
		return fw_render_view($this->locate_path('/views/view.php'), compact('atts', 'content', 'tag'));
	}

	public static function get_youtube_id($url) {
		$pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube(?:-nocookie)?\.com(?:/embed/|/v/|/watch\?v=))([\w-]{10,12})#';
		$result = preg_match($pattern, $url, $matches);
		if (false !== boolval($result)) {
			return $matches[1];
		}
		return sanitize_html_class($url);
	}
}
