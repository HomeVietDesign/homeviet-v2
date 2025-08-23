window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		//console.log(ajaxurl);

		$('button.send-purchase').on('click', function(e){
			let $this = $(this),
				$wrap = $this.closest('.product-order-tasks'),
				$btn_cancel = $wrap.find('button.cancel-purchase'),
				id = $this.data('id'),
				nonce = $this.data('nonce');

			$this.prop('disabled', true);
			$btn_cancel.prop('disabled', true);

			if(confirm('Duyệt đơn #'+id+'?')) {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action: 'send_purchase', id:id, nonce:nonce},
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(response) {
						if(response.code==1) {
							$wrap.html('<span class="purchased">Đã duyệt</span>');
						} else {
							$this.prop('disabled', false);
							$btn_cancel.prop('disabled', false);
						}
					},
					error: function() {
						$this.prop('disabled', false);
						$btn_cancel.prop('disabled', false);
					}
				});
			} else {
				$this.prop('disabled', false);
				$btn_cancel.prop('disabled', false);
			}

		});

		$('button.cancel-purchase').on('click', function(e){
			let $this = $(this),
				$wrap = $this.closest('.product-order-tasks'),
				$btn_send = $wrap.find('button.send-purchase'),
				id = $this.data('id'),
				nonce = $this.data('nonce');

			$this.prop('disabled', true);
			$btn_send.prop('disabled', true);

			if(confirm('Hủy đơn #'+id+'?')) {
				$.ajax({
					url: ajaxurl,
					type: 'POST',
					data: {action: 'cancel_purchase', id:id, nonce:nonce},
					dataType: 'json',
					beforeSend: function() {

					},
					success: function(response) {
						if(response.code==1) {
							$wrap.html('<span class="canceled">Đã hủy</span>');
						} else {
							$this.prop('disabled', false);
							$btn_send.prop('disabled', false);
						}
					},
					error: function() {
						$this.prop('disabled', false);
						$btn_send.prop('disabled', false);
					}
				});
			} else {
				$this.prop('disabled', false);
				$btn_send.prop('disabled', false);
			}
			
		});

	});
});