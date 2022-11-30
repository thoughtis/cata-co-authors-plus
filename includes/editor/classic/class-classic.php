<?php
/**
 * Classic Editor
 * 
 * @package Cata\CoAuthors_Plus;
 */

namespace Cata\CoAuthors_Plus\Editor;

/**
 * Classic
 */
class Classic {
	/**
	 * Construct
	 */
	public function __construct() {
		add_action( 'edit_form_top', array( __CLASS__, 'remove_editor_support' ) );
	}

	/**
	 * Remove Title Support
	 * We want editor support, but only for the block editor.
	 */
	public static function remove_editor_support() : void {
		remove_post_type_support( 'guest-author', 'editor' );
	}
}
