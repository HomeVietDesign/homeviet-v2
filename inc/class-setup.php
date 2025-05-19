<?php
namespace HomeViet;

class Setup {

	private static $instance = null;

	private function __construct() {
		add_action( 'after_setup_theme', [$this, 'after_setup_theme'] );
		add_filter( 'use_widgets_block_editor', '__return_false' );
		add_filter( 'use_block_editor_for_post_type', '__return_false', 10 );

		add_filter( 'image_size_names_choose', [$this, 'image_sizes_choose'] );

		add_filter( 'edit_post_link', [$this, 'edit_post_link_target'] );

		add_filter( 'posts_search', [$this, 'seo_post_search_by_title'], 10, 2 );

		add_filter( 'mime_types', [$this, 'fix_rar_mime_type'] );

		if(has_role('administrator')) {
			//add_action( 'template_redirect', [$this, 'redirect_first_province'], 0 );
		}
		
		if(!is_admin()) {
			//add_filter( 'wp_headers', [$this, 'wp_headers'], 10, 2 );
		}
	}

	public function wp_headers( $headers, $wp ) {
		$headers['x-powered-by'] = 'X88';

		return $headers;
	}

	public function fix_rar_mime_type($mime_types) {
		if(isset($mime_types['rar'])) {
			//$mime_types['rar'] = 'application/x-rar-compressed';
			$mime_types['rar'] = 'application/x-rar';
		}

		return $mime_types;
	}

	public function seo_post_search_by_title($search, $wp_query) {
	
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['s'] ) && isset( $wp_query->query_vars['custom_search'] ) ) {
			global $wpdb;

			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';

			$search = array();

			foreach ( ( array ) $q['s'] as $term )
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

			if ( ! is_user_logged_in() )
				$search[] = "$wpdb->posts.post_password = ''";

			$search = ' AND ' . implode( ' AND ', $search );

		}

		return $search;
	}


	public function edit_post_link_target($link) {

		$link = str_replace('<a', '<a target="_blank"', $link);

		return $link;
	}

	public function image_sizes_choose( $size_names ) {

		$full = $size_names['full'];
		unset($size_names['full']);
		
		$new_sizes = array(
			'medium_large' => 'Medium large',
			'extra_large' => 'Extra large',
			'full' => $full,
		);

		return array_merge( $size_names, $new_sizes );
	}
	
	public function after_setup_theme() {
		global $popup;
		$popup = isset($_REQUEST['popup']) ? true : false;

		if($popup):
			show_admin_bar( false );
		endif;

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * This theme does not use a hard-coded <title> tag in the document head,
		 * WordPress will provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		remove_image_size( '1536x1536' );
		remove_image_size( '2048x2048' );

		add_image_size( 'extra_large', get_option('extra_large_size_w', 1600), get_option('extra_large_size_h', 0) );

		//add_theme_support( 'menus' );

		
		register_nav_menus(
			array(
				'primary' => 'Vị trí chính',
				'secondary_left' => 'Vị trí phụ trái',
				'secondary_right' => 'Vị trí phụ phải',
			)
		);

		add_theme_support('custom-background');

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		// Add support for Block Styles.
		//add_theme_support( 'wp-block-styles' );

		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		add_filter('get_the_archive_title_prefix', '__return_empty_string');

		add_action( 'pre_get_posts', [$this, 'query_post_type_for_search'] );
	}

	public function query_post_type_for_search( $query ) {
		if(!is_admin() && $query->is_main_query()) {

			if(isset($_GET['s'])) {
				$query->set('post_type', 'post');
			}

		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}
Setup::instance();