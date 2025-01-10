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
		add_action( 'admin_init', array( __CLASS__, 'dont_show_posts_list_table' ) );
	}

	/**
	 * Add Editor Support
	 */
	public static function add_editor_support() : void {
		add_post_type_support( 'guest-author', 'editor' );
		add_post_type_support( 'guest-author', 'autosave' );
	}

	/**
	 * Don't Show Posts List Table
	 *
	 * @description Redirect away from `/wp-admin/edit.php?post_type=guest-author`
	 * to `/wp-admin/users.php?page=view-guest-authors`
	 * @global string $pagenow
	 * @global string $typenow
	 */
	public static function dont_show_posts_list_table() : void {
		global $pagenow, $typenow;
		if ( 'edit.php' !== $pagenow ) {
			return;
		}
		if ( 'guest-author' !== $typenow ) {
			return;
		}
		wp_redirect(
			admin_url( 'users.php?page=view-guest-authors' )
		);
	}
}
