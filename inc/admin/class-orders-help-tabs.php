<?php
namespace HomeViet;

class Orders_Help_Tabs {

	private $screen;

	public function __construct( \WP_Screen $screen ) {
		$this->screen = $screen;
	}

	public function set_help_tabs( $screen_type ) {
		switch ( $screen_type ) {
			case 'list':
				$this->screen->add_help_tab( array(
					'id' => 'list_overview',
					'title' => 'Tổng quan',
					'content' =>'',
				) );

				$this->sidebar();

				return;
		}
	}

	private function content( $name ) {
		$content = array();

		$content['list_overview'] = '<p>On this screen, you can manage orders.</p>';



		if ( ! empty( $content[$name] ) ) {
			return $content[$name];
		}
	}

	public function sidebar() {
		$content = '<p><strong>For more information:</strong></p>';

		$this->screen->set_help_sidebar( $content );
	}
}
