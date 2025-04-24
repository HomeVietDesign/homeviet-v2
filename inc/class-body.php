<?php
namespace HomeViet;

class Body {
	private static $instance = null;

	private function __construct() {
		add_action('wp_body_open', [$this, 'body_open_custom_code'], 5);
		add_action('wp_body_open', [$this, 'site_body_open'], 30);
		add_action('wp_footer', [$this, 'site_body_close'], 5);

		//add_filter('body_class', [$this, 'body_class']);
	}

	public function body_class($classes) {

		if ( get_query_var( 'savedlist' ) == true ) {
			$classes[] = 'page-template-savedlist';
		}

		return $classes;
	}

	public function site_body_close() {
		?>
		</div><!-- /#site-body -->
		<?php
	}

	public function site_body_open() {
		?>
		<div id="site-body">
		<?php
	}

	public function body_open_custom_code() {

		$custom_script = fw_get_db_settings_option('body_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Body::instance();