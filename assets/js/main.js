window.addEventListener('DOMContentLoaded', function(){
	
	// console.log(document.referrer);
	// console.log(window.atob(getCookie('_ref')).split(','));

	jQuery(function($){


		function check_input_phone_number(p) {
			const patt = /^(\+?\d{1,3}[-.\s]?)?(\(?\d{3}\)?[-.\s]?)?\d{3}[-.\s]?\d{4}$/;
			return patt.test(p);
		}

		function check_validity($form) {
			let valid = true;
			
			$form.find('input.wpcf7-form-control').each(function(index, el){
				let $el = $(el);

				//console.log($el.attr('aria-required'));

				switch(el.type) {
					case 'text':
						
						if(($el.attr('aria-required')=='true' && $el.val()=='') || el.validity.tooLong) {
							valid = false;
						}
						break;
					case 'tel':
						if(($el.attr('aria-required')=='true' && $el.val()=='') || !check_input_phone_number(el.value)) {
							valid = false;
						}
						break;
				}

			});

			return valid;
		}

		
		$(document).on('keyup', 'input.wpcf7-form-control', function(e){
			let $form = $(this).closest('form'),
				$submit_button = $form.find('[type="submit"]');

			if(check_validity($form)) {
				$submit_button.prop('disabled', false);
			} else {
				$submit_button.prop('disabled', true);
			}

		});

		$(document).on('submit', '.wpcf7', function( event ) {
			$(this).find('[type="submit"]').prop('disabled', true);
		});
		

		let debounced_contractor_search = debounce((event) => {
			let kw = $('#contractor-search-input').val().trim(),
				province = parseInt($('#contractor-search-province').val()),
				view = parseInt($('#contractor-search-view').val()),
				$result_wrap_el = $('#contractor-search-result-wrap'),
				$result_el = $result_wrap_el.find('.contractor-search-result'),
				$loading = $result_wrap_el.find('.loading');

			$loading.removeClass('invisible');
			$result_el.addClass('invisible');

			if(kw.length>2) {
				$loading.html('Đang tìm kiếm...');
				$.ajax({
					url: theme.ajax_url+'?action=contractor_search',
					method:'POST',
					data:{kw: kw, view: view, province: province},
					//dataType:'json',
					beforeSend:function(){
						
					},
					success:function(response){
						//console.log(response);
						$result_el.html(response);
						$result_el.removeClass('invisible');
						$loading.addClass('invisible');

						$result_el.find(".change-province").select2({
							data: theme.provinces,
							width: '100%',
							allowClear: true,
							dropdownAutoWidth: true,
							dropdownCssClass: 'change-province-dropdown',
							placeholder: 'Chọn tỉnh'
						});
					},
					complete: function() {
						
					}
				});
			} else if(kw.length>1) {

				$loading.html('Nhập từ khóa từ 3 ký tự trở lên');
			} else {
				$result_el.html('');
				$loading.addClass('invisible');
			}
		}, 800); // Wait 800ms after the last keypress

		$(document).on('input', '#contractor-search-input', function(event) {
			debounced_contractor_search(event);
		});
		
		$('.logout-post-password').on('click', function(e){
			e.preventDefault();
			let $this = $(this),
				url = $this.data('url');

			$.ajax({
				url:theme.ajax_url+'?action=url_delete_cache',
				method:'GET',
				data:{url:url},
				beforeSend:function(){
					$this.prop('disabled', true);
				},
				success:function(){
					deleteCookie('wp-postpass_'+$this.data('hash'));
					$this.remove();
					location.href = url;
				}
			});
			
		});

		function set_vh_size() {
			let vh = $(window).innerHeight();
			if($('#site-header').length>0) {
				vh -= $('#site-header').height();
			}
			if($('#wpadminbar').length>0) {
				vh -= $('#wpadminbar').height();
			}
			if($('#footer-buttons-fixed').length>0) {
				vh -= $('#footer-buttons-fixed').height();
			}
			$('#main-nav ul.sub-menu').css('max-height', `${vh}px`);
		}

		function align_submenu() {
			let win_width = $(window).width();
			$('#main-nav ul.sub-menu').each(function(index){
				let $sub_menu = $(this),
					$wrap_sub = $sub_menu.parent();
				let delta = $wrap_sub.offset().left + $sub_menu.width() - win_width;
				if( delta>0 ) {
					$sub_menu.css('right', '0');
					$sub_menu.css('left', 'auto');
				} else {
					$sub_menu.css('left', '0');
					$sub_menu.css('right', 'auto');
				}
			});
		}

		$(window).on('resize', function(){
			set_vh_size();
			align_submenu();
		}).resize();
		
		let $actions_fixed = $('.actions-fixed');
		let $actions = $('#product-actions');
		
		if($actions.length>0 && $actions_fixed.length>0) {
			if ("IntersectionObserver" in window) {
				let actionsObserver = new IntersectionObserver(function(entries, observer) {
					entries.forEach(function(actions) {
						if(actions.isIntersecting===false && actions.boundingClientRect.top<0) {
							$actions_fixed.addClass('visible');
						} else {
							$actions_fixed.removeClass('visible');
						}
						
					});
				}, {rootMargin: "0px",threshold: 0});

				actionsObserver.observe($actions.get(0));

			}
		}
		

		if($('body').hasClass('single')) {

			var sync1 = $(".single-gallery .slider");
			var sync2 = $(".single-gallery .navigation-thumbs");

			var thumbnailItemClass = '.owl-item';
			var args = {
				// video:false,
				items:1,
				lazyLoad:true,
				loop:false,
				autoplay:true,
				autoHeight:true,
				autoplayTimeout:3000,
				autoplayHoverPause:true,
				nav: true,
				dots: false
			};
			if(sync1.hasClass('has-video')) {
				args.autoplay = false;
			}
			var slides = sync1.owlCarousel(args).on('changed.owl.carousel', syncPosition);

			function syncPosition(el) {
				$owl_slider = $(this).data('owl.carousel');
				var loop = $owl_slider.options.loop;

				if(loop){
					var count = el.item.count-1;
					var current = Math.round(el.item.index - (el.item.count/2) - .5);
					if(current < 0) {
						current = count;
					}
					if(current > count) {
						current = 0;
					}
				}else{
					var current = el.item.index;
				}

				var owl_thumbnail = sync2.data('owl.carousel');
				var itemClass = "." + owl_thumbnail.options.itemClass;


				var thumbnailCurrentItem = sync2
				.find(itemClass)
				.removeClass("synced")
				.eq(current);

				thumbnailCurrentItem.addClass('synced');

				if (!thumbnailCurrentItem.hasClass('active')) {
					var duration = 300;
					sync2.trigger('to.owl.carousel',[current, duration, true]);
				}   
			}

			var thumbs = sync2.owlCarousel({
				items:4,
				lazyLoad:true,
				loop:false,
				margin:10,
				autoplay:false,
				nav: true,
				dots: false,
				// responsive : {
				// 	0 : {
				// 		items: 2
				// 	},
				// 	768 : {
				// 		items: 4
				// 	}
				// },
				onInitialized: function (e) {
					var thumbnailCurrentItem =  $(e.target).find(thumbnailItemClass).eq(this._current);
					thumbnailCurrentItem.addClass('synced');
				}
			})
			.on('click', thumbnailItemClass, function(e) {
				e.preventDefault();
				var duration = 300;
				var itemIndex =  $(e.target).parents(thumbnailItemClass).index();
				sync1.trigger('to.owl.carousel',[itemIndex, duration, true]);
			}).on("changed.owl.carousel", function (el) {
				var number = el.item.index;
				$owl_slider = sync1.data('owl.carousel');
				$owl_slider.to(number, 100, true);
			});
		}

		
		let popped_popup_content = getCookie('popped_popup_content');
		if($('#modal-popup').length>0 && theme.preview!='1' && !popped_popup_content) {
			
			const popup_content = new bootstrap.Modal('#modal-popup');
			
			setTimeout(function(){
				popup_content.show();
				setCookie('popped_popup_content', 1, 1);
			}, 1000*parseInt(theme.popup_content_timeout));
			
		}
		
		/*
		let popped = getCookie('popped');
		if($('#modal-popup-image').length>0 && theme.preview!='1' && !popped) {
			
			const popup = new bootstrap.Modal('#modal-popup-image');
			
			setTimeout(function(){
				popup.show();
				setCookie('popped', 1, 1);
			}, 1000*parseInt(theme.popup_timeout));
			
		}
		
		// xử lý link nhảy cóc
		function goToAncho(ancho_name) {
			let ancho = $('[name="'+ancho_name+'"]'), header_height = $('#site-header').height();
			if(ancho.length>0) {
				$('html, body').scrollTop((ancho.offset().top-header_height));
				setTimeout(function(){
					$('html, body').scrollTop((ancho.offset().top-header_height));
				}, 1000);
			}
		}

		let hash;
		hash = window.location.hash.replace('#','');
		if(hash!='' && $('[name="'+hash+'"]').length>0) {
			goToAncho(hash);
		}

		var main = $( '#query-monitor-main' );
		var menu_item = $( '#wp-admin-bar-query-monitor' );

		$('a[href^="#"]').on('click', function(e){
			e.preventDefault();
			hash = $(this).attr('href').replace('#','');
			if ( menu_item && main && hash.match(/^qm-/) ) {
				main.toggleClass('qm-show');
			} else {
				goToAncho(hash);
			}
			return false;
			
		});
		*/
		
		$('a[href$="#"]').on('click', function(e){
			e.preventDefault();
			return false;
		});
		
	
		// xử lý sub menu
		$('#main-nav a.toggle-sub-menu').on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			let $this = $(this);
			
			$this.parent('li').siblings().find('ul.sub-menu').removeClass('open');

			let sub = $this.next('ul.sub-menu');

			sub.toggleClass('open');

		});

		$('body').on('click', function(e){
			$('#main-nav ul.sub-menu').removeClass('open');
		});

		let pmsr = $('.posts-masonry,.list-media');
		pmsr.imagesLoaded(function(){
			//setTimeout(function(){
				pmsr.isotope();
				pmsr.isotope('layout');
			//}, 1000);
		});
		
		$('.posts-masonry-loadmore-button').on('click', function(e){
			let $this = $(this),
					$container = $this.closest('.posts-masonry-section'),
					$msr = $container.find('.posts-masonry'),
					cat = parseInt($this.data('cat')),
					location = parseInt($this.data('location')),
					catexc = $this.data('catexc'),
					pages = parseInt($this.data('pages')),
					page = parseInt($this.data('page'))+1,
					per = parseInt($this.data('per')),
					exclude = parseInt($this.data('exclude')),
					btn_text = $this.text();
				//console.log(catexc);
			$.ajax({
				url:theme.ajax_url+'?action=posts_masonry_loadmore',
				method:'GET',
				data:{cat:cat, local:location, catexc:catexc, page:page, per:per, ex:exclude},
				beforeSend:function(){
					$this.text('Đang tải...');
					if(page>=pages) {
						$this.prop('disabled', true);
					}
				},
				success:function(response){
					let $item = $.parseHTML(response);
					$msr.append($item).isotope('appended', $item);
					$msr.imagesLoaded(function(){
						$msr.isotope();
						$msr.isotope('layout');
					});
					
					$container.find('.loaded-page').text(page);
					$container.find('.loaded-bar').css('width',(100*page/pages)+'%');
					$this.data('page',page);
					$this.text(btn_text);
					if(page>=pages) {
						$this.remove();
					}
				}
			});

		});

		// chọn mẫu modal
		$('#order-product').on('show.bs.modal', function (event) {
			let modal = $(this),
				button = $(event.relatedTarget),
				$modal_label = $('#order-product-label'),
				attachment = button.data('attachment'),
				code = button.data('code'),
				ctype = button.data('type'),
				id = button.data('id'),
				msrc = button.data('src-medium');

			switch(ctype) {
				case 'premium': 
					$modal_label.find('.title-normal').addClass('hide');
					$modal_label.find('.title-premium').removeClass('hide');
					break;
				default:
					$modal_label.find('.title-normal').removeClass('hide');
					$modal_label.find('.title-premium').addClass('hide');
					break;
			}
			
			$('#product_attachment').val(attachment);
			$('#product_code').val(code);
			$('#product_id').val(id);
			$('#ctype').val(ctype);
			
			$('#order-product-preview').html('<img src="'+msrc+'">');
			
		}).on('hidden.bs.modal', function (e) {
			
			$('#product_customer_name').removeClass('is-invalid');
			$('#product_customer_name').next('.invalid-feedback').html('');
			$('#product_customer_phone').removeClass('is-invalid');
			$('#product_customer_phone').next('.invalid-feedback').html('');

			$('#product_attachment').val(0);
			$('#product_code').val('');
			$('#ctype').val('');

			$('#order-product-message').html('');
			$('#order-product-preview').html('');
			$('#submit-order').text('Đồng ý');
			$('#submit-order').prop('disabled',false);
		});

		// chọn mẫu submit
		let ajax_order = null;
		function submit_order_product(event) {
			
			let submit_button = $('#order-product-submit'),
				attachment = parseInt($('#product_attachment').val()),
				code = $('#product_code').val(),
				id = parseInt($('#product_id').val()),
				ctype = $('#ctype').val(),
				phone = $('#product_customer_phone').val(),
				phone_number = '',
				name = $('#product_customer_name').val(),
				token = $('[name="cf-turnstile-response"]').val(),

				feedback_name = $('#product_customer_name').next('.invalid-feedback'),
				feedback_phone = $('#product_customer_phone').closest('.input-group').find('.invalid-feedback'),
				validate_name = validate_phone = false, custom_value = '';

			$('#order-product-message').html('');

			if(attachment<=0) {
				$('#product_customer_phone').addClass('is-invalid');
				feedback_phone.html('Lựa chọn không xác định!');
			} else {
				if(check_input_phone_number(phone)) {
					validate_phone = true
					$('#product_customer_phone').removeClass('is-invalid');
					feedback_phone.html('');
				} else {
					$('#product_customer_phone').addClass('is-invalid');
					feedback_phone.html('Số điện thoại không đúng!');
				}

				if(name!='') {
					validate_name = true
					$('#product_customer_name').removeClass('is-invalid');
					feedback_name.html('');
				} else {
					$('#product_customer_name').addClass('is-invalid');
					feedback_name.html('Tên không được trống');
				}

				if(validate_name && validate_phone){

					if(phone.startsWith('0')) {
						phone_number = "+84" + phone.slice(1, phone.length);
					} else if(phone.startsWith('+84')) {
						phone_number = phone;
					} else {
						phone_number = "+84" + phone;  
					}

					let event_name = '';
					switch(ctype) {
						case 'premium': 
							event_name = 'orderPremiumProduct';
							break;
						default:
							event_name = 'orderProduct';
							break;
					}

					if(ajax_order!=null) ajax_order.abort();

					ajax_order = $.ajax({
						url: theme.ajax_url,
						type: 'post',
						data: {
							action: 'order_product',
							attachment:attachment,
							code:code,
							id:id,
							name:name,
							phone:phone_number,
							ctype:ctype,
							url: window.btoa(window.location.href),
							token:token
						},
						dataType: 'json',
						beforeSend: function(xhr) {
							submit_button.text('Đang gửi..');
							//submit_button.prop('disabled',true);
						},
						success: function(response) {
							//console.log(response);
							
							const eventOrder = new CustomEvent(event_name, {
								bubbles: true,
								detail: { attachment: attachment, code: response.data.code, id: response.data.id, name:response.data.name, phone:response.data.phone, fb_pxl_code:response.fb_pxl_code }
							});

							if(response.code==1) {
								//console.log(eventOrder);
								event.target.dispatchEvent(eventOrder);
							
								$('#order-product-message').html('<div class="py-5 px-3 text-center text-success">'+response.msg+'</div>');
								//$('#order-product-preview').html('<div class="py-5 px-3 text-center text-success">'+response.msg+'</div>');
								submit_button.text('Đã gửi');
								submit_button.closest('form').trigger('reset');

							} else {
								submit_button.text('Đồng ý');
								submit_button.prop('disabled', false);
								$('#order-product-message').html('<p class="text-danger">'+response.msg+'</p>');
							}
							
						},
						error: function() {
							submit_button.text('Đồng ý');
							submit_button.prop('disabled', false);
							$('#order-product-message').html('<p class="text-danger">Có lỗi khi gửi! Vui lòng tải lại trang rồi thử lại. Hoặc liên hệ với ban quản trị về sự cố này.</p>');
						},
						complete: function() {
							
						}
					});

				}
			}
		}

		$('#order-product-submit').on('click', function(e){
			e.preventDefault();
			$(this).prop('disabled', true);
			$('#frm-order-product').submit();
		});

		$('#frm-order-product').on('submit', function(e){
			e.preventDefault();

			submit_order_product(e);

			return false;

		}); // submit order

		$('#frm-order-product').find('input.form-control').on('keyup', function(e){
			let valid = true;
			$('#frm-order-product').find('input.form-control').each(function(index, el) {
				switch(el.type) {
					case 'text':
						if(el.validity.valueMissing || el.validity.tooLong) {
							valid = false;
						}
						break;
					case 'tel':
						if(!check_input_phone_number(el.value)) {
							valid = false;
						}
						break;
				}
			});
			
			if(valid) {
				$('#order-product-submit').prop('disabled', false);
			} else {
				$('#order-product-submit').prop('disabled', true);
			}
		});

		$('#modal-video-player').on('show.bs.modal', function (event) {
			let $modal = $(this),
				$button = $(event.relatedTarget),
				video = $button.data('video'),
				url = $button.data('url');
			$('#video-player').html(video.content);
			if(url!='') {
				$('#video-link').html('<a href="'+url+'" class="btn btn-sm btn-danger">Xem chi tiết</a>');
			}
			if(video.type=='youtube') {
				let h,w;
				if($(window).width()>768) {
					h = $(window).innerHeight()-100;
					w = 16*h/9;
				} else {
					w = $(window).width() - 24;
					h = 9*w/16;
				}
				$('#video-player').addClass('ratio ratio-16x9').height(h).width(w);
			} else {
				$('#video-player').removeClass('ratio ratio-16x9').removeAttr('style');
			}
		}).on('shown.bs.modal', function (event) {
			$(this).css('display', 'flex');
			if($(this).find('video').length>0) {
				$(this).find('video').get(0).play();
			}
		}).on('hidden.bs.modal', function (e) {
			$('#video-player').html('<div class="ratio ratio-16x9"></div>');
		});

		function matchKWS(params, data) {
			// If there are no search terms, return all of the data
			if ($.trim(params.term) === '') {
				return null;
			}

			// Do not display the item if there is no 'text' property
			if (typeof data.text === 'undefined') {
				return null;
			}

			// `params.term` should be the term that is used for searching
			// `data.text` is the text that is displayed for the data object
			if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
				var modifiedData = $.extend({}, data, true);

				// You can return modified objects from here
				// This includes matching the `children` how you want in nested data sets
				return modifiedData;
			}

			// Return `null` if the term should not be displayed
			return null;
		}
		
		$('#keyword-search').select2({
			// matcher: matchKWS,
			// data: theme.kws,
			ajax: {
				url: theme.ajax_url,
				dataType: 'json',
				data: function (params) {
					var query = {
						search: params.term,
						action: 'get_seo_post'
					}
					return query;
				},
				delay: 500,
				cache: true
			},
			dropdownParent: $('#modal-keyword-search .modal-body'),
			allowClear: false,
			placeholder: 'Tìm công trình theo tỉnh',
			dropdownCssClass: 'kws-dropdown',
			language: {
				inputTooLong: function(n) {
					return "Vui lòng xóa bớt ký tự";
				},
				inputTooShort: function(n) {
					return "Vui lòng nhập thêm ký tự";
				},
				loadingMore: function() {
					return "Đang lấy thêm kết quả…";
				},
				maximumSelected: function(n) {
					return "Chỉ có thể chọn giới hạn lựa chọn";
				},
				noResults: function() {
					return "Không tìm thấy kết quả";
				},
				searching: function() {
					return "Đang tìm…";
				},
				removeAllItems: function() {
					return "Xóa tất cả các mục";
				}
			}
		});
		
		$('#keyword-search').on('change', function(e) {
			location.href = $(this).val();
		});


	});// jQuery
	

}); // DOMContentLoaded