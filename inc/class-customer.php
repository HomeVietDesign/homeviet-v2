<?php
namespace HomeViet;

/**
 * 
 */
class Customer
{	
	private static $table = 'customer';

	private $id=0;

	private $fullname='';

	private $phone_number='';

	private $date_created='';

	private $date_created_gmt='';

	function __construct($phone_number='', $fullname='')
	{	
		global $wpdb;

		$this->phone_number = $phone_number;
		$this->fullname = $fullname;

		if($phone_number!='') {
			$customer = $wpdb->get_row( $wpdb->prepare("SELECT * FROM ".self::get_table()." WHERE phone_number='%s'", $phone_number) );
			if($customer) {
				$this->id = (int) $customer->customer_id;
				
				if($this->fullname=='') {
					$this->fullname = $customer->fullname;
				}
				
				$this->date_created = $customer->date_created;
				$this->date_created_gmt = $customer->date_created_gmt;
			}
		}
	}

	public static function get_instance($id) {
		global $wpdb;
		$phone_number = $wpdb->get_var( $wpdb->prepare("SELECT phone_number FROM ".self::get_table()." WHERE customer_id=%d", $id) );
		return new self($phone_number);
	}

	public static function get_table() {
		global $table_prefix;
		return $table_prefix . self::$table;
	}

	public function save() {
		global $wpdb;

		$saved = false;

		if($this->fullname!='') {

			if($this->id) {
				$updated = $wpdb->update( self::get_table(), ['fullname'=>$this->fullname], ['customer_id'=>$this->id], ['%s'], ['%d'] );
				if(false !== $updated) {
					$saved = true;

				}
			} elseif ($this->phone_number!='') {

				$this->date_created = current_time( 'mysql' );
				$this->date_created_gmt = current_time( 'mysql', 1 );

				$inserted = $wpdb->insert(self::get_table(), ['phone_number'=>$this->phone_number, 'fullname'=>$this->fullname, 'date_created' => $this->date_created, 'date_created_gmt' => $this->date_created_gmt], ['%s', '%s', '%s', '%s']);
				$saved = $inserted;
				if(false !== $inserted) {
					$this->id = (int) $wpdb->insert_id;
				}
			}
		}

		return $saved;
	}

	public function set_fullname($fullname) {
		$this->fullname = $fullname;
	}

	public function get_id() {
		return (int) $this->id;
	}

	public function get_phone_number() {
		return $this->phone_number;
	}

	public function get_fullname() {
		return $this->fullname;
	}

	public function get_date_created() {
		return $this->date_created;
	}

	public function get_date_created_gmt() {
		return $this->date_created_gmt;
	}
}