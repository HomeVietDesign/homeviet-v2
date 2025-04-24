<?php
get_header();

while (have_posts()) {
	the_post();
	global $post;

	$has_land_info = false;

	if(!post_password_required( $post )) {

		$location = get_the_terms( $post, 'location' );
		if($location) $location = array_reverse($location);
		$_breadth = get_post_meta($post->ID, '_breadth', true);
		$_length = get_post_meta($post->ID, '_length', true);
		$_area = get_post_meta($post->ID, '_area', true);
		$_functions = get_post_meta($post->ID, '_functions', true);
		
		$_images = get_post_meta($post->ID, '_images', true);

		$attachment = 0;
		if(has_post_thumbnail($post)) {
			$attachment = get_post_thumbnail_id($post);
		}

		if($_breadth!='' || $_length!='' || $_area!='' || $location!='') {
			$has_land_info = true;
		}

		$src = get_the_post_thumbnail_url( $post, 'full' );
		$src_medium = get_the_post_thumbnail_url( $post, 'medium' );

		$video_local = fw_get_db_post_option($post->ID, 'video');
		$video_url = fw_get_db_post_option($post->ID, 'video_url');
		$video_youtube = fw_get_db_post_option($post->ID, 'video_youtube');

		$data_video = ['type' => '', 'content' => ''];

		if($video_youtube!='') {
			$data_video['type'] = 'youtube';
			$data_video['content'] = '<div class="ratio ratio-16x9"><iframe id="ytplayer" class="ytplayer" type="text/html" width="1280" height="720"
		src="https://www.youtube.com/embed/'.get_youtube_id($video_youtube).'?autoplay=1&controls=1&fs=0&loop=1&playsinline=1&mute=1&modestbranding=1"
		frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope"></iframe></div>';

		} else if($video_url!='') {
			$data_video['type'] = 'url';

			$data_video['content'] = '<video preload="none" playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url($video_url).'" type="video/mp4" poster="'.esc_url($src).'" autoplay muted loop></video>';

		} else if(!empty($video_local)) {
			$data_video['type'] = 'local';

			$data_video['content'] = '<video preload="none" playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url(wp_get_attachment_url($video_local['attachment_id'])).'" type="video/mp4" poster="'.esc_url($src).'" autoplay muted loop></video>';

		}

		$get_premium = get_post_meta($post->ID, '_get_premium', true);
		$allow_order = get_post_meta($post->ID, '_allow_order', true);

		$product_order_button_text = get_option('product_order_button_text', '');
		$product_order_premium_button_text = get_option('product_order_premium_button_text', '');

		?>
		<div id="product-top-info" class="container-xl">
			<h1 id="entry-heading" class="text-center h3 py-3 m-0"><?php the_title(); ?></h1>
			<div class="row">
				<div class="entry-image col-lg-8 mb-2">
					<div class="h-100">
						<div class="position-sticky">
							<div class="single-image mb-3 position-relative">
								<?php
								if(!empty($_images)) {
								?>
								<div class="single-gallery">
									<div class="position-relative">
										<div class="slider owl-carousel owl-theme<?php
										if($data_video['type']!='') {
											echo ' has-video';
										}?>">
										<?php
										if($data_video['type']!='') {
											echo $data_video['content'];
										}
										foreach ($_images as $key => $value) {
											$src = wp_get_attachment_image_src( $value['attachment_id'], 'full', false );
											?>
											<img class="owl-lazy" data-src="<?php echo esc_url($src[0]); ?>">
											<?php
										}
										?>
										</div>
										<?php \HomeViet\Template_Tags::product_cost(); ?>
									</div>
									<div class="navigation-thumbs owl-carousel owl-theme">
									<?php
										if($data_video['type']!='') {
											?>
											<div class="position-relative">
												<img class="owl-lazy" data-src="<?=esc_url($src_medium)?>">
												<button type="button" class="btn btn-sm btn-danger position-absolute top-50 start-50 translate-middle">VIDEO</button>
											</div>
											<?php
										}
										foreach ($_images as $key => $value) {

											$img_src = wp_get_attachment_image_src( $value['attachment_id'], 'medium', false );
											?>
											<img class="owl-lazy" data-src="<?=esc_url($img_src[0])?>">
											<?php
										}
									?>
									</div>
								</div>
								<?php
								} else {
									if($data_video['type']!='') {
										echo $data_video['content'];
									} else {
										the_post_thumbnail('full');
									}

									\HomeViet\Template_Tags::product_cost();
								} ?>
			
							</div>
							
						</div>
					</div>
				</div>
				<div class="entry-excerpt col-lg-4 mb-2">
					<div class="p-3 border border-dark h-100">
						<div class="general-info position-sticky">
							<?php if($has_land_info) { ?>
							<div class="land-info mb-3">
								<div class="fw-bold mb-2"><?php echo esc_html(get_option('product_info_heading1', '')); ?></div>

								<?php if($_breadth) { ?>
								<div class="mb-2 d-flex justify-content-between"><span>Mặt tiền:</span><span class="flex-grow-1 border-bottom border-dark">&nbsp;</span><span><?=esc_html($_breadth)?>m</span></div>
								<?php } ?>
								<?php if($_length) { ?>
								<div class="mb-2 d-flex justify-content-between"><span>Chiều sâu:</span><span class="flex-grow-1 border-bottom border-dark">&nbsp;</span><span><?=esc_html($_length)?>m</span></div>
								<?php } ?>
								<?php if($_area) { ?>
								<div class="mb-2 d-flex justify-content-between"><span>Diện tích sàn tầng 1:</span><span class="flex-grow-1 border-bottom border-dark">&nbsp;</span><span><?=esc_html($_area)?>m<sup>2</sup></span></div>
								<?php } ?>
							</div>
							<?php } ?>
					
							<div id="product-actions" class="mb-3">
								<?php
								if($attachment) {
									if($allow_order=='yes' && $product_order_button_text) {
										echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger order-product d-block my-3 fw-bold'], esc_html($product_order_button_text));	
									}
									
									if($get_premium=='yes' && $product_order_premium_button_text!='') {
										echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'premium', 'class'=>'btn btn-danger order-premium-product d-block my-3 fw-bolder'], $product_order_premium_button_text);	
									}
								}
								$popup_content = fw_get_db_settings_option('popup_content', '');
								$popup_content_button_text = fw_get_db_settings_option('popup_content_button_text', '');
								if($popup_content!='' && $popup_content_button_text != '' && false) {
								?>
								<button type="button" class="btn-popup-open btn-popup-content-open btn btn-danger d-block w-100 fw-bold" style="color:#ff0;" data-bs-toggle="modal" data-bs-target="#modal-popup"><?=esc_html($popup_content_button_text)?></button>
								<?php
								}
								?>
							</div>
							
							<?php if($_functions) { ?>
							<div class="extra-info mb-3">
								<div class="fw-bold mb-2"><?php echo esc_html(get_option('product_info_heading2', '')); ?></div>
								<?php echo wp_format_content($_functions); ?>
							</div>
							<?php } ?>
							
							<?php if(is_active_sidebar( 'product_info' )): ?>
							<div class="widgets">
								<?php dynamic_sidebar('product_info'); ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>

		</div>
		<?php
	}
	the_content();

	if(!post_password_required( $post )) {
		$_footer_content = get_post_meta($post->ID, '_footer_content', 'yes');
		//if($has_land_info || $_footer_content=='yes') {
		if($_footer_content=='yes') {
			$single_product_footer = get_option('single_product_footer', '');
			if(!empty($single_product_footer)) {
				$footer_post = get_post($single_product_footer[0]);
				if($footer_post->post_status=='publish') {
					$content = $footer_post->post_content;
					if ( function_exists('fw_ext_page_builder_get_post_content') ) {
						$content = fw_ext_page_builder_get_post_content($footer_post);
					}

					echo '<div id="content-footer">';
					echo wp_get_the_content( $content );
					echo '</div>';

				}
			}
		}
	}
}
get_footer();