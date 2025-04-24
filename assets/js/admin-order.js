window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		function nl2br(str, replaceMode=true, isXhtml=true) {
			var breakTag = (isXhtml) ? '<br />' : '<br>';
			var replaceStr = (replaceMode) ? '$1'+ breakTag : '$1'+ breakTag +'$2';
			return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
		}

		$('.item-note-input').on('change', function(e){
			let $this = $(this),
				$wrap = $this.closest('.item-note-edit-wrap'),
				$display = $wrap.find('.item-note-display'),
				id = $this.data('id'),
				nonce = $this.data('nonce'),
				note = $this.val();
			$this.prop('readonly', true);
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {action: 'change_order_item_note', id:id, nonce:nonce, note:note},
				dataType: 'json',
				beforeSend: function() {

				},
				success: function(response) {
					if(response.code==1) {
						$display.html(nl2br(response.data));
					}
				},
				complete: function() {
					$this.prop('readonly', false);
					$wrap.removeClass('editing');
				}
			});
		});
		$('.edit-item-note').on('click', function(e){
			let $this = $(this), $wrap = $this.closest('.item-note-edit-wrap');
			$wrap.toggleClass('editing');
			if($wrap.hasClass('editing')) $wrap.find('.item-note-input').focus();
		});
	});
});