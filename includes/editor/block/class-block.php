<?php
/**
 * Block Editor
 * 
 * @package Cata\CoAuthors_Plus;
 */

namespace Cata\CoAuthors_Plus\Editor;

use WP_Post;

/**
 * Block
 */
class Block {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'use_block_editor_for_post_type', array(__CLASS__, 'dont_use_block_editor' ), 10, 2 );
		add_filter( 'use_block_editor_for_post', array( __CLASS__, 'use_block_editor' ), 10, 2 );
		add_filter( 'enqueue_block_editor_assets', array( __CLASS__, 'add_meta_box_action' ) );
		add_action( 'block_editor_meta_box_hidden_fields',  array( __CLASS__, 'add_hidden_nonce_meta_box' ) );
		add_filter( 'register_taxonomy_args', array(__CLASS__, 'update_author_taxonomy_args'), 10, 2 );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'add_block_editor_script' ) );
	}

	/**
	 * Don't Use Block Editor
	 * By default, use the classic editor.
	 *
	 * @param bool $use_block_editor
	 * @param string $post_type
	 */
	public static function dont_use_block_editor( bool $use_block_editor, string $post_type ) : bool {
		if ( 'guest-author' !== $post_type ) {
			return $use_block_editor;
		}
		return false;
	}

	/**
	 * Use Block Editor
	 * Use block editor for published guest authors with a slug / cap-user_login
	 *
	 * @param bool $use_block_editor
	 * @param WP_Post $post
	 * @return bool
	 */
	public static function use_block_editor( bool $use_block_editor, WP_Post $post) : bool {
		if ( 'guest-author' !== $post->post_type ) {
			return $use_block_editor;
		}
		if ( 'publish' !== $post->post_status ) {
			return $use_block_editor;
		}
		return self::is_non_empty_string(
			get_post_meta( $post->ID, 'cap-user_login', true )
		);
	}

	/**
	 * Is Non Empty String
	 * 
	 * @param $maybe_string
	 * @return bool
	 */
	private static function is_non_empty_string( $maybe_string ) : bool {
		return is_string( $maybe_string ) && ! empty( $maybe_string );
	}

	/**
	 * Add Meta Box Action
	 * When we're sure we're in the block editor, remove the extra save button from CAP.
	 */
	public static function add_meta_box_action() : void {
		// @priority 20 to override CoAuthors_Guest_Authors->action_add_meta_boxes().
		add_action( 'add_meta_boxes', array( __CLASS__, 'remove_save_meta_box' ), 20 );
	}

	/**
	 * Remove "Save" Meta Box
	 */
	public static function remove_save_meta_box() : void {
		remove_meta_box( 'coauthors-manage-guest-author-save', 'guest-author', 'side' );
	}

	/**
	 * Add Hidden Nonce Meta Box
	 *
	 * This is normally part of the callback for coauthors-manage-guest-author-save.
	 * We need the nonce field, but we don't need the rest.
	 *
	 * @param WP_Post $post
	 */
	public static function add_hidden_nonce_meta_box( WP_Post $post ) : void {
		if ( 'guest-author' !== $post->post_type ) {
			return;
		}
		wp_nonce_field( 'guest-author-nonce', 'guest-author-nonce' );
	}

	/**
	 * Update Author Taxonomy Args
	 * Make author publicy queryable so it works when used as a taxonomy filter in the query loop block.
	 * 
	 * @link https://core.trac.wordpress.org/browser/tags/6.1/src/wp-includes/blocks.php#L1317
	 * @param array $args
	 * @param string $taxonomy
	 * @return array
	 */
	public static function update_author_taxonomy_args( array $args, string $taxonomy ) : array {
		if ( 'author' !== $taxonomy ) {
			return $args;
		}
		return array_merge( $args, array( 'publicly_queryable' => true ) );
	}

	/**
	 * Add Block Editor Script
	 */
	public static function add_block_editor_script() : void {
	
		$screen = get_current_screen();

		if ( ! is_a( $screen, 'WP_Screen' ) || 'guest-author' !== $screen->id ) {
			return;
		}
	
		$asset_file = include cata_cap_get_plugin_directory_path() . '/build/index.asset.php';
		wp_enqueue_script(
			'cata-cap-guest-author-block-editor',
			cata_cap_get_plugin_directory_url() . 'build/index.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);
	}
}
