window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		if ('IntersectionObserver' in window) {
			function handleIntersectionLazyVideos(entries) {
				entries.map((entry) => {
					if (entry.isIntersecting) {
						//console.log(entry.target.dataset.poster);
						if(entry.target.dataset.poster) {
							entry.target.poster = entry.target.dataset.poster;
						}
						entry.target.src = entry.target.dataset.src;
						observer.unobserve(entry.target);
					}
				});
			}

			const lazyVideos = document.querySelectorAll('video.lazyvideo');
			const observer = new IntersectionObserver(
				handleIntersectionLazyVideos,
				{ rootMargin: "100px" }
			);
			lazyVideos.forEach(lazyVideo => observer.observe(lazyVideo));
		} else {
			
			const lazyVideos = document.querySelectorAll('video.lazyvideo');
			lazyVideos.forEach(lazyVideo => {
				if(lazyVideo.dataset.poster) {
					lazyVideo.poster = lazyVideo.dataset.poster;
				}
				lazyVideo.src = lazyVideo.dataset.src;
			});
		}
	});
});