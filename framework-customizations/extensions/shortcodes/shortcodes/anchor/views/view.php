<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

//$title = !empty($atts['title']) ? sanitize_text_field($atts['title']) : '';
$name = !empty($atts['name']) ? sanitize_key($atts['name']) : '';

if(!empty($name)) {
?>
<div id="<?=esc_attr($name)?>" name="<?=esc_attr($name)?>" class="fw-anchor-point"></div>
<?php } ?>