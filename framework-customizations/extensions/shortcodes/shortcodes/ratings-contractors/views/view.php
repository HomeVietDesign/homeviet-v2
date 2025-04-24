<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var array $atts
 */
global $current_password;

$contractor_ratings = get_terms(['taxonomy'=>'contractor_rating', 'hide_empty'=>false]);

$shortcode_html_id = uniqid('fw-shortcode-ratings-contractors-');

// debug($contractor_ratings);

$html_contractors = '';

if(is_array($contractor_ratings) && $contractor_ratings) {
	$args = [
		'number'=>isset($atts['number'])?$atts['number']:12,
		'contractor_cat'=>isset($atts['contractor_cat'])?$atts['contractor_cat']:[],
		'contractor_rating'=>[],
		'is_general'=>'no'
	];
	foreach ($contractor_ratings as $key => $value) {
		$is_general = fw_get_db_term_option( $value->term_id, 'contractor_rating', 'is_general', 'no' );
		
		$args['is_general'] = $is_general;
		$args['contractor_rating'] = [$value->term_id];
		
		$html_contractor = wp_do_shortcode('contractors', $args);
		if($html_contractor) {
			ob_start();
			?>
			<div class="contractor-rating-container py-3">
				<div class="text-center text-uppercase fw-bold fs-2<?php echo ($is_general=='yes')?' text-red':''; ?>"><?php echo esc_html($value->name); ?></div>
				<div class="contractor-rating-list">
					<?php echo $html_contractor; ?>
				</div>
			</div>
			<?php
			$html_contractors .= ob_get_clean();
		}
	}
}

if(has_role('administrator')) {
	$args = [
		'number'=>-1,
		'contractor_cat'=>isset($atts['contractor_cat'])?$atts['contractor_cat']:[],
		'contractor_rating'=>[],
		'not_rating'=>'yes',
		'is_general'=>'no',
	];

	$html_contractors_not_rating = wp_do_shortcode('contractors', $args);
	if($html_contractors_not_rating) {
		ob_start();
		?>
		<div class="contractor-rating-container py-3">
			<div class="text-center text-uppercase fw-bold fs-2 text-warning">Chưa phân nhóm</div>
			<div class="contractor-rating-list">
				<?php echo $html_contractors_not_rating; ?>
			</div>
		</div>
		<?php
		$html_contractors .= ob_get_clean();
	}
}

if($html_contractors) {
?>
<div id="<?=$shortcode_html_id?>" class="fw-shortcode-ratings-contractors">
	<?php echo $html_contractors; ?>
</div>
<?php
}