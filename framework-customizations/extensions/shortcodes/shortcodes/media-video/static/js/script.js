window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('.shortcode-video-play').on('click', function(e){
			let _this = $(this),
				video = _this.data('video'),
				icon = _this.find('.play-icon'),
				player = _this.find('.shortcode-video-player'),
				ratio = player.find('.ratio');
			//console.log(player);
			if(!_this.hasClass('playing')) {
				_this.addClass('playing');
				let video_html = $('<video width="1280" controls loop playsinline><source src="'+video.url+'" type="video/mp4"></video>');
				ratio.html(video_html);
				icon.addClass('hide');
				player.removeClass('hide');
				video_html[0].play();
				//video_html.play();
			} else {
				_this.removeClass('playing');
				icon.removeClass('hide');
				player.addClass('hide');
				ratio.html('');
			}
		});
	});
});