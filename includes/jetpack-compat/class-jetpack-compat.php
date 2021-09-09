<?php
/**
 * Jetpack Compat
 * 
 * @package Cata\CoAuthors_Plus
 */

namespace Cata\CoAuthors_Plus;

/**
 * Jetpack Compat
 */
class Jetpack_Compat {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'jetpack_sitemap_image_skip_post', array( __CLASS__, 'image_sitemap_skip_avatars' ), 10, 2 );
	}

	/**
	 * Image Sitemap Skip Avatars
	 * In the images sitemap, avatars produce URLs like `/?post_type=guest-author&p=7947` for their parent post.
	 * These lead to 404s and search indexing errors in Google Search Console.
	 *
	 * @param bool             $skip Whether to skip this entry in the sitemap.
	 * @param stdClass|WP_Post $post Post we're examining - can be stdClass for some reason.
	 * @return bool $skip Whether to skip this entry in the sitemap.
	 */
	public static function image_sitemap_skip_avatars( bool $skip, $post ) : bool {
		if ( ! is_object( $post ) || ! property_exists( $post, 'post_parent' ) ) {
			return $skip;
		}

		$parent_id = absint( $post->post_parent );

		if ( 0 === $parent_id ) {
			return $skip;
		}

		if ( 'guest-author' === get_post_type( $parent_id ) ) {
			return true;
		}

		return $skip;
	}
}
