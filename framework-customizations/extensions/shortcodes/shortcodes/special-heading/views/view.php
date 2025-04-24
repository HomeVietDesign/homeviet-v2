<?php if (!defined('FW')) die( 'Forbidden' );
/**
 * @var $atts
 */

$text_color = '';
if ( ! empty( $atts['text_color'] ) ) {
	$text_color = 'color:' . $atts['text_color'] . ';';
}

$text_size = '';
if ( ! empty( $atts['text_size'] ) ) {
	$text_size = 'font-size:' . floatval($atts['text_size']) . 'px;';
}

$text_shadow = (!empty($atts['text_shadow_h']) && !empty($atts['text_shadow_v']) && !empty($atts['text_shadow_br']) && !empty($atts['text_shadow_color'])) ? 'text-shadow:'.esc_attr($atts['text_shadow_h']).'px '.esc_attr($atts['text_shadow_v']).'px '.esc_attr($atts['text_shadow_br']).'px '.esc_attr($atts['text_shadow_color']).';' : '';

$html_id = '';
if ( ! empty( $atts['html_id'] ) ) {
	$html_id = ' id="' . sanitize_html_class($atts['html_id']) . '"';
}

$heading_style = ($text_color || $text_size || $text_shadow) ? 'style="'.esc_attr($text_color.$text_size.$text_shadow).'"' : '';
?>
<<?=$atts['heading']?><?=$html_id?> class="fw-heading fw-heading-<?php echo esc_attr($atts['heading']); ?> <?php echo !empty($atts['centered']) ? 'fw-heading-center' : ''; ?> <?php echo !empty($atts['text_bold']) ? 'fw-heading-bold' : ''; ?>" <?=$heading_style?>>
	<?php echo $atts['title']; ?>
</<?=$atts['heading']?>>