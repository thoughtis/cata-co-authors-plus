<?php
/**
 * API
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.2.2
 */

namespace Cata\CoAuthors_Plus;

use WP_REST_Request;

/**
 * API
 */
class API {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'register_taxonomy_args', array( __CLASS__, 'update_taxonomy_args' ), 10, 2 );
		add_filter( 'register_post_type_args', array( __CLASS__, 'update_post_type_args' ), 10, 2 );
		add_action( 'init', array( __CLASS__, 'register_guest_author_meta' ) );
		add_action( 'do_meta_boxes', array( __CLASS__, 'remove_cutstom_fields_box' ), 10, 0 );
		add_filter( 'rest_guest-author_query', array( __CLASS__, 'post_meta_request_params' ), 10, 2 );
	}

	/**
	 * Update Taxonomy Args
	 * 
	 * @param array  $args Provided taxonomy registration args.
	 * @param string $taxonomy The name of the taxonomy being registered.
	 * @return array Updated args for REST API inclusion.
	 */
	public static function update_taxonomy_args( array $args, string $taxonomy ) : array {
		global $coauthors_plus;
		if ( $coauthors_plus->coauthor_taxonomy !== $taxonomy ) {
			return $args;
		}
		return array_merge(
			$args,
			array(
				// Needs its own REST base because 'author' is taken.
				'rest_base'             => 'coauthor',
				'rest_controller_class' => 'Cata\\CoAuthors_Plus\\API\\CoAuthor_Controller',
				'show_in_rest'          => true,
			)
		);
	}

	/**
	 * Update Post Type Args
	 * 
	 * @param array  $args Provided post type registration args.
	 * @param string $post_type The post type being registered.
	 * @return array Args updated for REST API inclusion.
	 */
	public static function update_post_type_args( array $args, string $post_type ) : array {
		// Tried to get this from CoAuthors_Guest_Authors,
		// but couldn't access it through global $coauthors_plus.
		if ( 'guest-author' !== $post_type ) {
			return $args;
		}
		return array_merge(
			$args,
			array(
				'rest_controller_class' => 'Cata\\CoAuthors_Plus\\API\\Guest_Author_Controller',
				'show_in_rest'          => true,
				'supports'              => array_merge(
					$args['supports'],
					array( 'custom-fields' )
				),
			)
		);
	}

	/**
	 * Register Guest Author Meta
	 */
	public static function register_guest_author_meta() : void {

		$meta_keys = array(
			'cap-display_name',
			'cap-first_name',
			'cap-last_name',
			'cap-user_email',
			'cap-user_login',
			'cap-website',
			'cap-description',
			'cap-instagram',
			'cap-tiktok',
			'cap-twitter',
		);

		foreach ( $meta_keys as $meta_key ) {
			register_post_meta(
				'guest-author',
				$meta_key,
				array(
					// Auth prevents unauthorized editing and deleting, not reading.
					'auth_callback'     => array( __CLASS__, 'current_user_has_cap_cap' ),
					'sanitize_callback' => 'sanitize_text_field',
					'single'            => true,
					'show_in_rest'      => true,
					'type'              => 'string',
				)
			);
		}
	}

	/**
	 * Remove Custom Fields Box
	 * Custom Fields must be enabled to get meta data into the API,
	 * but we don't actually want it to be edited directly in the post editor.
	 */
	public static function remove_cutstom_fields_box() : void {
		remove_meta_box( 'postcustom', 'guest-author', 'normal' );
	}

	/**
	 * Current User Has CAP Capability
	 * 
	 * @global CoAuthors_Plus $coauthors_plus
	 * @return bool Whether current user has the capability to edit Guest Authors.
	 */
	public static function current_user_has_cap_cap() : bool {
		global $coauthors_plus;
		// No CAP so how could they have cap?
		if ( ! is_a( $coauthors_plus, 'CoAuthors_Plus' ) || ! isset( $coauthors_plus->guest_authors ) ) {
			return false;
		}
		if ( ! isset( $coauthors_plus->guest_authors->list_guest_authors_cap ) ) {
			return false;
		}
		return current_user_can( $coauthors_plus->guest_authors->list_guest_authors_cap );
	}

	/**
	 * Post Meta Request Params
	 * Allow filtering Guest Authors by email for authorized users.
	 * 
	 * @link https://developer.wordpress.org/reference/hooks/rest_this-post_type_query/
	 * @link https://gist.github.com/maheshwaghmare/0bbe5eabceed24aa76ef1eabe684a748
	 * @param array           $args Initial query args.
	 * @param WP_REST_Request $request API request being handled.
	 * @return array $args Updated query args.
	 */
	public static function post_meta_request_params( array $args, WP_REST_Request $request ) {
		// Only allowed for users who can edit Guest Authors.
		// This is a redundant auth check, in case the read capability ever changes.
		if ( ! self::current_user_has_cap_cap() ) {
			return $args;
		}
		// Only cap-user_email is supported.
		if ( ! isset( $request['meta_key'] ) || 'cap-user_email' !== $request['meta_key'] ) {
			return $args;
		}
		return array_merge(
			$args,
			array(
				'meta_key'   => $request['meta_key'],
				// @phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
				'meta_value' => $request['meta_value'],
			)
		);
	}
}
