<?php
namespace HomeViet;

class Background_Process {

	private static $instance = null;

	private function __construct() {

		//add_action('count_order_sent', [$this, 'count_order_sent']);
		add_action('add_product_order', [$this, 'add_product_order']);

	}

	public function add_product_order($events) {
		$order_id = wp_insert_post([
            'post_type' => 'product_order',
            'post_title' => $events['order_data']['name'],
            'post_name' => 'order-'.current_time( 'U' ),
            'post_status' => 'pending',
            'post_author' => 0
        ]);

        if( !($order_id instanceof \WP_Error) && $order_id>0 ) {
            update_post_meta($order_id, '_events', $events);
            //update_post_meta($order_id, '_event_data', $data['event_data']);
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