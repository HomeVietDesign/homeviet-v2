<?php
namespace HomeViet;

class Background_Process {

	private static $instance = null;

	private function __construct() {

		add_action('count_order_sent', [$this, 'count_order_sent']);
		add_action('add_customer_order', [$this, 'add_customer_order']);

	}

	public function add_customer_order($data) {
		// kiểm tra sự tồn tại của khách hàng thông qua số điện thoại
		// nếu đã tồn tại thì cập nhật tên
		// nếu chưa tồn tại thì thêm mới

		$customer = new Customer($data['phone'], $data['name']);
		$customer->save();

		if($customer->get_id()) {
			$order = new Order();
			$order->set_customer_id($customer->get_id());

			$utm_source = '';
			$utm_medium = '';

			$ref = base64_decode($data['ref']);

			$order->set_utm_source($utm_source);
			$order->set_utm_medium($utm_medium);
			$order->set_url($data['url']);
			$order->set_referrer($ref);
			$order->set_user_agent($data['user_agent']);
			$order->save();

			if($order->get_id()) {
				$id = (int) $data['id'];
				$order->insert_item([
					'name'=>$data['title'],
					'image'=>$data['image'],
					'type'=>$data['type'],
					'id'=>$id
				]);
			}
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