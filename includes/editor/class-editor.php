<?php
/**
 * Editor
 * 
 * @package Cata\CoAuthors_Plus;
 */

namespace Cata\CoAuthors_Plus;

/**
 * Editor
 */
class Editor {
	/**
	 * Construct
	 */
	public function __construct() {
		// @priority 20 because new CoAuthors_Guest_Authors() happens at default priority.
		add_action( 'init', array( __CLASS__, 'add_editor_support' ), 20 );
	}

	/**
	 * Add Editor Support
	 */
	public static function add_editor_support() : void {
		add_post_type_support( 'guest-author', 'editor' );
	}
}
