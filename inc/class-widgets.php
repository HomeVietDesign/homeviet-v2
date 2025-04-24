<?php
namespace HomeViet;

class Widgets {

	private static $instance = null;

	private function __construct() {
		add_action('widgets_init', [$this, 'widgets_init']);
	}

	public function widgets_init() {
		register_sidebar(
			array(
				'name'          => 'Footer 1',
				'id'            => 'footer-1',
				'description'   => __( 'Add widgets here to appear in your footer.' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => 'Footer 2',
				'id'            => 'footer-2',
				'description'   => __( 'Add widgets here to appear in your footer.' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => 'Footer 3',
				'id'            => 'footer-3',
				'description'   => __( 'Add widgets here to appear in your footer.' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);

		register_sidebar(
			array(
				'name'          => 'Chi tiết công trình',
				'id'            => 'product_info',
				'description'   => 'Hiên thị thông tin chung cho tất cả các công trình.',
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}
Widgets::instance();