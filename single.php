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
		//$_area = get_post_meta($post->ID, '_area', true);
		$_functions = get_post_meta($post->ID, '_functions', true);

		$design_price = '';
		$prices = get_the_terms( $post, 'price' );
		if($prices) $design_price = $prices[0]->description;

		$_area_1 = floatval(get_post_meta($post->ID, '_area_1', true));
		$_floors = floatval(get_post_meta($post->ID, '_floors', true));
		$area = $_area_1*$_floors;
		
		$_images = get_post_meta($post->ID, '_images', true);

		$attachment = 0;
		if(has_post_thumbnail($post)) {
			$attachment = get_post_thumbnail_id($post);
		}

		if($_breadth!='' || $_length!='' || $area!='' || $location!='') {
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

			ob_start();
			?>
			<div class="ratio ratio-16x9">
				<div id="single-product-video-wrap">
					<iframe id="single-product-video" data-no-lazy="1" type="text/html" width="1280" height="720" src="https://www.youtube.com/embed/<?=get_youtube_id($video_youtube)?>?autoplay=1&controls=0&enablejsapi=1&fs=0&loop=1&playsinline=1&mute=1&modestbranding=1" frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope"></iframe>
					<div class="play">
						<svg height="48" version="1.1" viewBox="0 0 68 48" width="68"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f03"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg>
					</div>
					<div class="pause"></div>
				</div>
			</div>
			<?php
			$data_video['content'] = ob_get_clean();

		} else if($video_url!='') {
			$data_video['type'] = 'url';

			$data_video['content'] = '<video preload="none" playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url($video_url).'" type="video/mp4" poster="'.esc_url($src).'" autoplay muted loop></video>';

		} else if(!empty($video_local)) {
			$data_video['type'] = 'local';

			$data_video['content'] = '<video preload="none" playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url(wp_get_attachment_url($video_local['attachment_id'])).'" type="video/mp4" poster="'.esc_url($src).'" autoplay muted loop></video>';

		}

		$allow_order = get_post_meta($post->ID, '_allow_order', true);

		$product_order_button_text = get_option('product_order_button_text', '');
		//$product_order_premium_button_text = get_option('product_order_premium_button_text', '');

		$display_price = fw_get_db_settings_option('display_price', 'yes');
		$display_location = fw_get_db_settings_option('display_location', 'yes');
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
										<?php if($design_price!='' && $display_price=='yes'): ?>
										<div class="design-price hidden d-flex text-yellow align-items-end position-absolute top-0 end-0 z-3 py-1 px-2">
											<!-- <span class="d-block me-1">Phong cách:</span> -->
											<span class="d-block fs-5 fw-bold lh-sm"><?=$design_price?></span>
										</div>
										<?php endif; ?>
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
										<?php \HomeViet\Template_Tags::product_cost('single'); ?>
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
									if($design_price!='' && $display_price=='yes'): ?>
										<div class="d-flex text-yellow align-items-end position-absolute top-0 end-0 z-3">
											<!-- <span class="d-block me-1">Phong cách:</span> -->
											<span class="d-block fs-5 fw-bold lh-sm"><?=$design_price?></span>
										</div>
										<?php endif;
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
								<?php if($_area_1) { ?>
								<div class="mb-2 d-flex justify-content-between"><span>Diện tích sàn tầng 1:</span><span class="flex-grow-1 border-bottom border-dark">&nbsp;</span><span><?=esc_html($_area_1)?>/<?=esc_html(number_format($area, 0, '.',','))?>m<sup>2</sup></span></div>
								<?php } ?>
								<?php if($location && $display_location=='yes') { ?>
								<div class="mb-2 d-flex justify-content-between"><span>Địa điểm:</span><span class="flex-grow-1 border-bottom border-dark">&nbsp;</span><span class="fw-bold"><?php
								foreach ($location as $key => $loca) {
									if($key==0) {
										echo esc_html($loca->name);
									} else {
										echo ", ".esc_html($loca->name);
									}
								}
								?></span></div>
								<?php } ?>
							</div>
							<?php } ?>
					
							<div id="product-actions" class="mb-3">
								<?php
								if($attachment) {
									if($allow_order=='yes') {
										if($product_order_button_text) {
											echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger order-product d-block my-3 fw-bold'], esc_html($product_order_button_text));	
										}

										// if($product_order_premium_button_text) {
										// 	echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'premium', 'class'=>'btn btn-danger order-product order-product-premium d-block my-3 fw-bold'], esc_html($product_order_premium_button_text));	
										// }
									}
					
								}

								$product_links = fw_get_db_settings_option('product_links', []);
								if(!empty($product_links)) {
									foreach ($product_links as $key => $value) {
										?>
										<a href="<?php echo esc_url($value['url']); ?>" class="btn btn-danger fw-bold text-yellow me-1 popup d-block product-link"><?=esc_html($value['name'])?></a>
										<?php
									}
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
		the_content();
		
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