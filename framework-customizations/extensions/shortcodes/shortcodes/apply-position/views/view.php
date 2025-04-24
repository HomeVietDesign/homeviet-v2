<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$position = isset($atts['position'])?sanitize_text_field($atts['position']):'';
$submit_text = isset($atts['submit_text'])?sanitize_text_field($atts['submit_text']):'';

/**
 * @var array $atts
 */
if(''!=$position && ''!=$submit_text) {
	$html_id = uniqid('fw-shortcode-apply-position-');
	?>
	<div id="<?=esc_attr($html_id)?>" class="fw-shortcode-apply-position">
		<form class="apply-position-form" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post">
			<input type="hidden" name="position" value="<?=esc_attr($position)?>" required>
			<div class="mb-3 row-control d-flex flex-wrap align-items-center justify-content-between">
				<label for="candidate_name">Họ tên ứng viên:</label>
				<div><input type="text" name="candidate_name" class="form-control form-control-sm rounded-0" required></div>
			</div>
			<div class="mb-3 row-control d-flex flex-wrap align-items-center justify-content-between">
				<label for="candidate_phone">Số điện thoại:</label>
				<div><input type="text" name="candidate_phone" class="form-control form-control-sm rounded-0" required></div>
			</div>
			<div class="apply-position-message py-2 text-center"></div>
			<div class="text-center"><button type="submit" class="apply-position-submit btn btn-danger rounded-0"><?=esc_html($submit_text)?></button></div>
		</form>
	</div>
	<?php
}
