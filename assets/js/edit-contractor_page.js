window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		//console.log('test');
		
		$(document).on('change', 'input._cat', function(e){
			let $this = $(this),
				nonce = $this.data('nonce'),
				id = $this.data('id'),
				cat = $this.val();
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'change_contractor_cat', cat: cat},
				beforeSend: function() {
					$this.prop('readonly', true);
				},
				success: function(response) {
					//console.log(response);
				},
				complete: function() {
					$this.prop('readonly', false);
				}
			})
		});
	});
});