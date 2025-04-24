<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Popup_Video extends FW_Shortcode
{


	public function _init()
	{
		add_action('wp_footer', [$this, 'modal_template']);
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		//$atts['vid'] = self::get_youtube_id(trim( $atts['url'] ));

		$this->enqueue_static();
		return fw_render_view($this->locate_path('/views/view.php'), compact('atts', 'content', 'tag'));
	}

	public static function modal_template() {
		?>
		<!-- Modal -->
		<div class="modal fade popup-video-shortcode-modal" id="popup-video-shortcode-modal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-lg">
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				<div class="modal-content">
					<div class="modal-body">
						
					</div>
				</div>
			</div>
		</div>
		<?php
	}


}
