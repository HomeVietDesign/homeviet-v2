<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @var array $atts
 */

$category_id = 0;
$location_id = 0;

if ( ! empty( $atts['category'] ) ) {
	$category_id = $atts['category'][0];
}

$category = get_category($category_id);

if ( ! empty( $atts['location'] ) ) {
	$location_id = $atts['location'][0];
}

$location = get_term($location_id, 'location');

$exclude = ( ! empty( $atts['category_exclude'] ) )?array_map('absint', $atts['category_exclude']):[];

$html_id = '';
if ( ! empty( $atts['html_id'] ) ) {
	$html_id = ' id="' . sanitize_html_class($atts['html_id']) . '"';
}

$number = !empty($atts['number']) ? intval($atts['number']) : 8;
$title = !empty($atts['title']) ? $atts['title'] : '';

$current_id = (is_page()||is_single())?get_the_ID():0;
?>
<div<?=$html_id?> class="fw-category-posts">
<?php
	if(!empty($title)) {
		?>
		<div class="heading"><?php echo do_shortcode( wp_kses_post($title) ); ?></div>
		<?php
	}
	
	$args = [
		'post_type' => 'post',
		'posts_per_page' => $number,
		//'paged' => 1,
		'post_status' => 'publish'
	];

	if(!is_wp_error($category) && $category) {
		$args['cat'] = $category->term_id;
	}

	if(!is_wp_error($location) && $location) {
		$args['tax_query'] = [
			'location' => [
				'taxonomy' => 'location',
				'field' => 'term_id',
				'terms' => $location->term_id
			]
		];
	}

	if(!empty($exclude)) {
		$args['category__not_in'] = $exclude;
	}

	if($current_id) {
		$args['post__not_in'] = [$current_id];
	}

	$query = new \WP_Query($args);

	//debug_log($query);

	\HomeViet\Template_Tags::category_posts($query);

	?>
</div>
