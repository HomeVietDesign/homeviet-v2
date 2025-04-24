<?php
get_header();

global $current_province, $current_password, $current_password_province, $view;

while (have_posts()) {
	the_post();

	$cat = absint(get_post_meta( get_the_ID(), '_cat', true ));

	$default_term_province = (int) get_option( 'default_term_province' );

	if(has_role('administrator') || ($current_password_province && $current_password_province->term_id==$default_term_province)):
		$permalink = get_permalink();

		$provinces = fw_get_db_settings_option('contractor_display_provinces', []);

		//debug($permalink);
		
		?>
		<div class="provinces position-sticky text-center">
			<div class="p-3 d-flex justify-content-center flex-wrap">
				<a class="m-1 btn btn-sm btn-<?php echo empty($current_province)?'danger':'primary'; ?>" href="<?php echo esc_url(remove_query_arg( 'province', $permalink )); ?>"><?=esc_html(get_term_field( 'name', $default_term_province, 'province' ))?></a>
			<?php
			if($provinces) {
				foreach ($provinces as $id) {

					?>
					<a class="m-1 btn btn-sm btn-<?php echo ($current_province && $id==$current_province->term_id)?'danger':'primary'; ?>" href="<?php echo esc_url(add_query_arg('province', $id, $permalink)); ?>"><?=esc_html(get_term_field( 'name', $id, 'province' ))?></a>

					<?php
				}
			}
			?>
			</div>
		</div>
		<?php
		

	endif;

    if(has_role('administrator') || $current_password) {
	?>
	<div class="contractor-search-wrap">
		<div class="d-flex justify-content-center py-3">
			<div class="d-flex align-items-center">
					<div class="btn btn-primary d-none d-lg-block text-nowrap rounded-0">Tra cứu nhà thầu</div>
					<input type="hidden" id="contractor-search-province" value="<?php echo ($current_province)?$current_province->term_id:0; ?>">
				<input type="hidden" id="contractor-search-view" value="<?php echo $view?$view->ID:0; ?>">
				<input type="search" id="contractor-search-input" class="form-control rounded-0" placeholder="Nhập từ khóa tìm kiếm..." title="Nhập số điện thoại, tên, hoặc mô tả dịch vụ.">
			</div>
		</div>
		<div id="contractor-search-result-wrap">
			<div class="contractor-search-result invisible mb-3"></div>
			<div class="loading text-center invisible mb-3"></div>
		</div>
	</div>
	<?php } ?>
	<div class="page-header bg-black py-3 mb-4">
		<div class="container-xxl">
			<h2 class="page-title text-center text-uppercase fw-bold p-0 m-0 d-flex justify-content-center align-items-center">
				<span class="d-inline-block mx-1 text-yellow"><?php if($cat) { ?>Nhà thầu <?php } ?><?php the_title(); ?></span>
			</h2>
		</div>
	</div>
	<?php
	if($cat) {
		echo wp_do_shortcode('ratings_contractors', ['number'=>4, 'contractor_cat'=>[$cat]]);
	}
	the_content();

	wp_do_shortcode('contractors', ['number'=>0, 'contractor_cat'=>[0]]);
}

get_footer();