<?php
global $post;

$src = get_the_post_thumbnail_url( $post, 'full' );

$video_local = fw_get_db_post_option($post->ID, 'video');
$video_url = fw_get_db_post_option($post->ID, 'video_url');
$video_youtube = fw_get_db_post_option($post->ID, 'video_youtube');

$data_video = ['type' => '', 'content' => ''];

if($video_youtube!='') {
	$data_video['type'] = 'youtube';
	$data_video['content'] = '<iframe id="ytplayer" type="text/html" width="1280" height="720"
src="https://www.youtube.com/embed/'.get_youtube_id($video_youtube).'?autoplay=1&controls=1&fs=0&loop=1&playsinline=1&mute=1&modestbranding=1"
frameborder="0" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope"></iframe>';

} else if($video_url!='') {
	$data_video['type'] = 'url';

	$data_video['content'] = '<video playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url($video_url).'" type="video/mp4" poster="'.esc_url($src).'" controls loop></video>';

} else if(!empty($video_local)) {
	$data_video['type'] = 'local';
	$video_metadata = wp_get_attachment_metadata( $video_local['attachment_id'] );
	$data_video['content'] = '<video width="'.absint($video_metadata['width']).'" height="'.absint($video_metadata['height']).'" playsinline disablePictureInPicture controlsList="nodownload" src="'.esc_url(wp_get_attachment_url($video_local['attachment_id'])).'" type="video/mp4" poster="'.esc_url($src).'" controls loop></video>';

}

$attachment = 0;

if(has_post_thumbnail($post)) {
	$attachment = get_post_thumbnail_id($post);
}

$_featured = get_post_meta($post->ID, '_featured', true);

$allow_order = get_post_meta($post->ID, '_allow_order', true);

$_design_price = absint(get_post_meta($post->ID, '_design_price', true));
$general_design_price = absint(get_option('product_design_price'));
$_use_general_design_price = get_post_meta($post->ID, '_use_general_design_price', true);

$design_price = ($_use_general_design_price=='yes') ? $general_design_price : $_design_price;

$_area_1 = floatval(get_post_meta($post->ID, '_area_1', true));
$_floors = floatval(get_post_meta($post->ID, '_floors', true));
$area = $_area_1*$_floors;

$location = get_the_terms( $post, 'location' );
if($location) $location = array_reverse($location);
?>
<div <?php post_class('post-masonry col-md-6'); ?>>
	<div class="inner <?php echo ($_featured=='yes')? 'featured':''; ?>">
		<div class="post-thumbnail">
			<a <?php if($data_video['type']!='') { ?>
			  class="entry-thumbnail open-modal-player" href="#modal-video-player" data-bs-toggle="modal" data-video="<?=esc_attr(json_encode($data_video))?>" data-url="<?php the_permalink(); ?>" title="Xem video"
			<?php } else { ?>
			 class="entry-thumbnail" href="<?php the_permalink(); ?>"
			<?php } ?>
			>
				<span class="d-block"><?php the_post_thumbnail('full', ['alt'=>esc_attr(get_the_title())]); ?></span>
				<?php if($data_video['type']!='') { ?>
				<span class="play-video-button position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center"><span class="d-flex justify-content-center align-items-center play-video-icon"><span class="dashicons dashicons-controls-play"></span></span></span>
				<?php } ?>
			</a>

			<?php \HomeViet\Template_Tags::product_cost(); ?>
			
			<?php
			if(has_role('administrator')) {
				?>
				<div class="position-absolute start-0 bottom-0 p-1"><?=esc_html($post->ID)?></div>
				<?php
			}

			if(!empty($area)) {
			?>
			<div class="position-absolute top-0 end-0 p-2 text-yellow total-area">
				<span>DT: </span><span class="fw-bold"><?php echo number_format($area, 0, '.',','); ?></span><span>m<sup>2</sup></span>
			</div>
			<?php
			}
			?>
		</div>
		<div class="post-summary position-relative">
			<?php if($location) { ?>
			<div class="location position-absolute p-2 d-flex start-0 top-0 z-3"><span><?php
			foreach ($location as $key => $loca) {
				if($key==0) {
					echo esc_html($loca->name);
				} else {
					echo ", ".esc_html($loca->name);
				}
			}
			?></span></div>
			<?php } ?>

			<?php if($design_price>0) { ?>
			<div class="design_price position-absolute top-0 end-0 product-design-fee d-flex p-2 text-yellow align-items-end">
				<span>Phí thiết kế: <b><?php echo esc_html($design_price); ?></b>k/m2</span>
			</div>
			<?php } ?>
			
			<h3 class="entry-title text-center<?php
			echo (($design_price>0 && $_show_general_design_price=='yes')||$location)?' mt-4':'';
			?>">
				<?php
				if($allow_order=='yes') {
					echo '<div class="mb-2">';
					echo wp_do_shortcode('order_product', ['attachment'=>$attachment, 'id'=>$post->ID, 'code'=>wp_basename( wp_get_attachment_url($attachment) ), 'type'=>'normal', 'class'=>'btn btn-danger btn-sm order-product fw-bold text-uppercase text-yellow'], esc_html(fw_get_db_settings_option('product_loop_order_button_text')));	
					echo '</div>';
				}
				?>
				<a href="<?php the_permalink(); ?>" class="title"><?php the_title(); ?></a>
			</h3>
			<?php edit_post_link( '<span class="dashicons dashicons-edit"></span>' ); ?>
			<?php
			if(''!=$post->post_excerpt) {
				//debug($post);
			?>
			<div class="entry-excerpt">
				<?php echo wp_format_content($post->post_excerpt); ?>
			</div>
			<?php
			} ?>

		</div>
	</div>
</div>
<?php