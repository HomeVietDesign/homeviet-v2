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

		if(!(is_singular('contractor_page')||is_singular('contractor')) && (has_nav_menu('secondary_left') || has_nav_menu('secondary_right'))) {
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

	public static function contractor_menu() {
		global $view;

		$parent = null;
		if($view->post_parent>0) {
			$parent = get_post($view->post_parent);
		}

		$contractor_cats = get_terms([
			'taxonomy' => 'contractor_cat',
			'hide_empty' => false,
			//'hierarchical' => true,
			'parent' => 0
		]);
		
		if($contractor_cats) {
		?>
		<nav id="main-nav">
			<div class="main-nav-inner">
				<ul class="menu d-flex flex-wrap justify-content-center">
				<?php
				foreach ($contractor_cats as $key => $value) {
					$children = get_terms([
						'taxonomy' => 'contractor_cat',
						'hide_empty' => false,
						'parent' => $value->term_id
					]);
					//debug($children);

					if($children) {
						$page_id = get_term_meta( $value->term_id, '_page', true );
					?>
					<li class="menu-item menu-item-has-children d-flex position-relative align-items-center<?php
					if($parent && $parent->ID==$page_id) {
						echo ' current-menu-ancestor current-menu-parent';
					} elseif ($view->ID==$page_id) {
						echo ' current-menu-item';
					}
					?>">
						<a href="<?php echo esc_url(get_permalink($page_id)); ?>"><?=esc_html($value->name)?></a>
						<a href="javascript:void(0);" class="toggle-sub-menu d-flex align-items-center"><span class="dashicons dashicons-arrow-down-alt2"></span></a>
						<ul class="sub-menu position-absolute">
						<?php
						foreach ($children as $child) {
							$page_id = get_term_meta( $child->term_id, '_page', true );
							?>
							<li class="menu-item<?php
							if ($view->ID==$page_id) {
								echo ' current-menu-item';
							}
							?>">
								<a href="<?php echo esc_url(get_permalink($page_id)); ?>"><?=esc_html($child->name)?></a>
							</li>
							<?php
						}
						?>
						</ul>
					</li>
					<?php
					}
				}
				?>
				</ul>
			</div>
		</nav>
		<?php
		}
	}

	public static function primary_menu() {
		$object = get_queried_object();
		$display_menu = 'yes';
		$menu = false;
		$nav_menu = '';
		if(is_singular( 'contractor' ) || is_singular( 'contractor_page' )) {
			self::contractor_menu();
			return;
			//$menu = fw_get_db_settings_option('contractor_menu');
		} elseif(is_page() || is_single() || is_singular( 'seo_post' )) {
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