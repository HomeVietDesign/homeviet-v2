window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('label[for="tag-name"],label[for="name"]').html('Số điện thoại');
		$('label[for="tag-description"],label[for="description"]').html('Tên gọi');
		$('#description-description').html('Tên gọi hiển thị thay cho số điện thoại để dễ nhận biết.');

		$(document).on('change', 'input.external_url', function(e){
			let $input = $(this),
				external_url = $input.val(),
				id = $input.data('id'),
				nonce = $input.data('nonce');
			$input.prop('readonly', true);
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {action: 'change_client_external_url', id: id, nonce: nonce, external_url: external_url},
				dataType: 'json',
				beforeSend: function() {

				},
				success: function(response) {
					$input.val(response['data']);
				},
				complete: function() {
					$input.prop('readonly', false);
				}
			});
		});
	});
});