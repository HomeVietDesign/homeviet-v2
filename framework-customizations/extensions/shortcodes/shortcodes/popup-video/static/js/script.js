let pvm = document.getElementById('popup-video-shortcode-modal');

pvm.addEventListener('shown.bs.modal', function (event) {
  let button = jQuery(event.relatedTarget);
  let modal = jQuery(this);
  let video_html = '<div class="popup-video-shortcode-play-wrap ratio-'+button.data('ratio')+'"><iframe src="'+button.data('video').url+'" frameborder="0" allowfullscreen="" allow="autoplay; encrypted-media" autoplay data-hj-allow-iframe="true"></iframe></div>';
  modal.find('.modal-body').html(video_html);
});

pvm.addEventListener('hidden.bs.modal', function (event) {
  //console.log(this);
  let modal = jQuery(this);
  modal.find('.modal-body').find('.popup-video-shortcode-play-wrap').remove();
});