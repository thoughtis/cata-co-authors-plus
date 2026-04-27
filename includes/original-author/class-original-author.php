<?php
/**
 * Original Author
 *
 * @package Cata\CoAuthors_Plus
 */

namespace Cata\CoAuthors_Plus;

/**
 * Original Author
 */
class Original_Author {
	/**
	 * Construct
	 */
	public function __construct() {
		add_action( 'wp_after_insert_post', array( __CLASS__, 'snapshot_on_creation' ), 10, 3 );
		add_action( 'init', array( __CLASS__, 'register_original_author_meta' ) );
		add_action( 'rest_api_init', array( __CLASS__, 'register_rest_field' ) );
	}

	/**
	 * Snapshot On Creation
	 * Captures the original post_author into _original_author meta on first save only.
	 *
	 * @param int      $post_id
	 * @param \WP_Post $post
	 * @param bool     $update
	 */
	public static function snapshot_on_creation( int $post_id, \WP_Post $post, bool $update ) : void {
		if ( $update ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( get_post_meta( $post_id, '_original_author', true ) ) {
			return;
		}
		if ( $post->post_author ) {
			update_post_meta( $post_id, '_original_author', (int) $post->post_author );
		}
	}

	/**
	 * Register Original Author Meta
	 */
	public static function register_original_author_meta() : void {
		register_post_meta(
			'post',
			'_original_author',
			array(
				'auth_callback'     => '__return_false',
				'sanitize_callback' => 'absint',
				'single'            => true,
				'show_in_rest'      => false,
				'type'              => 'integer',
			)
		);
	}

	/**
	 * Register REST Field
	 * Exposes original_author_name on /wp/v2/posts responses.
	 */
	public static function register_rest_field() : void {
		register_rest_field(
			'post',
			'original_author_name',
			array(
				'get_callback' => array( __CLASS__, 'get_original_author_name' ),
				'schema'       => array(
					'description' => 'Display name of the user who originally created this post.',
					'type'        => 'string',
					'context'     => array( 'view', 'edit' ),
				),
			)
		);
	}

	/**
	 * Get Original Author Name
	 *
	 * @param array $obj REST object data.
	 * @return string|null
	 */
	public static function get_original_author_name( array $obj ) : ?string {
		$uid  = (int) get_post_meta( $obj['id'], '_original_author', true );
		$user = $uid ? get_userdata( $uid ) : null;
		return $user ? $user->display_name : null;
	}
}
