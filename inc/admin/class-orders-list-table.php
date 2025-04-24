<?php
namespace HomeViet;
/**
 * List Table API: Orders_List_Table class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 3.1.0
 */

/**
 * Core class used to implement displaying orders in a list table.
 *
 * @since 3.1.0
 *
 * @see WP_List_Table
 */
class Orders_List_Table extends \WP_List_Table {

	/**
	 * Constructor.
	 *
	 * @since 3.1.0
	 *
	 * @see WP_List_Table::__construct() for more information on default arguments.
	 *
	 * @param array $args An associative array of arguments.
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'order',
				'plural' => 'orders',
				'ajax' => false
			)
		);
	}

	/**
	 * @global string $s
	 * @global string $orderby
	 * @global string $order
	 */
	public function prepare_items() {
		global $action, $order_id, $orderby, $order, $s;

		wp_reset_vars( array( 'action', 'order_id', 'orderby', 'order', 's' ) );

		$per_page = $this->get_items_per_page( Admin_Order::ADMIN_SLUG.'_per_page' );

		$current_page = $this->get_pagenum();
		if ( 1 < $current_page ) {
			$offset = $per_page * ( $current_page - 1 );
		} else {
			$offset = 0;
		}

		$args = array(
			'limit' => $per_page,
			'offset' => $offset
		);

		if ( ! empty( $s ) ) {
			$args['search'] = $s;
		}
		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;
		}
		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}

        $this->items = Order::get_orders( $args );

        $count = Order::get_count_orders( $args );

        // Set the pagination
		$this->set_pagination_args( array(
			'total_items' => $count,
			'per_page' => $per_page,
			'total_pages' => ceil( $count / $per_page )
		) );
	}

	public static function define_columns() {
		$columns = array(
			'cb'         => '<input type="checkbox" />',
			'fullname'       => 'Tên khách hàng',
			'phone_number'        => 'Số điện thoại',
			// 'url'        => 'URL',
			// 'referrer'        => 'Referer',
			// 'user_agent'        => 'UA',
			'date_created'        => 'Ngày giờ',
		);

		return $columns;
	}

	/**
	 */
	public function no_items() {
		_e( 'No orders found.' );
	}

	/**
	 * @return array
	 */
	protected function get_bulk_actions() {
		$actions           = array();
		$actions['delete'] = __( 'Delete' );

		return $actions;
	}

	/**
	 * @global int $cat_id
	 * @param string $which
	 */
	protected function extra_tablenav( $which ) {
		//global $cat_id;

		if ( 'top' !== $which ) {
			return;
		}
		?>
		<div class="alignleft actions">
			<?php
			/*
			$dropdown_options = array(
				'selected'        => $cat_id,
				'name'            => 'cat_id',
				'taxonomy'        => 'order_category',
				'show_option_all' => get_taxonomy( 'order_category' )->labels->all_items,
				'hide_empty'      => true,
				'hierarchical'    => 1,
				'show_count'      => 0,
				'orderby'         => 'name',
			);

			echo '<label class="screen-reader-text" for="cat_id">' . get_taxonomy( 'order_category' )->labels->filter_by_item . '</label>';

			wp_dropdown_categories( $dropdown_options );

			submit_button( __( 'Filter' ), '', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
			*/
			?>
		</div>
		<?php
	}

	/**
	 * @return array
	 */
	public function get_columns() {
		return get_column_headers( get_current_screen() );
	}

	/**
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'fullname'    => ['fullname', true],
			'date_created'    => ['date_created', true],
		);
	}

	/**
	 * Get the name of the default primary column.
	 *
	 * @since 4.3.0
	 *
	 * @return string Name of the default primary column, in this case, 'name'.
	 */
	protected function get_default_primary_column_name() {
		return 'fullname';
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.3.0
	 * @since 5.9.0 Renamed `$order` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param object $item The current order object.
	 */
	public function column_cb( $item ) {
		// Restores the more descriptive, specific name for use within this method.
		$order = $item;

		?>
		<label class="screen-reader-text" for="cb-select-<?php echo $order->order_id; ?>">
			<?php
			/* translators: Hidden accessibility text. %s: Link name. */
			printf( __( 'Select %s' ), $order->customer_id );
			?>
		</label>
		<input type="checkbox" name="ordercheck[]" id="cb-select-<?php echo $order->order_id; ?>" value="<?php echo esc_attr( $order->order_id ); ?>" />
		<?php
	}

	/**
	 * Handles the order name column output.
	 *
	 * @since 4.3.0
	 *
	 * @param object $order The current order object.
	 */
	public function column_username( $order ) {
		$view_order = $this->get_view_order( $order );
		printf(
			'<strong><a class="row-title" href="%s" aria-label="%s">%s</a></strong>',
			$view_order,
			esc_attr( sprintf( 'Chi tiết &#8220;%s&#8221;', $order->fullname ) ),
			$order->fullname
		);
	}

	public function column_default( $order, $column_name ) {
		switch( $column_name ) {
			case 'fullname':
				echo esc_html($order->fullname);
				break;
			case 'phone_number':
				echo esc_html($order->phone_number);
				break;
			// case 'url':
			// 	echo esc_html($order->url);
			// 	break;
			// case 'referrer':
			// 	echo esc_html($order->referrer);
			// 	break;
			// case 'user_agent':
			// 	echo esc_html($order->user_agent);
			// 	break;
			case 'date_created':
				echo esc_html($order->date_created);
				break;
			default:
				do_action( 'manage_orders_custom_column', $column_name, $order );
				break;
		}

	}

	public function display_rows() {
		foreach ( $this->items as $order ) {
			$order->fullname     = esc_attr( $order->fullname );;
			?>
		<tr id="order-<?php echo $order->order_id; ?>">
			<?php $this->single_row_columns( $order ); ?>
		</tr>
			<?php
		}
	}

	/**
	 * Generates and displays row action orders.
	 *
	 * @since 4.3.0
	 * @since 5.9.0 Renamed `$order` to `$item` to match parent class for PHP 8 named parameter support.
	 *
	 * @param object $item        Link being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for orders, or an empty string
	 *                if the current column is not the primary column.
	 */
	protected function handle_row_actions( $item, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		// Restores the more descriptive, specific name for use within this method.
		$order      = $item;
		$view_order = $this->get_view_order( $order );

		$actions           = array();
		$actions['view']   = '<a href="' . $view_order . '" class="thickbox">Xem chi tiết</a>';
		$actions['delete'] = sprintf(
			'<a class="submitdelete" href="%s" onclick="return confirm( \'%s\' );">%s</a>',
			wp_nonce_url( "admin.php?page=admin_order&amp;action=delete&amp;order_id=$order->order_id", 'delete-order_' . $order->order_id ),
			/* translators: %s: Link name. */
			esc_js( sprintf( __( "You are about to delete this order '%s'\n  'Cancel' to stop, 'OK' to delete." ), $order->fullname ) ),
			__( 'Delete' )
		);

		return $this->row_actions( $actions );
	}

	protected function get_view_order($order) {
		$nonce = wp_create_nonce( 'detail-order-'.$order->order_id );
		return admin_url( 'admin-ajax.php?action=order_detail&id='.$order->order_id.'&nonce='.$nonce.'&TB_iframe=true&width=800&height=600' );
	}
}
