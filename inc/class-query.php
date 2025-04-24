<?php
namespace HomeViet;

class Query {

	private static $instance = null;

	private function __construct() {

		//add_action( 'pre_get_posts', [$this, 'query_post'] );

		//add_filter( 'posts_request', [$this, 'debug_request'], 10, 2 );

		add_filter( 'posts_clauses', [$this, 'custom_fields_search'], 10, 2 );
		add_filter( 'posts_clauses', [$this, 'post_name_search'], 10, 2 );

	}

	public function debug_request($request, $query) {
		if($query->is_main_query()) {
			if($query->is_search()) {
				debug_log($request);
			}
		}

		return $request;
	}

	public function post_name_search($pieces, $wp_query) {

		if($wp_query->get('post_type', '')=='post' && $wp_query->get('s')!='' ) {
			global $wpdb;

			$keywords        = explode(' ', $wp_query->get('s'));
			$escaped_percent = $wpdb->placeholder_escape();
			$query           = "";

			if($keywords) {
				$query = "(";
				foreach ($keywords as $key => $word) {
					if($key>0) {
						$query .= " OR {$wpdb->posts}.post_name LIKE '{$escaped_percent}{$word}{$escaped_percent}'";
					} else {
						$query .= "{$wpdb->posts}.post_name LIKE '{$escaped_percent}{$word}{$escaped_percent}'";
					}
					
				}

				$query .= ") OR ";
			}

			if ( ! empty( $query ) ) { // append necessary WHERE and JOIN options.
				$pieces['where'] = str_replace( "((({$wpdb->posts}.post_title LIKE '{$escaped_percent}", "(({$query} ({$wpdb->posts}.post_title LIKE '{$escaped_percent}", $pieces['where'] );

				$pieces['orderby'] = "{$wpdb->posts}.post_name DESC,".$pieces['orderby'];
			}

			$pieces['distinct'] = "DISTINCT";
			//debug_log($pieces);
		}

		return $pieces;
	}

	public function custom_fields_search($pieces, $wp_query) {

		if($wp_query->get('post_type', '')=='contractor' && $wp_query->get('s')!='' ) {
			global $wpdb;

			$keywords        = explode(' ', $wp_query->get('s'));
			$escaped_percent = $wpdb->placeholder_escape();
			$query           = "";

			foreach ($keywords as $word) {
				$query .= " (pms.meta_value LIKE '{$escaped_percent}{$word}{$escaped_percent}') OR ";
			}

			if ( ! empty( $query ) ) { // append necessary WHERE and JOIN options.
				$pieces['where'] = str_replace( "((({$wpdb->posts}.post_title LIKE '{$escaped_percent}", "( {$query} (({$wpdb->posts}.post_title LIKE '{$escaped_percent}", $pieces['where'] );
				$pieces['join'] = $pieces['join'] . " INNER JOIN {$wpdb->postmeta} AS pms ON ({$wpdb->posts}.ID = pms.post_id) ";
			}

			$pieces['distinct'] = "DISTINCT";
			//debug_log($pieces);
		}

		

		return $pieces;
	}

	public function query_post( $wp_query ) {
		if( $wp_query->is_main_query() ) {
			if( $wp_query->get('post_type', '')=='post' && $wp_query->get('s')!='' ) {
				$meta_query = $wp_query->get('meta_query');
				if(empty($meta_query)) $meta_query = [];
				$meta_query['phone_number'] = [
					'key' => '_phone_number'
				];
				$wp_query->set('meta_query', $meta_query);
			}
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}
Query::instance();