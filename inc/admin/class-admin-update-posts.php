<?php
namespace HomeViet;

class Admin_Update_Posts {

	private static $instance = null;

	private function __construct() {

		add_action('admin_init', [$this, 'admin_init']);
		
		add_action( 'admin_notices', [$this, 'update_gallery_images_notice'] );
	}

	public function update_gallery_images_notice() {
		if (!empty($_REQUEST['post_gallery_import'])) {
			$num_changed = (int) $_REQUEST['post_gallery_import'];
			printf('<div id="message" class="updated notice is-dismissable"><p>' . __('Published %d posts.', 'txtdomain') . '</p></div>', $num_changed);
		}
	}

	public function handle_bulk_post_action($redirect_url, $action, $post_ids) {
		if($action=='post_gallery_import') {
        	
        	if (!empty($post_ids)) {
        		foreach ($post_ids as $post_id) {
        			$_images = get_post_meta( $post_id, '_images', true );
        			if(empty($_images)) {
						// chưa có ảnh slide
						$post = get_post($post_id);

						if($post) {

							$builder = get_post_meta($post->ID, 'fw:opt:ext:pb:page-builder:json', true);
							$builder = json_decode($builder, true);

							$images = [];

							if(has_post_thumbnail( $post_id )) {
								$attachment_id = get_post_thumbnail_id( $post_id );
								$images[] = [
									'attachment_id' => $attachment_id,
									'url' => wp_get_attachment_url( $attachment_id )
								];
							}

							if(!empty($builder)) {
								foreach ($builder as $index => $element) {
									if($element['type']=='section' && !empty($element['_items'])) {
										foreach ($element['_items'] as $sub_index => $sub_element) {
											if($sub_element['type']=='column' && !empty($sub_element['_items'])) {
												foreach ($sub_element['_items'] as $sub_sub_index => $sub_sub_element) {
													if ($sub_sub_element['type']=='simple') {
														$this->update_images_gallery($sub_sub_element, $images);
													}
												}
											} elseif ($sub_element['type']=='simple') {
												$this->update_images_gallery($sub_element, $images);
											}
										}
									} elseif ($element['type']=='column' && !empty($element['_items'])) {
										foreach ($element['_items'] as $sub_index => $sub_element) {
											if ($sub_element['type']=='simple') {
												$this->update_images_gallery($sub_element, $images);
											}   
										}
									} elseif ($element['type']=='simple') {
										$this->update_images_gallery($element, $images);
									}
								}

							}

							//debug_log($images);

							update_post_meta( $post_id, '_images', $images );
						}

        			}
        		}
        	}
        }

        $redirect_url = add_query_arg('post_gallery_import', count($post_ids), $redirect_url);

		return $redirect_url;
	}

	public function update_images_gallery($element, &$images) {
		if($element['shortcode']=='grid_images') {
			if( !empty($element['atts']['images']) ) {
				foreach ($element['atts']['images'] as $key => $value) {
					$images[] = $value;
				}
			} 
		}

	}

	public function bulk_post_actions($bulk_actions) {
		$bulk_actions['post_gallery_import'] = 'Nhập ảnh slide';
		return $bulk_actions;
	}

	public function admin_init() {
		add_filter( 'bulk_actions-edit-post', [$this, 'bulk_post_actions'], 10, 1 );
		add_filter( 'handle_bulk_actions-edit-post', [$this, 'handle_bulk_post_action'], 10, 3 );
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Update_Posts::instance();