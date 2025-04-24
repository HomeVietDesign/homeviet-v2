window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('.fw-shortcode-owlcarousel .owl-carousel').each(function(index, el){
			let $owl = $(this), options = $owl.data('options');
		
			$owl.owlCarousel({
				items:1,
				lazyLoad:true,
				loop:true,
				autoHeight:true,
				margin:0,
				nav: options['navs'],
				dots: options['dots'],
				navText: ['<span class="dashicons dashicons-arrow-left"></span>','<span class="dashicons dashicons-arrow-right"></span>'],
				autoplayHoverPause: true,
				autoplay: options['autoplay'],
				autoplayTimeout: options['autoplayTimeout']
			});
		});
	});
});
