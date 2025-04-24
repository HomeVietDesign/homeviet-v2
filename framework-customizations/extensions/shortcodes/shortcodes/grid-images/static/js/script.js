window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		
		let $grid_masonry = $('.shortcode-grid-images.layout-masonry');
		if($grid_masonry.length>0) {
			$grid_masonry.imagesLoaded(function(){
				$grid_masonry.isotope();
			});
		}

		$('.grid-images-viewmore-button').on('click', function(e){
			let $btn = $(this),
				$wrap = $btn.closest('.fw-shortcode-grid-images'),
				$grid = $wrap.find('.shortcode-grid-images')
				$viewmore_wrap = $btn.closest('.grid-images-viewmore-wrap'),
				$loaded_bar = $viewmore_wrap.find('.loaded-bar'),
				$loaded_page = $viewmore_wrap.find('.loaded-page'),
				page = parseInt($btn.attr('data-page')),
				next_page = page+1;
				pages = $btn.data('pages');

			if(next_page<=pages) {
				$grid.find('.filter-'+next_page).show();
				$loaded_bar.width((next_page*100/pages)+'%');
				$loaded_page.text(next_page);
				$btn.attr('data-page', next_page);

				if($grid.hasClass('layout-masonry')) {
					$grid.isotope('layout');
				}
			}

		});
	});
});