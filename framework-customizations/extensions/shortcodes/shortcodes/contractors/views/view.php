<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */
global $current_password, $current_password_province, $current_province, $view;

// debug('current_password:');
// debug($current_password);
// debug('current_password_province:');
// debug($current_password_province);
// debug('current_province:');
// debug($current_province);

$contractor_cat_id = 0;
if ( ! empty( $atts['contractor_cat'] ) ) {
	$contractor_cat_id = $atts['contractor_cat'][0];
}
$contractor_cat = get_term_by( 'term_id', $contractor_cat_id, 'contractor_cat' );

$contractor_rating_id = 0;
if ( ! empty( $atts['contractor_rating'] ) ) {
	$contractor_rating_id = $atts['contractor_rating'][0];
}
$contractor_rating = get_term_by( 'term_id', $contractor_rating_id, 'contractor_rating' );

//$is_general = fw_get_db_term_option( $contractor_rating_id, 'contractor_rating', 'is_general', 'no' );

$number = !empty($atts['number']) ? intval($atts['number']) : 8;

$kw = isset($_REQUEST['kw']) ? sanitize_text_field($_REQUEST['kw']) : '';

$not_rating = isset($atts['not_rating'])?$atts['not_rating']:'no';

$is_general = isset($atts['is_general'])?$atts['is_general']:'no';

$allow_query = false;
$tax_query = [];
$meta_query = [];
$orderby = [];

$provinces = [];

// mặc định nhìn thấy nhà thầu toàn quốc
$default_province = (int) get_option( 'default_term_province', 0 );
if($default_province) {
	$provinces[] = $default_province;
}

if( !is_user_logged_in() ) {
	if($current_password) {
		$allow_query = true;

		$orderby = [
			'date' => 'DESC',
			'ID' => 'DESC',
		];

		if($current_password_province) {

			if($default_province==$current_password_province->term_id && !$current_province) { // chủ đầu tư thuộc toàn quốc
				$provinces=[]; // loại bỏ lọc theo tỉnh thành để hiển thị tất cả nhà thầu
			} else {
				// lọc theo tỉnh của chủ đầu tư
				$provinces[] = $current_province->term_id;
			}
		} else { // chủ đầu tư không thuộc tỉnh nào
			$allow_query = false; // không truy vấn dữ liệu
			//$provinces=[0];
		}
		
	}
} elseif(has_role('administrator')) {
	$allow_query = true;
	
	$orderby = [
		//'term_orderby_1' => 'DESC',
		'date' => 'DESC',
		'ID' => 'DESC',
	];

	// hiển thị nhà thầu theo tỉnh
	if($current_province) {
		$provinces[] = $current_province->term_id;
	} else {
		$provinces=[]; // loại bỏ lọc theo tỉnh thành để hiển thị tất cả nhà thầu
	}
}

if(!empty($provinces)) {
	$tax_query['province'] = [
		'taxonomy' => 'province',
		'field' => 'term_id',
		'terms' => $provinces,
	];
}

if($allow_query) {

	$args = [
		'post_type' => 'contractor',
		'posts_per_page' => $number,
		'paged' => 1,
		'post_status' => 'publish',
		'meta_query' => $meta_query,
		'orderby' => $orderby
	];

	if($kw!='') {
		$args['s'] = $kw;
	}

	if(!is_wp_error($contractor_cat) && $contractor_cat) {
		$tax_query['contractor_cat'] = [
			'taxonomy' => 'contractor_cat',
			'field' => 'term_id',
			'terms' => $contractor_cat->term_id,
		];
	}

	if(!is_wp_error($contractor_rating) && $contractor_rating) {
		$tax_query['contractor_rating'] = [
			'taxonomy' => 'contractor_rating',
			'field' => 'term_id',
			'terms' => $contractor_rating->term_id,
		];
	}

	if($not_rating=='yes') {
		$tax_query['contractor_rating'] = [
			'taxonomy' => 'contractor_rating',
			'field' => 'term_id',
			'terms' => [],
			'operator' => 'NOT EXISTS',
		];
	}

	if($tax_query) {
		$args['tax_query'] = $tax_query;
	}

	$query = new \WP_Query($args);
	//debug($args);
	//debug($query->request);
	$shortcode_html_id = uniqid('fwsc-');
	if($query->have_posts()) {
	?>
	<div id="<?=$shortcode_html_id?>" class="fw-shortcode-contractors position-relative is-general-<?php echo esc_html($is_general); ?>">
		<div class="fw-shortcode-contractors-inner">
			<input type="hidden" name="paged" value="1">
			<input type="hidden" name="view" value="<?=($view?$view->ID:0)?>">
			<input type="hidden" name="uri" value="<?php echo empty($kw)?esc_attr($_SERVER['REQUEST_URI']):''; ?>">
	        <input type="hidden" name="query" value="<?=esc_attr(json_encode($query->query))?>">

	        <div class="contractors-container container-xxl p-0 position-relative">
				<?php \FW_Shortcode_Contractors::contractors($query); ?>
			</div>
			<?php
			if($query->max_num_pages>1) {
			?>
			<div class="paginate-links contractor-paginate-links d-flex justify-content-center align-items-center p-2">
				<?php echo \FW_Shortcode_Contractors::pagination($query, 3, 2); ?>
			</div>
			<?php
			} // if pagination
			?>
		</div>
		<div class="overlay invisible position-absolute w-100 h-100 bg-light opacity-25 start-0 top-0 z-3"></div>
	</div>
	<?php
	}
}
?>
