window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('.panorama-viewer').each(function(index, el) {
			//let container = document.getElementById($(el).attr('id'))
			new PhotoSphereViewer.Viewer({
				container: el,
				panorama: $(el).data('src')
			});
		})
		
	});
});
