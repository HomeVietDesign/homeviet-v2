<?php
namespace HomeViet;

final class Unyson {

	private static $instance = null;

	private function __construct() {
		add_action('fw_init', [$this, '_action_theme_remove_default_option_types']);
		add_action('fw_option_types_init', [$this, '_action_theme_include_custom_option_types']);
		
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ] );
	}

	public function on_after_setup_theme() {
		if ($this->is_compatible()) {
			add_action('wp_loaded', [$this, 'remove_try_brizy_notice']);
			add_filter('fw_use_sessions', '__return_false');
		}
	}

	public function _action_theme_include_custom_option_types() {
		require_once THEME_DIR.'/framework-customizations/option-types/code-editor/class-fw-option-type-code-editor.php';
		require_once THEME_DIR.'/framework-customizations/option-types/numeric/class-fw-option-type-numeric.php';

		\FW_Option_Type::register( 'FW_Option_Type_Hidden' );
		\FW_Option_Type::register( 'FW_Option_Type_Text' );
		\FW_Option_Type::register( 'FW_Option_Type_Short_Text' );
        \FW_Option_Type::register( 'FW_Option_Type_Number' );
		\FW_Option_Type::register( 'FW_Option_Type_Password' );
		\FW_Option_Type::register( 'FW_Option_Type_Textarea' );
		\FW_Option_Type::register( 'FW_Option_Type_Html' );
		\FW_Option_Type::register( 'FW_Option_Type_Html_Fixed' );
		\FW_Option_Type::register( 'FW_Option_Type_Html_Full' );
		\FW_Option_Type::register( 'FW_Option_Type_Checkbox' );
		\FW_Option_Type::register( 'FW_Option_Type_Checkboxes' );
		\FW_Option_Type::register( 'FW_Option_Type_Radio' );
		\FW_Option_Type::register( 'FW_Option_Type_Select' );
		\FW_Option_Type::register( 'FW_Option_Type_Short_Select' );
		\FW_Option_Type::register( 'FW_Option_Type_Select_Multiple' );
		\FW_Option_Type::register( 'FW_Option_Type_Unique' );
		\FW_Option_Type::register( 'FW_Option_Type_Addable_Box' );
		\FW_Option_Type::register( 'FW_Option_Type_Addable_Option' );
		\FW_Option_Type::register( 'FW_Option_Type_Addable_Popup' );
		\FW_Option_Type::register( 'FW_Option_Type_Addable_Popup_Full' );
		\FW_Option_Type::register( 'FW_Option_Type_Background_Image' );
		\FW_Option_Type::register( 'FW_Option_Type_Color_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Date_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Datetime_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Datetime_Range' );
		\FW_Option_Type::register( 'FW_Option_Type_Gradient' );
		\FW_Option_Type::register( 'FW_Option_Type_Icon' );
		\FW_Option_Type::register( 'FW_Option_Type_Image_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Multi' );
		\FW_Option_Type::register( 'FW_Option_Type_Multi_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Multi_Upload' );
		\FW_Option_Type::register( 'FW_Option_Type_Popup' );
		\FW_Option_Type::register( 'FW_Option_Type_Radio_Text' );
		\FW_Option_Type::register( 'FW_Option_Type_Range_Slider' );
		\FW_Option_Type::register( 'FW_Option_Type_Rgba_Color_Picker' );
		\FW_Option_Type::register( 'FW_Option_Type_Slider' );
		\FW_Option_Type::register( 'FW_Option_Type_Slider_Short' );
		\FW_Option_Type::register( 'FW_Option_Type_Switch' );
		\FW_Option_Type::register( 'FW_Option_Type_Typography' );
		\FW_Option_Type::register( 'FW_Option_Type_Typography_v2' );
		\FW_Option_Type::register( 'FW_Option_Type_Upload' );
		\FW_Option_Type::register( 'FW_Option_Type_Wp_Editor' );

		{
			\FW_Option_Type::register( 'FW_Option_Type_Multi_Select' );
		}

		{
			\FW_Option_Type::register( 'FW_Option_Type_Oembed' );
		}
	}

	public function _action_theme_remove_default_option_types() {
		remove_action( 'fw_option_types_init', '_action_fw_init_option_types' );
	}

	public function remove_try_brizy_notice() {
		remove_action('admin_notices', [fw()->theme, '_action_admin_notices']);
	}

	public function is_compatible() {

		// Check if Unyson installed and activated
		if ( ! unyson_exists() ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return false;
		}

		return true;

	}

	public function admin_notice_missing_main_plugin() {

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'homeviet' ),
			'<strong>' . esc_html__( 'Unyson Test Extension', 'homeviet' ) . '</strong>',
			'<strong>' . esc_html__( 'Unyson', 'homeviet' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}

}

Unyson::instance();