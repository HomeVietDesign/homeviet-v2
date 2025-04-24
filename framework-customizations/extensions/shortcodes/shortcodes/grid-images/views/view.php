<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
if(!empty($atts['images']) && !empty($atts['images'])) {

	$class = ['image'];
	$class[] = 'text-center';
	$class[] = $atts['show_mobile'];
	$class[] = $atts['show_tablet'];
	$class[] = $atts['show_desktop'];
		
	$layout = isset($atts['layout']) ? $atts['layout'] : 'masonry';

	$per = isset($atts['per'])?absint($atts['per']):0;
	if($per==0) $per = -1;
	$pages = 0;
	if($per>0){
		$pages = ceil(count($atts['images'])/$per);
	}
	?>
	<div class="fw-shortcode-grid-images" data-pages="<?=$pages?>">
		<div class="row shortcode-grid-images layout-<?=esc_attr($layout)?>">
		<?php
		$paged = 1;
		$dem = 0;
		foreach ($atts['images'] as $key => $image) {
			$dem++;
			?>
			<div class="<?=esc_attr(implode(' ', $class))?> filter-<?=$paged?>" style=<?php echo ($paged>1)?'display:none;':''; ?>>
				<?php echo wp_get_attachment_image( $image['attachment_id'], $atts['size'], false, ['style'=>'width:100%;'] ); ?>
			</div>
			<?php
			if($dem == $per) {
				$paged++;
				$dem = 0;
			}
		}
		?>
		</div>
		<?php
		if($pages>1) {
			?>
			<div class="grid-images-viewmore-wrap mt-5">
				<div class="load-bar"><div class="loaded-bar" style="width: <?=(100/$pages)?>%;"><div class="loaded-num"><div><span class="loaded-page">1</span>/<?=$pages?></div></div></div></div>
				<button class="grid-images-viewmore-button" type="button" data-page="1" data-pages="<?=$pages?>" data-per="<?=$per?>">Xem thêm</button>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
