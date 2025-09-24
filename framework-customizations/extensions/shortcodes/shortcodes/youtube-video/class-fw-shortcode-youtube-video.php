<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Youtube_Video extends FW_Shortcode
{

	public function _init()
	{
		// require thư viện youtube_api_scripts

		//add_action( 'wp_footer', [$this, 'youtube_api'] );
	}

	public function youtube_api() {
		?>
		<script type="text/javascript" id="shortcode-youtube-video">
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

		function onPlayerStateChange(event) {
			//console.log(event);
			let settings = JSON.parse(event.target.g.dataset.settings);
			if(settings.loop && event.data == YT.PlayerState.ENDED) {
				//event.target.mute();
				event.target.playVideo();
			}
			if(event.data == YT.PlayerState.PAUSED || event.data == YT.PlayerState.UNSTARTED || event.data == YT.PlayerState.CUED) {
				event.target.g.closest('.shortcode-youtube-video').classList.add('paused');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
			} else if(event.data == YT.PlayerState.PLAYING) {
				event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
				event.target.g.closest('.shortcode-youtube-video').classList.add('playing');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
			} else if(event.data == YT.PlayerState.BUFFERING) {
				event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
				event.target.g.closest('.shortcode-youtube-video').classList.add('buffering');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('ended');
			} else if(event.data == YT.PlayerState.ENDED) {
				event.target.g.closest('.shortcode-youtube-video').classList.remove('paused');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('playing');
				event.target.g.closest('.shortcode-youtube-video').classList.remove('buffering');
				event.target.g.closest('.shortcode-youtube-video').classList.add('ended');
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
						height: '1080',
						width: '1920',
						videoId: el.dataset.id,
						playerVars: JSON.parse(el.dataset.settings),
						events: {
							'onReady': onPlayerReady,
							'onStateChange': onPlayerStateChange
						}
					});
					yt_players.push(player);
				});
			}

			let plays = document.querySelectorAll('.shortcode-youtube-video .play');
			let pauses = document.querySelectorAll('.shortcode-youtube-video .pause');
			plays.forEach(function(play) {
				play.addEventListener("click", function() {
					//console.log('play');
					//console.log(yt_players[play.dataset.index]);
					yt_players[play.dataset.index].playVideo();
				});
			});
			pauses.forEach(function(pause) {
				pause.addEventListener("click", function() {
					//console.log('pause');
					//console.log(yt_players[pause.dataset.index]);
					yt_players[pause.dataset.index].pauseVideo();
				});
			});
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
