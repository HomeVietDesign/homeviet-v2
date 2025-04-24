<?php if (!defined('FW')) die('Forbidden');

$class = fw_ext_builder_get_item_width('page-builder', $atts['width'] . '/frontend_class');

$class .= (isset($atts['html_class'])) ? ' '.sanitize_html_class($atts['html_class']) : '';

$html_id = uniqid('fw-col-');

$bg_image = '';
if (isset($atts['background_image']) && ! empty( $atts['background_image'] ) && ! empty( $atts['background_image']['data']['icon'] ) ) {
	$bg_image = 'data-background="' . $atts['background_image']['data']['icon'] . '"';
}

$inner_bg_image = '';
if (isset($atts['text_color']) && ! empty( $atts['inner_background_image'] ) && ! empty( $atts['inner_background_image']['data']['icon'] ) ) {
	$inner_bg_image = 'data-background="' . $atts['inner_background_image']['data']['icon'] . '"';
}

?>
<div id="<?=esc_attr($html_id)?>" class="<?php echo esc_attr($class); ?>" <?php echo $bg_image; ?>>
	<div class="inner-column" <?php echo $inner_bg_image; ?>>
	<?php echo do_shortcode($content); ?>
	</div>
</div>
<style type="text/css">
	#<?=esc_html($html_id)?> {
		<?php if(isset($atts['background_color']) && $atts['background_color']!='') { ?>
			background-color: <?=esc_attr($atts['background_color'])?>;
		<?php } ?>
		<?php if(isset($atts['background_size']) && $atts['background_size']!='' && $atts['background_size']!='default') { ?>
			background-size: <?=esc_attr($atts['background_size'])?>;
		<?php } ?>
		<?php if(isset($atts['background_position']) && $atts['background_position']!='' && $atts['background_position']!='default') { ?>
			background-position: <?=esc_attr($atts['background_position'])?>;
		<?php } ?>
		<?php if(isset($atts['background_repeat']) && $atts['background_repeat']!='' && $atts['background_repeat']!='default') { ?>
			background-repeat: <?=esc_attr($atts['background_repeat'])?>;
		<?php } ?>

		<?php if(isset($atts['border_width']) && $atts['border_width']!='') { ?>
			border-width: <?=esc_attr($atts['border_width'])?>px;
			<?php if(isset($atts['border_color']) && $atts['border_color']!='') { ?>
				border-color: <?=esc_attr($atts['border_color'])?>;
			<?php } ?>
			<?php if(isset($atts['border_style']) && $atts['border_style']!='' && $atts['border_style']!='default') { ?>
				border-style: <?=esc_attr($atts['border_style'])?>;
			<?php } ?>
		<?php } ?>

		<?php if(isset($atts['margin_top']) && $atts['margin_top']!='') { ?>
			margin-top: <?=esc_attr($atts['margin_top'])?>;
		<?php } ?>
		<?php if(isset($atts['margin_right']) && $atts['margin_right']!='') { ?>
			margin-right: <?=esc_attr($atts['margin_right'])?>;
		<?php } ?>
		<?php if(isset($atts['margin_bottom']) && $atts['margin_bottom']!='') { ?>
			margin-bottom: <?=esc_attr($atts['margin_bottom'])?>;
		<?php } ?>
		<?php if(isset($atts['margin_left']) && $atts['margin_left']!='') { ?>
			margin-left: <?=esc_attr($atts['margin_left'])?>;
		<?php } ?>

		<?php if(isset($atts['padding_top']) && $atts['padding_top']!='') { ?>
			padding-top: <?=esc_attr($atts['padding_top'])?>;
		<?php } ?>
		<?php if(isset($atts['padding_right']) && $atts['padding_right']!='') { ?>
			padding-right: <?=esc_attr($atts['padding_right'])?>;
		<?php } ?>
		<?php if(isset($atts['padding_bottom']) && $atts['padding_bottom']!='') { ?>
			padding-bottom: <?=esc_attr($atts['padding_bottom'])?>;
		<?php } ?>
		<?php if(isset($atts['padding_left']) && $atts['padding_left']!='') { ?>
			padding-left: <?=esc_attr($atts['padding_left'])?>;
		<?php } ?>
	}

	#<?=esc_html($html_id)?> .inner-column {
		<?php if(isset($atts['inner_background_color']) && $atts['inner_background_color']!='') { ?>
			background-color: <?=esc_attr($atts['inner_background_color'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_background_size']) && $atts['inner_background_size']!='' && $atts['inner_background_size']!='default') { ?>
			background-size: <?=esc_attr($atts['inner_background_size'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_background_position']) && $atts['inner_background_position']!='' && $atts['inner_background_position']!='default') { ?>
			background-position: <?=esc_attr($atts['inner_background_position'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_background_repeat']) && $atts['inner_background_repeat']!='' && $atts['inner_background_repeat']!='default') { ?>
			background-repeat: <?=esc_attr($atts['inner_background_repeat'])?>;
		<?php } ?>

		<?php if(isset($atts['inner_border_width']) && $atts['inner_border_width']!='') { ?>
			border-width: <?=esc_attr($atts['inner_border_width'])?>px;
			<?php if(isset($atts['inner_border_color']) && $atts['inner_border_color']!='') { ?>
				border-color: <?=esc_attr($atts['inner_border_color'])?>;
			<?php } ?>
			<?php if(isset($atts['inner_border_style']) && $atts['inner_border_style']!='' && $atts['inner_border_style']!='default') { ?>
				border-style: <?=esc_attr($atts['inner_border_style'])?>;
			<?php } ?>
		<?php } ?>

		<?php if(isset($atts['inner_margin_top']) && $atts['inner_margin_top']!='') { ?>
			margin-top: <?=esc_attr($atts['inner_margin_top'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_margin_right']) && $atts['inner_margin_right']!='') { ?>
			margin-right: <?=esc_attr($atts['inner_margin_right'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_margin_bottom']) && $atts['inner_margin_bottom']!='') { ?>
			margin-bottom: <?=esc_attr($atts['inner_margin_bottom'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_margin_left']) && $atts['inner_margin_left']!='') { ?>
			margin-left: <?=esc_attr($atts['inner_margin_left'])?>;
		<?php } ?>

		<?php if(isset($atts['inner_padding_top']) && $atts['inner_padding_top']!='') { ?>
			padding-top: <?=esc_attr($atts['inner_padding_top'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_padding_right']) && $atts['inner_padding_right']!='') { ?>
			padding-right: <?=esc_attr($atts['inner_padding_right'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_padding_bottom']) && $atts['inner_padding_bottom']!='') { ?>
			padding-bottom: <?=esc_attr($atts['inner_padding_bottom'])?>;
		<?php } ?>
		<?php if(isset($atts['inner_padding_left']) && $atts['inner_padding_left']!='') { ?>
			padding-left: <?=esc_attr($atts['inner_padding_left'])?>;
		<?php } ?>
	}
</style>
