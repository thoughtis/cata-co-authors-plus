<?php
/**
 * oEmbed
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.3.0
 */

namespace Cata\CoAuthors_Plus;

use WP_Post;

/**
 * oEmbed
 */
class oEmbed {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'oembed_response_data', array( __CLASS__, 'update_author_data' ), 10, 2 );
	}

	/**
	 * Update Author Data
	 * 
	 * @global CoAuthors_Plus $coauthors_plus
	 * @param array $data
	 * @param WP_Post $post
	 */
	public static function update_author_data( array $data, WP_Post $post ) : array {
		global $coauthors_plus;
		
		if ( ! in_array( $post->post_type, $coauthors_plus->supported_post_types, true ) ) {
			return $data;
		}

		$coauthors = get_coauthors( $post->ID );

		if ( ! is_array( $coauthors ) || empty( $coauthors ) ) {
			return $data;
		}

		$author = current( $coauthors );

		if ( ! isset( $author->ID ) || ! isset( $author->user_nicename ) ) {
			return $data;
		}

		return array_merge(
			$data,
			array(
				'author_name' => $author->display_name,
				'author_url'  => get_author_posts_url( $author->ID, $author->user_nicename ),
			)
		);
	}
}
