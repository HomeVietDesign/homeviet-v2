<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Panorama extends FW_Shortcode
{


	public function _init()
	{
		add_action('wp_head', [$this, 'head_scripts']);
		add_action('wp_footer', [$this, 'footer_scripts']);
	}

	public function head_scripts() {
		?>
		<link rel="stylesheet" href="https://unpkg.com/photo-sphere-viewer@4/dist/photo-sphere-viewer.css" />
		<?php
	}

	public function footer_scripts() {
		?>
		<!-- Three.js (bắt buộc) -->
		<script src="https://unpkg.com/three@0.158.0/build/three.min.js"></script>

		<!-- uEvent (EventEmitter cho PSV) -->
		<script src="https://unpkg.com/uevent@2.0.0/browser.js"></script>

		<!-- Photo Sphere Viewer core -->
		<script src="https://unpkg.com/photo-sphere-viewer@4/dist/photo-sphere-viewer.js"></script>
		<?php
	}


}
