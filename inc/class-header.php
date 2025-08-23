<?php
namespace HomeViet;

class Header {

	private static $instance = null;

	private function __construct() {
		add_action( 'template_redirect', [$this, 'display_header_html'] );
		
	}

	public function site_header() {
		
		?>
		<header id="site-header" class="position-sticky">
		<?php
		self::primary_menu();
		//self::primary_menu_2();

		if(has_nav_menu('secondary_left') || has_nav_menu('secondary_right')) {
			?>
			<nav id="secondary-nav" class="">
				<div class="container p-0">
					<div class="d-flex flex-wrap justify-content-center overflow-hidden">
					<?php
					if(has_nav_menu('secondary_left')) {
						?>
						<div class="secondary-menu-left">
							<?php wp_nav_menu([
								'theme_location' => 'secondary_left',
								'container' => false,
								'echo' => true,
								'fallback_cb' => '',
								'depth' => 1,
								'walker' => new \HomeViet\Walker_Secondary_Menu(),
								'items_wrap' => '<ul class="menu list-unstyled p-0 m-0 d-flex">%3$s</ul>',
							]); ?>
						</div>
						<?php
					}

					if(has_nav_menu('secondary_right')) {
						?>
						<div class="secondary-menu-right">
							<?php wp_nav_menu([
								'theme_location' => 'secondary_right',
								'container' => false,
								'echo' => true,
								'fallback_cb' => '',
								'depth' => 1,
								'walker' => new \HomeViet\Walker_Secondary_Menu(),
								'items_wrap' => '<ul class="menu list-unstyled p-0 m-0 d-flex">%3$s</ul>',
							]); ?>
						</div>
						<?php
					}
					?>
					</div>
				</div>
			</nav>
			<?php
		}
		?>
		</header>
		<?php
		
	}


	public function primary_menu_2() {
		$nav_menu = wp_nav_menu([
				'theme_location' => 'primary',
				'container' => false,
				'echo' => false,
				'fallback_cb' => '',
				'depth' => 2,
				'walker' => new \HomeViet\Walker_Primary_Menu(),
				'items_wrap' => '<ul class="%2$s">%3$s</ul>',
			]);
		if($nav_menu!='') {
			?>
			<nav id="main-nav">
				<div class="main-nav-inner"><?php echo $nav_menu; ?></div>
			</nav>
			<?php
		}
	}

	public static function post_tax_header() {

		the_archive_title( '<h1 class="post-tax-header pt-4 text-center">', '</h1>' );
		the_archive_description( '<div class="post-tax-description">', '</div>' );
		
	}

	public static function primary_menu() {
		$object = get_queried_object();
		$display_menu = 'yes';
		$menu = false;
		$nav_menu = '';
		
		if(is_page() || is_single() || is_singular( 'seo_post' )) {
			$display_menu = fw_get_db_post_option($object->ID, 'display_menu', 'yes');
			$menu = fw_get_db_post_option($object->ID, 'apply_menu');
		} else if(is_category() || is_tax()) {
			$display_menu = fw_get_db_term_option($object->term_id, $object->taxonomy, 'display_menu', 'yes');
			$menu = fw_get_db_term_option($object->term_id, $object->taxonomy, 'apply_menu');
		}

		if($display_menu=='yes') {
			$obj_menu = ($menu) ? wp_get_nav_menu_object( $menu[0] ): false;
			if($obj_menu) {
				$nav_menu = wp_nav_menu([
					'menu' => $obj_menu,
					'container' => false,
					'echo' => false,
					'fallback_cb' => '',
					'depth' => 2,
					'walker' => new \HomeViet\Walker_Primary_Menu(),
					'items_wrap' => '<ul class="%2$s d-flex flex-wrap justify-content-center">%3$s</ul>',
				]);

			} else if(has_nav_menu('primary')) {
				$nav_menu = wp_nav_menu([
					'theme_location' => 'primary',
					'container' => false,
					'echo' => false,
					'fallback_cb' => '',
					'depth' => 2,
					'walker' => new \HomeViet\Walker_Primary_Menu(),
					'items_wrap' => '<ul class="%2$s d-flex flex-wrap justify-content-center">%3$s</ul>',
				]);
			}

			
			if($nav_menu!='') {
				?>
				<nav id="main-nav">
					<div class="main-nav-inner"><?php echo $nav_menu; ?></div>
				</nav>
				<?php
			}
			
		}
	
	}

	public function display_header_html() {
		global $popup;
		if( !$popup ) {
			add_action('wp_body_open', [$this, 'site_header'], 10);
		}
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Header::instance();