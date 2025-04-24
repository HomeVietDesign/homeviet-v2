<?php
namespace HomeViet;

class Theme {

	private static $instance = null;

	private function __construct() {
	
		include_once THEME_DIR.'/inc/global-functions.php';
		include_once THEME_DIR.'/inc/unyson/class-unyson.php';
		include_once THEME_DIR.'/inc/admin/class-admin.php';

		include_once THEME_DIR.'/inc/class-custom-types.php';
		include_once THEME_DIR.'/inc/class-background-process.php';
		include_once THEME_DIR.'/inc/class-customer.php';
		include_once THEME_DIR.'/inc/class-order.php';

		if(class_exists('\\FileBird\\Plugin')) {
			include_once THEME_DIR.'/inc/filebird/class-filebird.php';
		}

		if(class_exists('\\FacebookPixelPlugin\\FacebookForWordpress')) {
			include_once THEME_DIR.'/inc/official-facebook-pixel/class-official-facebook-pixel.php';
		}

		if(class_exists('WPCF7_ContactForm')) {
			include_once THEME_DIR.'/inc/wpcf7/class-wpcf7.php';
		}

		if(class_exists('WP_Statistics')) {
			include_once THEME_DIR.'/inc/wp-statistics/class-wp-statistics.php';
		}

		include_once THEME_DIR.'/inc/class-authentication.php';
		
		if(unyson_exists()) {

			include_once THEME_DIR.'/inc/class-common.php';
			include_once THEME_DIR.'/inc/class-template-tags.php';
			include_once THEME_DIR.'/inc/class-setup.php';
			include_once THEME_DIR.'/inc/class-query.php';
			include_once THEME_DIR.'/inc/class-assets.php';
			include_once THEME_DIR.'/inc/class-ajax.php';
			include_once THEME_DIR.'/inc/class-head.php';
			include_once THEME_DIR.'/inc/class-body.php';
			include_once THEME_DIR.'/inc/class-header.php';
			include_once THEME_DIR.'/inc/class-footer.php';

			include_once THEME_DIR.'/inc/class-shortcode.php';

			include_once THEME_DIR.'/inc/class-walker-primary-menu.php';
			include_once THEME_DIR.'/inc/class-walker-secondary-menu.php';

			// widgets
			include_once THEME_DIR.'/inc/class-widgets.php';

		}

		add_action('after_switch_theme', [$this, 'theme_activation']);
		add_action('switch_theme', [$this, 'theme_deactivation']);
	}

	public function theme_activation() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		global $table_prefix, $wpdb;

		$customer_table = $table_prefix . 'customer';
		$order_stats_table = $table_prefix . 'order_stats';
		$order_items_table = $table_prefix . 'order_items';
		$charset_collate = $wpdb->get_charset_collate();

		if( $wpdb->get_var( "show tables like '{$customer_table}'" ) != $customer_table ) {
			$sql = "CREATE TABLE {$customer_table} (
				customer_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				phone_number varchar(13) NOT NULL,
				fullname varchar(100) NOT NULL,
				date_created datetime NULL DEFAULT NULL,
				date_created_gmt datetime NULL DEFAULT NULL,
				PRIMARY KEY (customer_id),
				UNIQUE KEY (phone_number)
			) {$charset_collate};";

			dbDelta( $sql );
		}

		if( $wpdb->get_var( "show tables like '{$order_stats_table}'" ) != $order_stats_table ) {
			$sql = "CREATE TABLE {$order_stats_table} (
				order_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				customer_id bigint(20) UNSIGNED NOT NULL,
				utm_source varchar(20) NULL,
				utm_medium varchar(20) NULL,
				url text NULL,
				referrer text NULL,
				user_agent text NULL,
				date_created datetime NULL DEFAULT NULL,
				date_created_gmt datetime NULL DEFAULT NULL,
				PRIMARY KEY (order_id),
				KEY (customer_id)
			) {$charset_collate};";

			dbDelta( $sql );
		}

		if( $wpdb->get_var( "show tables like '{$order_items_table}'" ) != $order_items_table ) {
			$sql = "CREATE TABLE {$order_items_table} (
				order_item_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				order_id bigint(20) UNSIGNED NOT NULL,
				item_name text NULL,
				item_image text NULL,
				item_type varchar(10) NULL,
				item_type_text text NULL,
				item_note text NULL,
				item_id bigint(20) NULL DEFAULT NULL,
				PRIMARY KEY (order_item_id),
				KEY (order_id),
				KEY (item_id)
			) {$charset_collate};";

			dbDelta( $sql );
		}
	}

	public function theme_deactivation() {
		
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

