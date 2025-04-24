<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
$section_extra_classes = '';

$bg_color = '';
if ( ! empty( $atts['background_color'] ) ) {
	$bg_color = 'background-color:' . $atts['background_color'] . ';';
}

$bg_image = '';
if ( ! empty( $atts['background_image'] ) && ! empty( $atts['background_image']['data']['icon'] ) ) {
	$bg_image = 'data-background="' . $atts['background_image']['data']['icon'] . '"';
}

$bg_size = '';
if ( ! empty( $atts['background_size'] ) && ! empty( $atts['background_size'] ) && ( $atts['background_size']!='default' ) ) {
	$bg_size = 'background-size:' . $atts['background_size'] . ';';
}

$bg_position = '';
if ( ! empty( $atts['background_position'] ) && ! empty( $atts['background_position'] ) && ( $atts['background_position']!='default' ) ) {
	$bg_position = 'background-position:' . $atts['background_position'] . ';';
}

$bg_repeat = '';
if ( ! empty( $atts['background_repeat'] ) && ! empty( $atts['background_repeat'] ) && ( $atts['background_repeat']!='default' ) ) {
	$bg_repeat = 'background-repeat:' . $atts['background_repeat'] . ';';
}

$margin_top = !empty($atts['margin_top']) ? 'margin-top:'.$atts['margin_top'].';' : '';
$margin_right = !empty($atts['margin_right']) ? 'margin-right:'.$atts['margin_right'].';' : '';
$margin_bottom = !empty($atts['margin_bottom']) ? 'margin-bottom:'.$atts['margin_bottom'].';' : '';
$margin_left = !empty($atts['margin_left']) ? 'margin-left:'.$atts['margin_left'].';' : '';

$padding_top = !empty($atts['padding_top']) ? 'padding-top:'.$atts['padding_top'].';' : '';
$padding_right = !empty($atts['padding_right']) ? 'padding-right:'.$atts['padding_right'].';' : '';
$padding_bottom = !empty($atts['padding_bottom']) ? 'padding-bottom:'.$atts['padding_bottom'].';' : '';
$padding_left = !empty($atts['padding_left']) ? 'padding-left:'.$atts['padding_left'].';' : '';

$section_style   = ($bg_color || $bg_size || $bg_position || $bg_repeat  || $margin_top  || $margin_right  || $margin_bottom  || $margin_left  || $padding_top  || $padding_right  || $padding_bottom  || $padding_left ) ? 'style="' . esc_attr($bg_color . $bg_size . $bg_position . $bg_repeat . $margin_top . $margin_right . $margin_bottom . $margin_left . $padding_top . $padding_right . $padding_bottom . $padding_left) . '"' : '';

$container_class = ( isset( $atts['is_fullwidth'] ) && $atts['is_fullwidth'] ) ? 'fw-container-fluid' : 'fw-container';

$html_id = isset($atts['html_id']) ? sanitize_html_class( $atts['html_id']) : '';
$section_id = ($html_id!='') ? $html_id : uniqid('section-');

$bg_overlay_color = '';
if ( ! empty( $atts['background_overlay_color'] ) ) {
	$section_extra_classes .= ' background-overlay';
	$bg_overlay_color = 'background-color:' . $atts['background_overlay_color'] . ';';
}

$show_less = isset($atts['show_less'])?$atts['show_less']:'';
$show_row = absint($atts['show_row']);

if('yes'==$show_less) {
	$section_extra_classes .= ' has-show-less';
}
$left_right_padding = isset($atts['left_right_padding'])?$atts['left_right_padding']:'';
if($left_right_padding!=='yes') {
	$section_extra_classes .= ' no-padding';
}
?>
<section id="<?php echo $section_id; ?>" class="fw-main-row<?php echo esc_attr($section_extra_classes) ?>" <?php echo $section_style; ?> <?php echo $bg_image; ?>>
	<?php if($bg_overlay_color){ ?><div class="fw-section-overlay" style="<?=esc_attr($bg_overlay_color)?>"></div><?php } ?>
	<div class="<?php echo esc_attr($container_class); ?>">
		<?php echo do_shortcode( $content ); ?>
	
		<?php if('yes'==$show_less): ?>
		<a class="fw-section-show-more" href="javascript:;">Xem thêm <span class="dashicons dashicons-arrow-down"></span></a>
		<?php endif; ?>
	</div>
</section>
<?php if('yes'==$show_less): ?>
<script type="text/javascript">
	window.addEventListener('DOMContentLoaded', function(){
		jQuery(function($){
			let $section = $('#<?=$section_id?>');
			let $container = $section.find('>div[class*=fw-container]');
			let show_row = parseInt(<?=$show_row?>);
			let $show_rows = $container.find('>.fw-row');
			//console.log($show_rows);
			if($show_rows.length>show_row) {
				let new_height = $($show_rows[show_row]).offset().top-$section.offset().top;
				if($container.height()>new_height) {
					$container.height(new_height);
					$container.find('.fw-section-show-more').addClass('show');
				}
			}
		});
	});
</script>
<?php endif; ?>
