window.addEventListener('DOMContentLoaded', function(){
	jQuery(document).ready(function($) {
		if ('IntersectionObserver' in window) {
			function handleIntersectionBgimage(entries) {
				entries.map((entry) => {
					if (entry.isIntersecting) {
						// Item has crossed our observation
						// threshold - load src from data-src
						entry.target.style.backgroundImage = "url('"+entry.target.dataset.background+"')";
						// Job done for this item - no need to watch it!
						observer.unobserve(entry.target);
					}
				});
			}

			const bgimages = document.querySelectorAll('[data-background]');
			const observer = new IntersectionObserver(
				handleIntersectionBgimage,
				{ rootMargin: "100px" }
			);
			bgimages.forEach(bgimage => observer.observe(bgimage));
		} else {
			const bgimages = document.querySelectorAll('[data-background]');
			bgimages.forEach(bgimage => {
				bgimage.style.backgroundImage = "url('"+bgimage.dataset.background+"')";
			});
		}
	});
});
