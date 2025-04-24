window.addEventListener('DOMContentLoaded',function(){
	jQuery(function($){
		const counterUp = window.counterUp.default;

		const callback = entries => {
			entries.forEach( entry => {
				const el = entry.target;
				if ( entry.isIntersecting && ! el.classList.contains( 'is-visible' ) ) {
					counterUp( el, {
						duration: 2000,
						delay: 16,
					} )
					el.classList.add( 'is-visible' );
				}
			} )
		}

		const IO = new IntersectionObserver( callback, { threshold: 1 } );

		const els = document.querySelectorAll( '.counter' );
		els.forEach(el => {

			IO.observe( el );
		})
		
	});
});