window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		// $('#contractor_rating-pop input[type="checkbox"], #contractor_rating-all input[type="checkbox"]').on('change', function(e){
		// 	let $this = $(this),
		// 		checked = $this.prop('checked'),
		// 		contractor_rating_top = (adminContractor.contractor_rating_top.length>0)?adminContractor.contractor_rating_top[0]:0;
			
		// 	if($this.val()==contractor_rating_top && checked) {
		// 		$('#passwords-pop input[type="checkbox"], #passwords-all input[type="checkbox"]').prop('checked', false);
		// 	}
		// });

		let phone_number_el = $('#fw-option-_phone_number');
			phone_number_el.parent().append('<span id="_phone_number_error" class="required"></span>'),
			ajax_check = null;;

		function check_contractor_exists() {
			let phone_number = phone_number_el.val(),
				id = $('#post_ID').val(),
				msg_phone_number_el = phone_number_el.next('#_phone_number_error'),
				not_exists = false;

			// if(''==phone_number) {
			// 	phone_number_el.focus();
			// 	msg_phone_number_el.html('Thiếu số điện thoại!');
			// } else {
				phone_number = sanitize_phone_number(phone_number);
				// if(''==phone_number) {
				// 	phone_number_el.focus();
				// 	msg_phone_number_el.html('Số điện thoại chưa hợp lệ!');
				// } else {
					if(ajax_check!=null) ajax_check.abort();
					ajax_check = $.ajax({
						url: ajaxurl,
						type: 'post',
						dataType: 'json',
						async: false,
						data: {action:'check_contractor_exists', phone_number:phone_number, id:id},
						beforeSend: function(xhr) {
							msg_phone_number_el.html('Đang kiểm tra...');
						},
						success: function(response) {
							if(response) {
								msg_phone_number_el.focus();
								msg_phone_number_el.html('Số điện thoại đã tồn tại!');
							} else {
								msg_phone_number_el.html('');
								not_exists = true;
							}
						}
					});
				// }
			//}

			return not_exists;
		}

		$('#publish').on('click', function(e){
			let not_exists = check_contractor_exists();
			if(!not_exists) {
				$('html,body').scrollTop($('#fw-option-_phone_number').closest('.postbox-container').offset().top);
			}
			return not_exists;
		});
		phone_number_el.on('keyup change', function(e){
			check_contractor_exists();
		});
	});
});