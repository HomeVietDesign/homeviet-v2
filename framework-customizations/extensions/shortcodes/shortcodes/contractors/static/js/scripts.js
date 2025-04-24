document.addEventListener('DOMContentLoaded', function(e){
	//const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	//const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

	jQuery(function($){

		$(".change-province").select2({
			data: theme.provinces,
			width: '100%',
			allowClear: true,
			dropdownAutoWidth: true,
			dropdownCssClass: 'change-province-dropdown',
			placeholder: 'Chọn tỉnh'
		});

		$(document).on('submit', '.edit-external-url-form', function(e){
			e.preventDefault();
			let $form = $(this),
				data = $form.serialize(),
				$button_submit = $form.find('.edit-external-url-update'),
				$button_open = $form.find('.edit-external-url-open'),
				$button_copy = $form.find('.edit-external-url-copy'),
				$response_msg = $form.find('.response-msg'),
				urlParams = new URLSearchParams(data),
				$btn = $(document).find('.edit-external-url[data-id="'+urlParams.get('id')+'"]');
			
			$button_submit.prop('disabled', true);
			$response_msg.html('');
			$.ajax({
				url: theme.ajax_url+'?action=edit_external_url_update',
				method:'POST',
				data:data,
				dataType:'json',
				beforeSend:function(){
					$response_msg.html('Đang xử lý...');
				},
				success:function(response){
					if(response.code!=1) {
						$response_msg.removeClass('text-success');
						$response_msg.addClass('text-danger');
					} else {
						$response_msg.addClass('text-success');
						$response_msg.removeClass('text-danger');
					}
					$response_msg.html(response.msg);

					$btn.data('url', response.data);
					$button_open.attr('href', response.data);
					$button_copy.attr('data-text', response.data);
					if(response.data!='') {
						$button_copy.prop('disabled', false);
						$button_open.removeClass('disabled');
						$btn.removeClass('btn-warning');
						$btn.addClass('btn-success');;
					} else {
						$button_copy.prop('disabled', true);
						$button_open.addClass('disabled');
						$btn.addClass('btn-warning');
						$btn.removeClass('btn-success');
					}
				},
				complete: function() {
					$button_submit.prop('disabled', false);
				}
			});
			return false;
		});

		$('#edit-external-url-modal').on('show.bs.modal', function(e){
			let $button = $(e.relatedTarget),
				$modal = $(this),
				id = $button.data('id'),
				url = $button.data('url'),
				nonce = $button.data('nonce'),
				$btn_copy = $modal.find('.edit-external-url-copy'),
				$a_open = $modal.find('.edit-external-url-open'),
				$response_msg = $modal.find('.response-msg');
			
			$response_msg.html('');
			$modal.find('[name="id"]').val(id);
			$modal.find('[name="nonce"]').val(nonce);
			$modal.find('[name="external_url"]').val(url);
			if(url!='') {
				$btn_copy.prop('disabled', false);
				$a_open.removeClass('disabled');
			} else {
				$btn_copy.prop('disabled', true);
				$a_open.addClass('disabled');
			}
		}).on('hidden.bs.modal', function(e){
			let $modal = $(this);
			$modal.find('[name="id"]').val(0);
			$modal.find('[name="external_url"]').val('');
			$modal.find('.edit-external-url-copy').prop('disabled', true);
			$modal.find('.edit-external-url-open').addClass('disabled');
		});

		$(document).on('change', '.toggle-best', function(e){
			let $this = $(this),
				$section = $this.closest('.fw-shortcode-contractors'),
				uri = $section.find('[name="uri"]').val(),
				view = parseInt($section.find('[name="view"]').val()),
				id = parseInt($this.data('id')),
				best = $this.prop('checked'),
				nonce = $this.data('nonce');
			
			$.ajax({
				url: theme.ajax_url+'?action=toggle_best',
				method:'POST',
				data:{id: id, best: best, nonce: nonce, uri: uri, view: view},
				dataType:'json',
				beforeSend:function(){
					$this.prop('disabled', true);
				},
				success:function(response){
					//console.log(response);
				},
				complete: function() {
					$this.prop('disabled', false);
				}
			});
			
		});

		$('body').tooltip({
			selector: '[data-bs-toggle="tooltip"]'
		});

		$(document).on('click', '.copy-text', function(e){
			e.preventDefault();
			let $this = $(this);
			if(CopyToClipboard($this.data('text'))) {
				$('body').find('#'+$this.attr('aria-describedby')+' .tooltip-inner').html('Đã sao chép!');
			}
		});

		function load_contractors($section, paged=1, scrolltop=true) {
			let $list_el = $section.find('.list-contractors'),
				$pagination_links_el = $section.find('.contractor-paginate-links'),
				$paged_el = $section.find('[name="paged"]'),
				
				query = JSON.parse($section.find('[name="query"]').val()),
				view = parseInt($section.find('[name="view"]').val());

			$.ajax({
				url:theme.ajax_url+'?action=contractors_paginate',
				method:'GET',
				data:{
					query:query
					,paged:paged
					,view:view
				},
				beforeSend:function(){
					$section.find('.overlay').removeClass('invisible');
					if(scrolltop) {
						let offset_top = $section.offset().top;
						if($section.closest('.contractor-rating-container').length>0) offset_top = $section.closest('.contractor-rating-container').offset().top;
						if($('#site-header').length>0) offset_top -= $('#site-header').height();
						if($('.provinces').length>0) offset_top -= $('.provinces').height();
						if($('#wpadminbar').length>0) offset_top -= $('#wpadminbar').height();
						$('html,body').scrollTop(offset_top);
					}
				},
				success:function(response){
					//console.log(response);
					$paged_el.val(paged);
					$list_el.html(response['items']);
					$pagination_links_el.html(response['paginate_links']);

					$list_el.find(".change-province").select2({
						data: theme.provinces,
						width: '100%',
						allowClear: true,
						dropdownAutoWidth: true,
						dropdownCssClass: 'change-province-dropdown',
						placeholder: 'Chọn tỉnh'
					});
				},
				complete:function() {
					$section.find('.overlay').addClass('invisible');
				}
			});
			
		}

		let ajax_change_province = {};
		$(document).on('change', ".change-province", function(e){
			let $this = $(this),
				$section = $this.closest('.fw-shortcode-contractors'),
				uri = $section.find('[name="uri"]').val(),
				view = parseInt($section.find('[name="view"]').val()),
				id = parseInt($this.data('id')),
				nonce = $this.data('nonce'),
				provinces = $this.val();
			//console.log($this.val());
			if(ajax_change_province.hasOwnProperty('ajax'+id) && ajax_change_province['ajax'+id]!=null) ajax_change_province['ajax'+id].abort();
			
			ajax_change_province['ajax'+id] = $.ajax({
				url: theme.ajax_url+'?action=change_provinces',
				method:'POST',
				data:{id: id, provinces: provinces, uri: uri, view: view, nonce: nonce},
				dataType:'json',
				beforeSend:function(){

				},
				success:function(response){
					//console.log(response);
				},
				complete: function() {
					
				}
			});
		});

		$(document).on('click', '.contractor-paginate-links button.page-numbers', function(e){
			let $this = $(this),
				$section = $this.closest('.fw-shortcode-contractors'),
				paged = parseInt($this.data('paged'));

			$this.prop('disabled', true);
			load_contractors($section, paged);
		});

		$(document).on('click', '.contractor-arrange', function(e){
			let $this = $(this),
				$section = $this.closest('.fw-shortcode-contractors'),
				arrange = $this.data('arrange'),
				id = parseInt($this.data('id')),
				nonce = $this.data('nonce'),
				uri = $section.find('[name="uri"]').val(),
				paged = parseInt($section.find('[name="paged"]').val()),
				view = parseInt($section.find('[name="view"]').val());

			$.ajax({
				url: theme.ajax_url+'?action=contractor_arrange',
				method:'POST',
				data:{id: id, arrange: arrange, nonce: nonce, view: view, uri: uri},
				dataType:'json',
				beforeSend:function(){
					$this.prop('disabled', true);
				},
				success:function(response){
					if(response.code) {
						load_contractors($section, paged, false);
					}
				},
				complete: function() {
					$this.prop('disabled', false);
				}
			});
		});

	});
});