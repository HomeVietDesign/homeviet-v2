<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

$boxies = '';
if ( ! empty( $atts['boxies'] ) ) {
	$boxies = $atts['boxies'];
}

if(!empty($boxies)):

	$box_radius = '';
	if ( ! empty( $atts['box_radius'] ) ) {
		$box_radius = 'border-radius:'.$atts['box_radius'].';';
	}

	$box_width = '';
	if ( ! empty( $atts['box_width'] ) ) {
		$box_width = 'width:'.$atts['box_width'].';';
	}

	$box_height = '';
	if ( ! empty( $atts['box_height'] ) ) {
		$box_height = 'height:'.$atts['box_height'].';';
	}

	$box_shadow = (!empty($atts['box_shadow_h']) && !empty($atts['box_shadow_v']) && !empty($atts['box_shadow_br']) && !empty($atts['box_shadow_color'])) ? 'box-shadow:'.esc_attr($atts['box_shadow_h']).' '.esc_attr($atts['box_shadow_v']).' '.esc_attr($atts['box_shadow_br']).' '.esc_attr($atts['box_shadow_color']).';' : '';

	$bg_color = '';
	if ( ! empty( $atts['background_color'] ) ) {
		$bg_color = 'background-color:' . $atts['background_color'] . ';';
	}

	$bg_image = '';
	if ( ! empty( $atts['background_image'] ) && ! empty( $atts['background_image']['data']['icon'] ) ) {
		$bg_image = 'background-image:url(' . $atts['background_image']['data']['icon'] . ');';
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
	
	$v_align = !empty($atts['v_align']) ? 'vertical-align:'.$atts['v_align'].';' : '';

	$html_id = '';
	if ( ! empty( $atts['html_id'] ) ) {
		$html_id = ' id="' . sanitize_html_class($atts['html_id']) . '"';
	}

	$box_style = ($box_radius || $box_width || $box_height || $box_shadow || $bg_color || $bg_image || $bg_size || $bg_position || $bg_repeat || $margin_top || $margin_right || $margin_bottom || $margin_left || $padding_top || $padding_right || $padding_bottom || $padding_left || $v_align) ? 'style="'.esc_attr($box_radius . $box_width . $box_height . $box_shadow . $bg_color . $bg_image . $bg_size . $bg_position . $bg_repeat . $margin_top . $margin_right . $margin_bottom . $margin_left . $padding_top . $padding_right . $padding_bottom . $padding_left . $v_align).'"' : '';

?>
<div<?=$html_id?> class="fw-text-boxies">
<?php
foreach ($boxies as $box) {
	?>
	<div class="fw-text-box-item <?=esc_attr($box['html_class'])?>" <?=$box_style?> title="<?=esc_attr($box['title'])?>">
		<?php echo wp_format_content($box['text']); ?>
	</div>
	<?php
}
?>
</div>
<?php endif; ?>