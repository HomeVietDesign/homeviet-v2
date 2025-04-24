<?php
namespace HomeViet;

/**
 * 
 */
class Order
{
	private static $table = 'order_stats';
	private static $item_table = 'order_items';
	private static $customer_table = 'customer';

	private $id=0;

	private $customer_id=0;

	private $utm_source='';

	private $utm_medium='';

	private $url='';

	private $referrer='';

	private $user_agent='';

	private $date_created='';

	private $date_created_gmt='';

	private $order_type_text=[];

	function __construct($id=0)
	{
		global $wpdb;

		$this->order_type_text['normal'] = get_option('product_order_popup_title', '');
		$this->order_type_text['premium'] = get_option('product_order_premium_popup_title', '');
		$this->order_type_text['wpcf7'] = 'Đăng ký tư vấn qua form';

		if($id>0) {
			$order = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".self::get_table()." WHERE order_id = '%d'", $id) );
			if($order) {
				$this->id = (int) $order->order_id;
				$this->customer_id = (int) $order->customer_id;
				$this->utm_source = $order->utm_source;
				$this->utm_medium = $order->utm_medium;
				$this->url = $order->url;
				$this->referrer = $order->referrer;
				$this->user_agent = $order->user_agent;
				$this->date_created = $order->date_created;
				$this->date_created_gmt = $order->date_created_gmt;
			}
		}
	}

	public static function get_table() {
		global $table_prefix;
		return $table_prefix . self::$table;
	}

	public static function get_item_table() {
		global $table_prefix;
		return $table_prefix . self::$item_table;
	}

	public static function get_customer_table() {
		global $table_prefix;
		return $table_prefix . self::$customer_table;
	}

	public function save() {
		global $wpdb;

		$saved = false;

		if($this->id===0 && $this->customer_id>0) {

			$this->date_created = current_time( 'mysql' );
			$this->date_created_gmt = current_time( 'mysql', 1 );

			$inserted = $wpdb->insert(self::get_table(), ['customer_id'=>$this->customer_id, 'utm_source'=>$this->utm_source, 'utm_medium'=>$this->utm_medium, 'url'=>$this->url, 'referrer'=>$this->referrer, 'user_agent'=>$this->user_agent, 'date_created' => $this->date_created, 'date_created_gmt' => $this->date_created_gmt], ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);
			$saved = $inserted;
			if(false !== $inserted) {
				$this->id = (int) $wpdb->insert_id;
			}

		}

		return $saved;
	}

	// $data = ['name'=>'', 'image'=>'', 'type'=>'', 'id'=>0];
	public function insert_item($data) {
		global $wpdb;

		$inserted = false;

		if($this->id>0) {
			$inserted = $wpdb->insert(self::get_item_table(), ['order_id'=>$this->id, 'item_name'=>$data['name'], 'item_image'=>$data['image'], 'item_type'=>$data['type'], 'item_type_text'=>$this->order_type_text[$data['type']], 'item_note'=>'', 'item_id'=>$data['id']], ['%d', '%s', '%s', '%s', '%s', '%s', '%d']);
		}

		return $inserted;
	}

	public static function update_order_item($order_item_id, $data) {
		global $wpdb;
		if($data) {
			$wpdb->update(self::get_item_table(), $data, ['order_item_id'=>$order_item_id]);
		}
		return $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".self::get_item_table()." WHERE order_item_id = %d", $order_item_id) );
	}

	public static function delete($order_id) {
		global $wpdb;
		$wpdb->delete(self::get_table(), ['order_id'=>$order_id], ['%d']);
		$wpdb->delete(self::get_item_table(), ['order_id'=>$order_id], ['%d']);
		return true;
	}

	public function get_item() {
		global $wpdb;
		$result = null;
		if($this->id>0) {
			$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".self::get_item_table()." WHERE order_id = %d", $this->id) );
		}
		return $result;
	}

	public function get_customer() {
		global $wpdb;
		$result = null;
		if($this->id>0) {
			$result = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".self::get_customer_table()." WHERE customer_id = %d", $this->customer_id) );
		}
		return $result;
	}

	// set
	public function set_customer_id($customer_id) {
		$this->customer_id = (int) $customer_id;
	}

	public function set_utm_source($utm_source) {
		$this->utm_source = $utm_source;
	}

	public function set_utm_medium($utm_medium) {
		$this->utm_medium = $utm_medium;
	}

	public function set_url($url) {
		$this->url = $url;
	}

	public function set_referrer($referrer) {
		$this->referrer = $referrer;
	}

	public function set_user_agent($user_agent) {
		$this->user_agent = $user_agent;
	}

	// get
	public function get_id() {
		return (int) $this->id;
	}

	public function get_customer_id() {
		return (int) $this->customer_id;
	}

	public function get_utm_source() {
		return $this->utm_source;
	}

	public function get_utm_medium() {
		return $this->utm_medium;
	}

	public function get_url() {
		return $this->url;
	}

	public function get_referrer() {
		return $this->referrer;
	}

	public function get_user_agent() {
		return $this->user_agent;
	}

	public function get_date_created() {
		return $this->date_created;
	}

	public function get_date_created_gmt() {
		return $this->date_created_gmt;
	}

	public static function get_orders( $args ) {
		global $wpdb;

		$defaults = array(
			'orderby'        => 'date',
			'order'          => 'DESC',
			'limit'          => -1,
			'offset'          => 0,
			'include'        => '',
			'exclude'        => '',
			'search'         => '',
		);

		$parsed_args = wp_parse_args( $args, $defaults );

		$key   = md5( serialize( $parsed_args ) );
		$cache = wp_cache_get( 'get_orders', 'order' );

		if ( 'rand' !== $parsed_args['orderby'] && $cache ) {
			if ( is_array( $cache ) && isset( $cache[ $key ] ) ) {
				return $cache[ $key ];
			}
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$inclusions = '';
		if ( ! empty( $parsed_args['include'] ) ) {
			
			$incorders = wp_parse_id_list( $parsed_args['include'] );
			if ( count( $incorders ) ) {
				foreach ( $incorders as $incorder ) {
					if ( empty( $inclusions ) ) {
						$inclusions = ' AND ( order_id = ' . $incorder . ' ';
					} else {
						$inclusions .= ' OR order_id = ' . $incorder . ' ';
					}
				}
			}
		}
		if ( ! empty( $inclusions ) ) {
			$inclusions .= ')';
		}

		$exclusions = '';
		if ( ! empty( $parsed_args['exclude'] ) ) {
			$exorders = wp_parse_id_list( $parsed_args['exclude'] );
			if ( count( $exorders ) ) {
				foreach ( $exorders as $exorder ) {
					if ( empty( $exclusions ) ) {
						$exclusions = ' AND ( order_id <> ' . $exorder . ' ';
					} else {
						$exclusions .= ' AND order_id <> ' . $exorder . ' ';
					}
				}
			}
		}
		if ( ! empty( $exclusions ) ) {
			$exclusions .= ')';
		}

		$search = '';
		if ( ! empty( $parsed_args['search'] ) ) {
			$like   = '%' . $wpdb->esc_like( $parsed_args['search'] ) . '%';
			$search = $wpdb->prepare( ' AND ( (url LIKE %s) OR (fullname LIKE %s) OR (user_agent LIKE %s) OR (referrer LIKE %s) ) ', $like, $like, $like, $like );
		}

		$orderby = strtolower( $parsed_args['orderby'] );
		switch ( $orderby ) {
			case 'rand':
				$orderby = 'rand()';
				break;
			case 'id':
				$orderby = self::get_table().".order_id";
				break;
			case 'date':
				$orderby = self::get_table().".date_created";
				break;
			default:
				$orderparams = array();
				$keys        = array( 'fullname' );
				foreach ( explode( ',', $orderby ) as $ordparam ) {
					$ordparam = trim( $ordparam );

					if ( in_array( $ordparam, $keys, true ) ) {
						$orderparams[] = $ordparam;
					}
				}
				$orderby = implode( ',', $orderparams );
		}

		$order = strtoupper( $parsed_args['order'] );
		if ( '' !== $order && ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		$query  = "SELECT ".self::get_table().".*,".Customer::get_table().".fullname,".Customer::get_table().".phone_number FROM ".self::get_table()." INNER JOIN ".Customer::get_table()." ON(".self::get_table().".customer_id=".Customer::get_table().".customer_id) WHERE 1=1 ";
		$query .= " $exclusions $inclusions $search";
		$query .= " ORDER BY $orderby $order";
		if ( -1 != $parsed_args['limit'] ) {
			$query .= ' LIMIT ' . absint( $parsed_args['limit'] ).' OFFSET ' . absint($parsed_args['offset']);
		}
		//debug_log($query);
		$results = $wpdb->get_results( $query );

		if ( 'rand()' !== $orderby ) {
			$cache[ $key ] = $results;
			wp_cache_set( 'get_orders', $cache, 'order' );
		}

		return $results;
	}

	public static function get_count_orders( $args ) {
		global $wpdb;

		$defaults = array(
			'include'        => '',
			'exclude'        => '',
			'search'         => '',
		);

		$parsed_args = wp_parse_args( $args, $defaults );

		$key   = md5( serialize( $parsed_args ) );
		$cache = wp_cache_get( 'get_count_orders', 'order' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		$inclusions = '';
		if ( ! empty( $parsed_args['include'] ) ) {
			
			$incorders = wp_parse_id_list( $parsed_args['include'] );
			if ( count( $incorders ) ) {
				foreach ( $incorders as $incorder ) {
					if ( empty( $inclusions ) ) {
						$inclusions = ' AND ( order_id = ' . $incorder . ' ';
					} else {
						$inclusions .= ' OR order_id = ' . $incorder . ' ';
					}
				}
			}
		}
		if ( ! empty( $inclusions ) ) {
			$inclusions .= ')';
		}

		$exclusions = '';
		if ( ! empty( $parsed_args['exclude'] ) ) {
			$exorders = wp_parse_id_list( $parsed_args['exclude'] );
			if ( count( $exorders ) ) {
				foreach ( $exorders as $exorder ) {
					if ( empty( $exclusions ) ) {
						$exclusions = ' AND ( order_id <> ' . $exorder . ' ';
					} else {
						$exclusions .= ' AND order_id <> ' . $exorder . ' ';
					}
				}
			}
		}
		if ( ! empty( $exclusions ) ) {
			$exclusions .= ')';
		}

		$search = '';
		if ( ! empty( $parsed_args['search'] ) ) {
			$like   = '%' . $wpdb->esc_like( $parsed_args['search'] ) . '%';
			$search = $wpdb->prepare( ' AND ( (url LIKE %s) OR (fullname LIKE %s) OR (user_agent LIKE %s) OR (referrer LIKE %s) ) ', $like, $like, $like, $like );
		}

		$query  = "SELECT COUNT(order_id) FROM ".self::get_table()." INNER JOIN ".Customer::get_table()." ON(".self::get_table().".customer_id=".Customer::get_table().".customer_id) WHERE 1=1";

		$query .= " $exclusions $inclusions $search";

		$count = $wpdb->get_var( $query );

		$cache[ $key ] = $count;
		wp_cache_set( 'get_count_orders', $cache, 'order' );
		

		return $count;
	}
}