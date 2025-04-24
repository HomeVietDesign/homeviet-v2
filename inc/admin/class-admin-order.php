<?php
namespace HomeViet;

class Admin_Order {

	private static $instance = null;

	const ADMIN_SLUG = 'admin_order';

	private function __construct() {
		if(is_admin()) {
			add_action('admin_menu', [$this, 'admin_menu']);

			// lưu các trường tùy biến cho trang quản trị (screen là đối tượng trang quản trị)
			add_filter('set_screen_option_'.self::ADMIN_SLUG.'_per_page', [$this, 'set_screen_option'], 10, 3 );

			add_action('wp_ajax_order_detail', [$this, 'ajax_order_detail']);
			add_action('wp_ajax_change_order_item_note', [$this, 'ajax_change_order_item_note']);

			add_action( 'admin_enqueue_scripts', [$this, 'enqueue_scripts'] );
		}
	}

	public function enqueue_scripts($hook) {
		//debug_log($hook);
		if($hook=='toplevel_page_admin_order') {
			wp_enqueue_style( 'admin-order', THEME_URI.'/assets/css/admin-order.css' );
			wp_enqueue_script('admin-order', THEME_URI.'/assets/js/admin-order.js', array('jquery'), '', false);
		}
	}

	public function ajax_change_order_item_note() {
		$response = [
			'code' => 0,
			'data' => ''
		];
		$order_item_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		check_ajax_referer( 'edit-order-item-note-'.$order_item_id, 'nonce', true );
		$note = isset($_REQUEST['note']) ? sanitize_textarea_field($_REQUEST['note']) : '';
		$item = Order::update_order_item($order_item_id, ['item_note'=>wp_unslash($note)]);
		$response['code'] = 1;
		$response['data'] = $item->item_note;

		wp_send_json($response);
		exit;
	}

	public function ajax_order_detail() {
		$order_id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		check_ajax_referer( 'detail-order-'.$order_id, 'nonce', true );

		$order = new Order($order_id);
		$item = $order->get_item();
		$customer = $order->get_customer();

		?>
		
		<!DOCTYPE html>
		<html>
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title></title>
			<style type="text/css">
				html, body {
					font-family: arial;
					font-size: 14px;
				}
				table {
					width: 100%;
					border-collapse: collapse;
					table-layout: fixed;
				}
				table th,
				table td {
					padding: 5px;
					border: #eee 1px solid;
					vertical-align: top;
				}
				table th {
					text-align: left;
					width: 150px;
				}
				a {
					color: blue;
					text-decoration: none;
				}
				a:focus,
				a:hover {
					text-decoration: underline;
				}
			</style>
		</head>
		<body>
		
			<h3>THÔNG TIN KHÁCH HÀNG</h3>
			<table>
				<tr>
					<th>Họ tên:</th>
					<td><?=esc_html($customer->fullname)?></td>
				</tr>
				<tr>
					<th>Số điện thoại:</th>
					<td><?=esc_html($customer->phone_number)?></td>
				</tr>
				<tr>
					<th>Yêu cầu:</th>
					<td><?=esc_html($item->item_type_text)?></td>
				</tr>
				<tr>
					<th>Ghi chú:</th>
					<td><?=(($item->item_note)?esc_html($item->item_note):'')?></td>
				</tr>
			</table>
			<h3>THÔNG TIN SẢN PHẨM</h3>
			<table>
				<tr>
					<th>Tiêu đề link:</th>
					<td><a href="<?php echo esc_url(get_permalink($item->item_id)); ?>" target="_blank"><?=esc_html($item->item_name)?></a></td>
				</tr>
				<tr>
					<th>Hình ảnh:</th>
					<td><img width="300" src="<?=esc_html($item->item_image)?>"></td>
				</tr>
			</table>
			<h3>THÔNG TIN ĐƠN HÀNG</h3>
			<table>
				<tr>
					<th>Ngày đặt hàng:</th>
					<td><?=esc_html(date('d/m/Y H:i:s', strtotime($order->get_date_created())))?></td>
				</tr>
				<tr>
					<th>URL:</th>
					<td><?=esc_html($order->get_url())?></td>
				</tr>
				<tr>
					<th>Quá trình truy cập:</th>
					<td><?php
					$referrers = explode(',', $order->get_referrer());
					if(!empty($referrers)) {
						foreach ($referrers as $key => $value) {
							?>
							<p style="word-wrap: break-word;"><a href="<?=esc_url($value)?>" target="_blank"><?=esc_html($value)?></a></p>
							<?php
						}
					}
					?></td>
				</tr>
				<tr>
					<th>Tác nhân:</th>
					<td><?=esc_html($order->get_user_agent())?></td>
				</tr>
			</table>
		</body>
		</html>
		<?php
		// debug($order);
		// debug($customer);
		// debug($item);

		die;
	}

	public static function set_screen_option( $result, $option, $value ) {
		$orders_screens = array(
			self::ADMIN_SLUG.'_per_page',
		);

		if ( in_array( $option, $orders_screens ) ) {
			$result = $value;
		}

		return $result;
	}

	public function admin_order_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Sorry, you are not allowed to edit the orders for this site.' ) );
		}

		add_thickbox();

		$wp_list_table = new Orders_List_Table();

		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<p></p>
			
		</div>

		<?php

		$wp_list_table->prepare_items();

		?>
		<div class="wrap">
			<!-- <h1><?php echo esc_html(get_admin_page_title()); ?></h1> -->
			<hr class="wp-header-end">
			<?php
			if ( isset( $_REQUEST['deleted'] ) ) {
				echo '<div id="message" class="updated notice is-dismissible"><p>';
				$deleted = (int) $_REQUEST['deleted'];
				/* translators: %s: Number of orders. */
				printf( _n( '%s order deleted.', '%s orders deleted.', $deleted ), $deleted );
				echo '</p></div>';
				$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] );
			}
			?>

			<form id="posts-filter" method="get">
				<input type="hidden" name="page" value="<?=self::ADMIN_SLUG?>">
				<?php $wp_list_table->search_box( __( 'Search Orders' ), 'order' ); ?>

				<?php $wp_list_table->display(); ?>

				<div id="ajax-response"></div>
			</form>

		</div>
		
		<?php
	}

	public static function current_action() {
		if ( isset( $_REQUEST['action'] ) and -1 != $_REQUEST['action'] ) {
			return $_REQUEST['action'];
		}

		if ( isset( $_REQUEST['action2'] ) and -1 != $_REQUEST['action2'] ) {
			return $_REQUEST['action2'];
		}

		return false;
	}

	public function orders_load_admin() {
		wp_reset_vars( array( 'action', 'order_id' ) );

		$action = self::current_action();

		$redirect_to = menu_page_url( self::ADMIN_SLUG, false );

		if ( 'delete' == $action ) {
			$order_id = isset($_REQUEST['order_id']) ? absint($_REQUEST['order_id']) : 0;
			$bulkorders   = isset($_REQUEST['ordercheck']) ? (array) $_REQUEST['ordercheck'] : [];
			
			if ( $order_id  ) {
				check_admin_referer( 'delete-order_' . $order_id );
				$bulkorders[] = $order_id;
				
				// if($_SERVER['HTTP_REFERER']) {
				// 	$redirect_to = add_query_arg( 'deleted', 1, $_SERVER['HTTP_REFERER'] );
				// }
			}
			else {
				check_admin_referer( 'bulk-orders' );
			}

			if(!empty($bulkorders)) {
				$deleted = 0;
				foreach ( $bulkorders as $order_id ) {
					$order_id = (int) $order_id;
					if(Order::delete( $order_id )) {
						$deleted += 1;
					} else {
						wp_die( "Error in deleting #".$order_id );
					}
				}

				$redirect_to = add_query_arg( 'deleted', $deleted, $redirect_to );
			}
			
			wp_safe_redirect( $redirect_to );
			exit();
		} else {
			
			if(isset($_GET['_wp_http_referer']) && ''!=$_GET['_wp_http_referer']) {
				wp_safe_redirect( remove_query_arg(['_wp_http_referer', '_wpnonce'], $_SERVER['REQUEST_URI']) );
				exit();
			}

		}

		// đối tượng trang quản trị hiện tại
		$current_screen = get_current_screen();

		//$help_tabs = new Orders_Help_Tabs( $current_screen );

		add_filter('manage_' . $current_screen->id . '_columns', ['HomeViet\Orders_List_Table', 'define_columns'], 0, 0);

		// tạo các trường tùy biến cho trang quản trị (screen là đối tượng trang quản trị)
		add_screen_option( 'per_page', ['default' => 20, 'option' => self::ADMIN_SLUG.'_per_page'] );

		add_filter('manage_' . $current_screen->id . '_columns', [$this, 'admin_orders_custom_columns'], 10, 1);

		add_action('manage_orders_custom_column', [$this, 'admin_orders_custom_value'], 10, 2);
	}

	public function admin_orders_custom_value($column_name, $order) {
		$order = new Order($order->order_id);
		$item = $order->get_item();
		//$customer = $order->get_customer();
		$referrer = urldecode($order->get_referrer().','.$order->get_url());
		$nonce = wp_create_nonce('edit-order-item-note-'.$item->order_item_id);
		switch ($column_name) {
			case 'item_source':
				if(strpos($referrer, 'facebook')!==false || strpos($referrer, 'fbclid')!==false) {
					echo 'Facebook';
				} elseif (strpos($referrer, 'google')!==false || strpos($referrer, 'gclid')!==false) {
					echo 'Google';
				} elseif (strpos($referrer, 'zalo')!==false) {
					echo 'Zalo';
				}

				break;
			case 'ad':
				//echo esc_html($referrer);
				if( preg_match("/(?:.*)utm_content=([^,&]+)(?:.*)/", $referrer, $matches) ) {
					//debug($matches);
					echo esc_html(str_replace('+', ' ', $matches[1]));
				}
				break;
			case 'item_note':
				if($item) {
					?>
					<div class="item-note-edit-wrap">
						<div class="item-note-display"><?php echo ($item->item_note)?nl2br(esc_html($item->item_note)):''; ?></div>
						<div class="item-note-input-wrap"><textarea class="item-note-input" data-id="<?php echo absint($item->order_item_id); ?>" data-nonce="<?=esc_attr($nonce)?>"><?php echo ($item->item_note)?esc_textarea($item->item_note):''; ?></textarea></div>
						<a href="javascript:void(0)" class="edit-item-note"><span class="dashicons dashicons-edit"></span></a>	
					</div>
					<?php
					
				}
				break;
			case 'item_name':
				if($item) {
					if($item->item_id>0) {
						echo '<a href="'.esc_url(get_permalink( $item->item_id )).'" target="_blank">'.esc_html($item->item_name).'</a>';
					} else {
						echo esc_html($item->item_name);
					}
				}
				break;
			case 'item_image':
				if($item) {
					echo '<img src="'.esc_url($item->item_image).'" style="max-width:100%;max-height:100%;">';
				}
				break;
			case 'item_type_text':
				if($item) {
					echo esc_html($item->item_type_text);
				}
				break;
		}
	}

	public function admin_orders_custom_columns($columns) {
		$cb = $columns['cb'];
		$fullname = $columns['fullname'];
		$phone_number = $columns['phone_number'];
		unset($columns['cb']);
		unset($columns['fullname']);
		unset($columns['phone_number']);

		$new_columns = [
			'cb' => $cb,
			'fullname' => $fullname,
			'phone_number' => $phone_number,
			'item_source' => 'Nguồn',
			'ad' => 'Quảng cáo',
			'item_note' => 'Ghi chú',
			'item_name' => 'Công trình',
			'item_image' => 'Ảnh',
			'item_type_text' => 'Yêu cầu',
		];

		$columns = array_merge($new_columns, $columns);

		//$columns['device'] = 'Thiết bị';

		return $columns;
	}

	public function admin_menu() {
		$admin_page = add_menu_page( 'Đơn hàng', 'Đơn hàng', 'manage_options', self::ADMIN_SLUG, [$this, 'admin_order_page'], 'dashicons-cart', 22 );

		add_action( 'load-' . $admin_page, [$this, 'orders_load_admin'], 10, 0 );
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}

Admin_Order::instance();