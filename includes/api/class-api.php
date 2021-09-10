<?php
/**
 * API
 * 
 * @package Cata\CoAuthors_Plus
 */

namespace Cata\CoAuthors_Plus;

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
	 * @param array $args
	 * @param string $taxonomy
	 */
	public static function update_taxonomy_args( array $args, string $taxonomy ) {
		global $coauthors_plus;
		if ( $coauthors_plus->coauthor_taxonomy !== $taxonomy ) {
			return $args;
		}
		return array_merge(
			$args,
			array(
				'show_in_rest' => true,
			)
		);
	}

	/**
	 * Update Post Type Args
	 * 
	 * @param array $args
	 * @param string $post_type
	 */
	public static function update_post_type_args( array $args, string $post_type ) : array {
		// Tried to get this from CoAuthors_Guest_Authors,
		// but couldn't access it through global $coauthors_plus
		if ( 'guest-author' !== $post_type ) {
			return $args;
		}
		return array_merge(
			$args,
			array(
				'show_in_rest' => true,
				'supports'     => array_merge(
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
			'cap-twitter'
		);

		foreach ( $meta_keys as $meta_key ) {
			register_post_meta(
				'guest-author',
				$meta_key,
				array(
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
	 * but we don't actually want it to be editing directly in the post editor.
	 */
	public static function remove_cutstom_fields_box() : void {
		remove_meta_box( 'postcustom', 'guest-author', 'normal' );
	}

	/**
	 * @link https://gist.github.com/maheshwaghmare/0bbe5eabceed24aa76ef1eabe684a748
	 */
	public static function post_meta_request_params( $args, $request ) {
		
		$args = array_merge(
			$args,
			array(
				'meta_key'   => $request['meta_key'],
				'meta_value' => $request['meta_value'],
			)
		);

		return $args;
	}
}
