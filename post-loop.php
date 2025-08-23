<?php
global $post;

$src = get_the_post_thumbnail_url( $post, 'full' );

$video_local = fw_get_db_post_option($post->ID, 'video');
$video_url = fw_get_db_post_option($post->ID, 'video_url');
$video_youtube = fw_get_db_post_option($post->ID, 'video_youtube');

$data_video = ['type' => '', 'content' => ''];

// if($video_youtube!='') {
// 	$data_video['type'] = 'youtube';
// 	$data_video['content'] = '<iframe type="text/html" width="1280" height="720"
// data-src="https://www.youtube.com/embed/'.get_youtube_id($video_youtube).'?autoplay=0&controls=1&fs=0&loop=1&playsinline=1&mute=1&modestbranding=1"
// frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope"></iframe>';

// } else 

if($video_url!='') {
	$data_video['type'] = 'url';

	$data_video['content'] = '<video playsinline disablePictureInPicture controlsList="nodownload" data-src="'.esc_url($video_url).'" type="video/mp4" poster="'.esc_url($src).'" controls loop muted></video>';

} else if(!empty($video_local)) {
	$data_video['type'] = 'local';
	$video_metadata = wp_get_attachment_metadata( $video_local['attachment_id'] );
	$data_video['content'] = '<video width="'.absint($video_metadata['width']).'" height="'.absint($video_metadata['height']).'" playsinline disablePictureInPicture controlsList="nodownload" data-src="'.esc_url(wp_get_attachment_url($video_local['attachment_id'])).'" type="video/mp4" poster="'.esc_url($src).'" controls loop muted></video>';

}

$attachment = 0;

if(has_post_thumbnail($post)) {
	$attachment = get_post_thumbnail_id($post);
}

$_featured = get_post_meta($post->ID, '_featured', true);

$allow_order = get_post_meta($post->ID, '_allow_order', true);

$design_price = '';
$prices = get_the_terms( $post, 'price' );
if($prices) $design_price = $prices[0]->description;

$display_price = fw_get_db_settings_option('display_price', 'yes');
$display_location = fw_get_db_settings_option('display_location', 'yes');

$_area_1 = floatval(get_post_meta($post->ID, '_area_1', true));
$_floors = floatval(get_post_meta($post->ID, '_floors', true));
$area = $_area_1*$_floors;

$_breadth = get_post_meta($post->ID, '_breadth', true);
$_length = get_post_meta($post->ID, '_length', true);


$location = get_the_terms( $post, 'location' );
if($location) $location = array_reverse($location);

$_images = get_post_meta($post->ID, '_images', true);

?>
<div <?php post_class('post-masonry col-md-6'); ?>>
	<div class="inner <?php echo ($_featured=='yes')? 'featured':''; ?>">
		<div class="post-thumbnail">
			<div class="top-left-wrap position-absolute p-2">
				
			</div>
			<div class="entry-thumbnail">
				<a class="popup" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'full' ); ?></a>
			</div>
	
			<?php \HomeViet\Template_Tags::product_cost(); ?>
			
			<?php
			if(has_role('administrator')) {
				?>
				<div class="position-absolute start-0 bottom-0 p-1 z-3"><?=esc_html($post->ID)?></div>
				<?php
			}

			?>
			<!-- <div class="position-absolute top-0 end-0 p-2 z-3">
				<?php
				if(!empty($area)) {
				?>
				<div class="text-yellow total-area mb-2">
					<span>DT: </span><span class="fw-bold"><?php echo number_format($area, 0, '.',','); ?></span><span>m<sup>2</sup></span>
				</div>
				<?php
				}
				?>
				
			</div> -->

		</div>
		<div class="post-summary position-relative">
			<div class="px-2 d-flex">
				<?php
				if($allow_order=='yes') {
					echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger btn-sm order-product fw-bold text-uppercase text-yellow'], esc_html(fw_get_db_settings_option('product_loop_order_button_text')));	
				}
				?>
				<?php if($data_video['type']!='') { ?>
				<a class="open-modal-player text-uppercase btn btn-sm btn-danger text-yellow fw-bold ms-1" href="#modal-video-player" data-bs-toggle="modal" data-video="<?=esc_attr(json_encode($data_video))?>" data-url="<?php the_permalink(); ?>" title="Xem video">Video</a>
				<?php } ?>
			</div>
			<?php if($design_price!='' && $display_price=='yes') { ?>
			<!-- <div class="design-price position-absolute top-0 end-0 d-flex p-2 text-yellow align-items-end"> -->
			<div class="design-price px-2 mt-3 text-center">
				<span><b><?php echo esc_html($design_price); ?></b></span>
			</div>
			<?php } ?>
			
			<h3 class="entry-title fw-bold text-center<?php echo ($design_price!=''||$allow_order=='yes')?' mt':''; ?>">
				<?php the_title(); ?>
			</h3>
			<?php if(($_breadth!='' && $_length!='')||!empty($area)) { ?>
			<div class="d-flex justify-content-center align-items-center mt-2 flex-wrap">
				<?php
				if($_breadth!='' && $_length!='') {
				?>
				<div class="dimension">
					<span>KT: </span><span class="fw-bold"><?php echo number_format($_breadth, 0, '.',','); ?></span>x<span class="fw-bold"><?php echo number_format($_length, 0, '.',','); ?></span><span>m</span>
				</div>
				
				<?php
				}
			
				if(!empty($area)) {
				?>
				<div class="mx-3 bg-white" style="width: 2px; height: 16px;"></div>
				<div class="total-area">
					<span>DT: </span><span class="fw-bold"><?php echo number_format($area, 0, '.',','); ?></span><span>m<sup>2</sup></span>
				</div>
				<?php
				}
				?>
			</div>
			<?php } ?>
			<div class="d-flex justify-content-center align-items-center mt-2 flex-wrap">
				<?php if($location && $display_location=='yes') { ?>
				<div class="location w-100 text-center mb-2">
					<span>Địa điểm: </span>
					<span class="fw-bold"><?php
					foreach ($location as $key => $loca) {
						if($key==0) {
							echo esc_html($loca->name);
						} else {
							echo ", ".esc_html($loca->name);
						}
					}
					?>
					</span>
				</div>
				<?php } ?>
				<a href="<?php the_permalink(); ?>" class="view-detail btn btn-sm btn-primary d-block mt-1 popup">Xem chi tiết</a>
			</div>

			<?php edit_post_link( '<span class="dashicons dashicons-edit"></span>' ); ?>
			<?php
			if(''!=$post->post_excerpt) {
				//debug($post);
			?>
			<div class="entry-excerpt mt-2">
				<?php echo wp_format_content($post->post_excerpt); ?>
			</div>
			<?php
			} ?>

		</div>
	</div>
</div>

