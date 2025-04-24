window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		function add_query_url(key,value,url) {
			let new_url = new URL(url);
			let search_params = new_url.searchParams;
			search_params.append(key, value);
			new_url.search = search_params.toString();
			return new_url.toString();
		}

		function remove_query_url(key,url) {
			let new_url = new URL(url);
			let search_params = new_url.searchParams;
			search_params.delete(key);
			new_url.search = search_params.toString();
			return new_url.toString();
		}

		$(document).on('change', 'select.change-parent', function(e){
			let $this = $(this),
				id = parseInt($this.attr('id').replace('cat-', '')),
				nonce = $this.attr('aria-describedby'),
				parent = parseInt($this.val());

			$('select.change-parent').prop('disabled', true);
			
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'admin_change_contractor_cat', parent: parent},
				beforeSend: function() {
					
				},
				success: function(response) {
					if(response) {
						window.location.reload();
					}
				},
				complete: function() {
					$('select.change-parent').prop('disabled', false);
				}
			});
		});

		let current_url = new URL(window.location.href);
		let parent = current_url.searchParams.has('parent') ? parseInt(current_url.searchParams.get('parent')) : 0;
		//console.log(current_url.searchParams);
		let parent_only = '<div class="parent-only alignleft"><label><input type="checkbox" name="parent_only" value="1"'+(current_url.searchParams.has('parent_only')?' checked':'');

			parent_only += '> Chỉ hiện cấp 0</label></div>';
	
		$.ajax({
			url: ajaxurl,
			type: 'get',
			//dataType: 'json',
			data: {parent: parent, action:'admin_get_contractor_cat_parents'},
			beforeSend: function() {
				
			},
			success: function(response) {
				$(response).insertBefore('.tablenav-pages');
			},
			complete: function() {
				
			}
		});
		
		$(parent_only).insertBefore('.tablenav-pages');

		$(document).on('change', '.parent-only [name="parent_only"]', function(e){
			$(this).prop('disabled', true);
			if($(this).prop('checked')) {
				current_url = add_query_url('parent_only', 1, current_url);
			} else {
				current_url = remove_query_url('parent_only', current_url);
			}
			window.location.href = current_url;
		});

		$(document).on('change', 'select.filter-parent', function(e){
			$(this).prop('disabled', true);
			let parent = $(this).val();
			if(parent==0) {
				current_url = remove_query_url('parent', current_url);
			} else {
				current_url = add_query_url('parent', parent, current_url);
			}
			
			window.location.href = current_url;
		});

		$(document).on('change', 'textarea.term-note', function(e){
			let $this = $(this),
				id = parseInt($this.data('id')),
				nonce = $this.data('nonce'),
				note = $this.val();

			$this.prop('readonly', true);
			
			$.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {id: id, nonce: nonce, action:'admin_change_contractor_cat_note', note: note},
				beforeSend: function() {
					
				},
				success: function(response) {
					$this.val(response);
				},
				complete: function() {
					$this.prop('readonly', false);
				}
			});
		});

	});//jQuery
});