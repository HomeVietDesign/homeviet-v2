window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){

		function submit_apply_position(event) {
			let $form = $(event.currentTarget),
				$message = $form.find('.apply-position-message'),
				$submit = $form.find('.apply-position-submit'),
				$position = $form.find('[name="position"]'),
				$candidate_name = $form.find('[name="candidate_name"]'),
				$candidate_phone = $form.find('[name="candidate_phone"]');
				$token = $form.find('[name="cf-turnstile-response"]');
				patt_phone = /^0[1-9]\d{8}$/gm,
				position = $position.val(),
				phone = $candidate_phone.val(),
				name = $candidate_name.val(),
				token = $token.val();

			$message.html('');

			//console.log('token:'+token);

			if(phone=='' || name=='' || !patt_phone.test(phone)) {
				$message.html('<div class="text-danger">Bạn cần điền đầy đủ và hợp lệ tên, số điện thoại!</div>');
			} else {
				if(phone.startsWith('0')) {
					phone_number = "+84" + phone.slice(1, phone.length);
				} else if(phone.startsWith('+84')) {
					phone_number = phone;
				} else {
					phone_number = "+84" + phone;  
				}

				$message.html('<div class="text-info">Đang gửi...</div>');
				$submit.prop('disabled',true);

				let ref = getCookie('_ref');

				$.ajax({
					url: theme.ajax_url,
					type: 'post',
					data: {
						action: 'apply_position',
						name:name,
						phone:phone_number,
						position:position,
						token:token,
						url: window.location.href,
						ref: ref
					},
					dataType: 'json',
					beforeSend: function(xhr) {
						
					},
					success: function(response) {
						const eventApply = new CustomEvent('applyPosition', {
							bubbles: true,
							detail: { position:response.data.position, name:response.data.name, phone:response.data.phone, fb_pxl_code:response.fb_pxl_code }
						});

						if(response.code==1) {
							event.target.dispatchEvent(eventApply);
							$message.html('<div class="text-warning">'+response.msg+'</div>');
						} else {
							$message.html('<div class="text-danger">'+response.msg+'</div>');
						}
					},
					error: function() {
						$message.html('<div class="text-danger">Có lỗi khi gửi! Vui lòng tải lại trang rồi thử lại. Hoặc liên hệ với ban quản trị về sự cố này.</div>');
					},
					complete: function() {
						$submit.prop('disabled',false);
					}
				});

			}

		}

		$('.apply-position-form').on('submit', function(e){
			e.preventDefault();

			submit_apply_position(e);

			return false;

		}); // submit apply

	});
});