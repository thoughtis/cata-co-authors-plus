<?php
/**
 * RSS
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.2.1
 */

namespace Cata\CoAuthors_Plus;

/**
 * RSS
 */
class RSS {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'the_author', array( __CLASS__, 'use_coauthors' ), 100 );
	}

	/**
	 * Use CoAuthors
	 * 
	 * @link https://danielbachhuber.com/2011/12/13/co-authors-in-your-rss-feeds/
	 * @param string $author Default author name.
	 * @return string Updated author name(s).
	 */
	public static function use_coauthors( ?string $author ) : string {
		global $coauthors_plus;

		if ( ! is_feed() || ! function_exists( 'coauthors' ) ) {
			return $author;
		}

		if ( ! is_a( $coauthors_plus, 'CoAuthors_Plus' ) ) {
			return $author;
		}

		// Bail if the current post type does not support CoAuthors.
		if ( ! in_array( get_post_type( get_the_ID() ), $coauthors_plus->supported_post_types ) ) {
			return $author;
		}

		return coauthors( null, null, null, null, false );
	}
}
