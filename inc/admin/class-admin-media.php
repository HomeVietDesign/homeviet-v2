<?php
namespace HomeViet;

class Admin_Media {

	private static $instance = null;

	private function __construct() {
		if(is_admin()) {
			// add_filter( 'media_row_actions', [$this, 'media_row_actions'], 10, 2 );

			// add_filter( 'bulk_actions-upload', [$this, 'bulk_actions_upload'] );
			// add_filter( 'handle_bulk_actions-upload', [$this, 'handle_bulk_actions_upload'], 1000, 3 );

			add_action( 'admin_init', [$this, 'add_media_settings'] );

		}
	}

	public function add_media_settings() {
		
		//register setting to save the data
		register_setting( 'media', 'medium_large_size_w' );
		register_setting( 'media', 'medium_large_size_h' );

		register_setting( 'media', 'extra_large_size_w' );
		register_setting( 'media', 'extra_large_size_h' );

		//add a sample field to this section.
		add_settings_field(
			'medium_large_sizes',
			'Medium large',
			[$this, 'medium_large_sizes_html'],
			'media',
			'default'
		);

		add_settings_field(
			'extra_large_sizes',
			'Extra large',
			[$this, 'extra_large_sizes_html'],
			'media',
			'default'
		);
	}

	public function medium_large_sizes_html($args) {
		?>
		<fieldset>
			<label for="medium_large_size_w"><?php _e( 'Max Width' ); ?></label>
			<input name="medium_large_size_w" type="number" step="1" min="0" id="medium_large_size_w" value="<?php form_option( 'medium_large_size_w' ); ?>" class="small-text" />
			<br />
			<label for="medium_large_size_h"><?php _e( 'Max Height' ); ?></label>
			<input name="medium_large_size_h" type="number" step="1" min="0" id="medium_large_size_h" value="<?php form_option( 'medium_large_size_h' ); ?>" class="small-text" />
		</fieldset>
		<?php
	}

	public function extra_large_sizes_html($args) {
		?>
		<fieldset>
			<label for="extra_large_size_w"><?php _e( 'Max Width' ); ?></label>
			<input name="extra_large_size_w" type="number" step="1" min="0" id="extra_large_size_w" value="<?php form_option( 'extra_large_size_w' ); ?>" class="small-text" />
			<br />
			<label for="extra_large_size_h"><?php _e( 'Max Height' ); ?></label>
			<input name="extra_large_size_h" type="number" step="1" min="0" id="extra_large_size_h" value="<?php form_option( 'extra_large_size_h' ); ?>" class="small-text" />
		</fieldset>
		<?php
	}

	public function handle_bulk_actions_upload($redirect_to, $action_name, $post_ids) {
		

		return $redirect_to;
	}

	public function bulk_actions_upload($actions) {
			$actions['mcloud_regenerate_thumbnail'] = 'Rebuild Thumbnails';

			return $actions;
		}

	public function media_row_actions($actions, $post){
		if (strpos($post->post_mime_type, 'image') === 0) {
			$nonce = wp_create_nonce('media_cloud_regenerate_thumbnail');
			
			$newaction['mcloud_regenerate_thumbnail'] = '<a data-post-id="'.$post->ID.'" data-nonce="'.$nonce.'" class="mcloud-regenerate-thumbnail" href="#" title="Rebuild Thumbnails">Rebuild Thumbnails</a>';

			return array_merge($actions,$newaction);
		}

		return $actions;
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Media::instance();