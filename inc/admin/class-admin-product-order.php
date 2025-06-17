<?php
namespace HomeViet;

class Admin_Product_Order {

	private static $instance = null;


	private function __construct() {
		
		if(is_admin()) {
			
			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );

			add_action( 'manage_product_order_posts_custom_column', [ $this, 'custom_columns_value' ], 2, 2 );
			add_filter( 'manage_product_order_posts_columns', [ $this, 'add_custom_columns_header' ] );

			add_action( 'add_meta_boxes', [$this, 'meta_boxes'] );

			//add_action( 'save_post_product_order', [$this, 'save_product_order'], 15, 3 );

			add_action('wp_ajax_send_purchase', [$this, 'ajax_send_purchase']);
			add_action('wp_ajax_cancel_purchase', [$this, 'ajax_cancel_purchase']);
		}
		
	}

	public function ajax_cancel_purchase() {
		$response = [
			'code' => 0,
			'data' => ''
		];

		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;

		if(current_user_can('publish_orders') && check_ajax_referer( 'quick_edit_'.$id, 'nonce', false )) {
			$order = get_post($id);
			
			$purchase = absint(get_post_meta($order->ID, 'purchase', true));
			
			if($order && $purchase==0 && $order->post_status=='pending') {
				wp_update_post([
					'ID' => $order->ID,
					'post_status' => 'publish'
				]);
				update_post_meta( $data['id'], '_purchase', 0 );
				$response['code'] = 1;
			}
		}
	
		wp_send_json($response);
		exit;
	}

	public function ajax_send_purchase() {
		$response = [
			'code' => 0,
			'data' => ''
		];

		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;

		if(current_user_can('publish_orders') && check_ajax_referer( 'quick_edit_'.$id, 'nonce', false )) {
			$order = get_post($id);
			
			$purchase = absint(get_post_meta($order->ID, 'purchase', true));
			
			if($order && $purchase==0 && $order->post_status=='pending') {
				do_action('purchase', ['id'=>$order->ID]);
				$response['code'] = 1;
			}
		}
	
		wp_send_json($response);
		exit;
	}

	public function save_product_order($post_id, $post, $update) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		
		if ( wp_is_post_revision( $post_id ) ) return;

		if ( ! current_user_can( 'publish_orders', $post_id ) ) {
			return;
		}
    	
		// if($post->post_status=='publish') {
		// 	$event_data = get_post_meta($post->ID, '_event_data', true);
		// 	$order_data = get_post_meta($post->ID, '_data', true);
		// 	do_action('purchase', ['event_data'=>$event_data,'order_data'=>$order_data]);
		// }
	}

	public function meta_boxes() {
		add_meta_box(
            'order_data'     // Reusing just 'postexcerpt' doesn't work.
        ,   'Thông tin đơn hàng'    // Title
        ,   array ( $this, 'order_data' ) // Display function
        ,   'product_order'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'core'            // Priority
        );

        add_meta_box(
            'event_data'     // Reusing just 'postexcerpt' doesn't work.
        ,   'Thông tin sự kiện'    // Title
        ,   array ( $this, 'event_data' ) // Display function
        ,   'product_order'              // Screen, we use all screens with meta boxes.
        ,   'normal'          // Context
        ,   'core'            // Priority
        );
	}

	public function event_data($post) {
		$event_data = get_post_meta($post->ID, '_event_data', true);
		debug($event_data);
	}

	public function order_data($post) {
		$order_data = get_post_meta($post->ID, '_data', true);
		debug($order_data);
	}

	public function enqueue_scripts($hook) {
		//debug_log($hook);
		global $post_type;

		if($post_type=='product_order' && ($hook=='edit.php' || $hook=='post.php')) {
			//add_thickbox();
			wp_enqueue_script('edit-product-order', THEME_URI.'/assets/js/admin-edit-product-order.js', array('jquery'), '', false);
			wp_enqueue_style('edit-product-order', THEME_URI.'/assets/css/admin-edit-product-order.css', array(), '');
		}

	}

	/**
	 * giá trị các cộng thông tin mởi rộng cho đối tượng(post)
	 */
	public function custom_columns_value( $column, $post_id ) {
		$quick_edit_nonce = wp_create_nonce('quick_edit_'.$post_id);
		$post = get_post($post_id);

		$event_data = get_post_meta($post_id, '_event_data', true);
    	$order_data = get_post_meta($post_id, '_data', true);
    	$referrer = $order_data['url'].(($order_data['referrer']!='')?','.$order_data['referrer']!='':'');

		switch ($column) {
			case 'ID':
				echo esc_html($post_id);
				break;

			case 'image':
				echo esc_html($post_id);
				break;

			case 'phone':
				if(!empty($event_data['ph'])) {
					foreach ($event_data['ph'] as $key => $value) {
						?><div><?=esc_html(phone_8420($value))?></div><?php
					}
				}
				break;

			case 'source':
				if(strpos($referrer, 'facebook')!==false || strpos($referrer, 'fbclid')!==false) {
					echo 'Facebook';
				} elseif (strpos($referrer, 'google')!==false || strpos($referrer, 'gclid')!==false) {
					echo 'Google';
				} elseif (strpos($referrer, 'youtube')!==false) {
					echo 'Youtube';
				} elseif (strpos($referrer, 'zalo')!==false) {
					echo 'Zalo';
				} else {
					echo '(Không xác định)';
				}
				break;

			case 'ads':
				if( preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches) ) {
					echo esc_html(str_replace('+', ' ', $matches[1]));
				}
				break;

			case 'product':
				if($order_data['id']) {
					echo '<a href="'.esc_url(get_permalink( $order_data['id'] )).'" target="_blank">'.esc_html(get_the_title($order_data['id'])).'</a>';
				} else {
					echo 'Gửi số tư vấn';
				}
				break;

			case 'tasks':
				$purchase = absint(get_post_meta($post_id, '_purchase', true));
				?>
				<div class="product-order-tasks">
				<?php if($post->post_status=='pending' && $purchase==0) { ?>
					<button type="button" class="button button-primary send-purchase" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>">Duyệt đơn</button>
					<button type="button" class="button button-secondary cancel-purchase" data-id="<?=$post_id?>" data-nonce="<?=esc_attr($quick_edit_nonce)?>">Hủy đơn</button>
				<?php } else if($post->post_status=='publish' && $purchase==0) { ?>
					<span class="canceled">Đã hủy</span>
				<?php } else if($post->post_status=='publish' && $purchase==1) { ?>
					<span class="purchased">Đã duyệt</span>
				<?php } ?>
				</div>
				<?php
				break;
			
		}
		
	}

	/**
	 * Tiêu đề các cột thông tin mở rộng cho đối tượng(post)
	 */
	public function add_custom_columns_header( $columns ) {
		
		$cb = $columns['cb'];
		unset($columns['cb']);

		$title = $columns['title'];
		unset($columns['title']);

		if(isset($columns['thumbnail'])) {
			unset($columns['thumbnail']);
		}

		$new_columns = ['cb' => $cb, 'title' => $title];

		$new_columns['ID'] = 'ID';
		$new_columns['phone'] = 'Số điện thoại';
		$new_columns['source'] = 'Nguồn';
		$new_columns['ads'] = 'Quảng cáo';
		$new_columns['product'] = 'Sản phẩm';
		$new_columns['tasks'] = 'Tác vụ';

		$columns = array_merge($new_columns, $columns);
		return $columns;

	}


	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Product_Order::instance();