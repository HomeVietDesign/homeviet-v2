<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

$text_color = '';
if ( ! empty( $atts['text_color'] ) ) {
	$text_color = 'color:' . $atts['text_color'] . ';';
}

$text_link_color = '';
if ( ! empty( $atts['text_link_color'] ) ) {
	$text_link_color = 'color:' . $atts['text_link_color'] . ';';
}

$text_shadow = (!empty($atts['text_shadow_h']) && !empty($atts['text_shadow_v']) && !empty($atts['text_shadow_br']) && !empty($atts['text_shadow_color'])) ? 'text-shadow:'.esc_attr($atts['text_shadow_h']).' '.esc_attr($atts['text_shadow_v']).' '.esc_attr($atts['text_shadow_br']).' '.esc_attr($atts['text_shadow_color']).';' : '';

$html_id = uniqid('fw-text-');

$text_block_style = ($text_color || $text_shadow) ? 'style="'.esc_attr($text_color.$text_shadow).'"' : '';
?>
<div id="<?=$html_id?>" class="fw-text-block-wrap" <?=$text_block_style?>>
<?php echo wp_format_content( $atts['text'] ); ?>
</div>
<?php if($text_link_color!='') {
?>
<style type="text/css">
	#<?=$html_id?> a {
		<?=$text_link_color?>
	}
</style>
<?php
}