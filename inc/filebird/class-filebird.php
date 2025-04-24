<?php
namespace HomeViet;

use FileBird\Classes\Core as FBCore;
use FileBird\Model\Folder as FolderModel;
use FileBird\Classes\Tree as FolderTree;

class Filebird {

	private static $instance = null;

	private $folders = [];


	private function __construct() {
		if(is_admin()) {
			$fbv_folders = [];

			$_folders = FolderTree::getFolders();
			// debug_log($_folders);
			// foreach ($_folders as $folder) {
			// 	$fbv_folders[$folder['id']] = $folder['text'];
			// }
			self::treeFolders($fbv_folders, $_folders);
			//debug_log($fbv_folders);
			$this->folders = $fbv_folders;

			//add_action('wp_body_open', [$this, 'test']);

			remove_action( 'attachment_fields_to_edit', array( FBCore::getInstance(), 'attachment_fields_to_edit' ), 10, 2 );
			add_action( 'attachment_fields_to_edit', array( $this, 'attachment_fields_to_edit' ), 10, 2 );

		}
	}

	public static function treeFolders(&$folders, $_folders, $level='') {
		if(!empty($_folders)) {
			foreach ($_folders as $folder) {
				$folders[$folder['id']] = $level.$folder['text'];
				if(!empty($folder['children'])) {
					self::treeFolders($folders, $folder['children'], $level.'-');
				}
			}
		}
	}

	public function attachment_fields_to_edit( $form_fields, $post ) {

		$screen = null;
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();

			if ( ! is_null( $screen ) && 'attachment' === $screen->id ) {
				return $form_fields;
			}
		}

		
		$fbv_folder         = FolderModel::getFolderFromPostId( $post->ID ); // current folder

		$fbv_folder         = count( $fbv_folder ) > 0 ? $fbv_folder[0] : (object) array(
			'folder_id' => 0,
			'name'      => __( 'Uncategorized', 'filebird' ),
		);
		$folder_name = esc_attr( $fbv_folder->name );
		$folder_id   = (int) $fbv_folder->folder_id;
		$post_id     = (int) $post->ID;

		$dropdown_html = '<div class="fbv-attachment-edit-wrapper attachment-edit-wrapper"><select id="attachments-'.$post_id.'-fbv_folder" name="attachments['.$post_id.'][fbv]">';

		$dropdown_html .= '<option value="0" '.selected( 0, $folder_id, false ).'>'.__( 'Uncategorized', 'filebird' ).'</option>';

		if($this->folders) {
			foreach ($this->folders as $id => $name) {
				$dropdown_html .= '<option value="'.$id.'" '.selected( $id, $folder_id, false ).'>'.esc_html( $name ).'</option>';
			}
		}

		$dropdown_html .= '</select></div>';

		$form_fields['fbv'] = array(
			'html'  => $dropdown_html,
			'label' => esc_html__( 'FileBird folder:', 'filebird' ),
			'helps' => esc_html__( 'Click on the button to move this file to another folder', 'filebird' ),
			'input' => 'html',
		);

		return $form_fields;
	}

	public function test() {
		?>
		<h1>test</h1>
		<?php
	}

	public static function instance() {
		if(empty(self::$instance))
			self::$instance = new self;

		return self::$instance;
	}
}
Filebird::instance();