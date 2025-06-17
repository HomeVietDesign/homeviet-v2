<?php
namespace HomeViet;

class Background_Process {

	private static $instance = null;

	private function __construct() {

		//add_action('count_order_sent', [$this, 'count_order_sent']);
		//add_action('add_customer_order', [$this, 'add_customer_order']);
		add_action('add_product_order', [$this, 'add_product_order']);

	}

	public function add_product_order($data) {
		$order_id = wp_insert_post([
            'post_type' => 'product_order',
            'post_title' => $data['order_data']['name'],
            'post_name' => 'order-'.current_time( 'U' ),
            'post_status' => 'pending',
            'post_author' => 0
        ]);

        if( !($order_id instanceof \WP_Error) && $order_id>0 ) {
            update_post_meta($order_id, '_data', $data['order_data']);
            update_post_meta($order_id, '_event_data', $data['event_data']);
        }
	}

	public function add_customer_order($data) {
		// kiểm tra sự tồn tại của khách hàng thông qua số điện thoại
		// nếu đã tồn tại thì cập nhật tên
		// nếu chưa tồn tại thì thêm mới
		
		$order_id = wp_insert_post([
			'post_type' => 'product_order',
			'post_title' => $data['name'],
			'post_name' => 'order-'.current_time( 'U' ),
			'post_status' => 'pending'
		]);
		if( !($order_id instanceof \WP_Error) && $order_id>0 ) {
			$meta_data = [
				'url' => $data['url'],
				'referrer' => base64_decode($data['ref']),
				'user_agent' => $data['user_agent'],
				'image' => $data['image'],
				'type' => $data['type'],
				'id' => $data['id'],
			];
			update_post_meta($order_id, '_data', $term_id);
		}
		
	}



	public function count_order_sent($id) {
		// debug_log(__METHOD__);
		// debug_log($id);
		$order_count = absint(get_post_meta($id, '_order_count', true));
		update_post_meta( $id, '_order_count', ++$order_count );
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Background_Process::instance();