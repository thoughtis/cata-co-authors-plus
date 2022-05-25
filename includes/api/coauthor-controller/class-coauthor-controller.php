<?php
/**
 * CoAuthor Controller
 * 
 * @link https://developer.wordpress.org/reference/classes/wp_rest_terms_controller/
 * @package Cata\CoAuthors_Plus
 */

namespace Cata\CoAuthors_Plus\API;

use WP_REST_Terms_Controller;
use WP_Error;
use Cata\CoAuthors_Plus\API;

/**
 * CoAuthor Controller
 */
class CoAuthor_Controller extends WP_REST_Terms_Controller {

	/**
	 * Checks if a given request has access to read posts.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function get_items_permissions_check( $request ) {
 
		$ask_an_adult = parent::get_items_permissions_check( $request );

		if ( true !== $ask_an_adult ) {
			return $ask_an_adult;
		}

		/**
		 * Allow embed context since the sensitive data ( term description )
		 * is not included.
		 */
		if ( 'embed' === $request->get_param('context') ) {
			return true;
		}
 
		return API::current_user_has_cap_cap();
	}

	/**
	 * Checks if a given request has access to get a specific item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has read access for the item, WP_Error object otherwise.
	 */
	public function get_item_permissions_check( $request ) {

		$ask_an_adult = parent::get_item_permissions_check( $request );

		if ( true !== $ask_an_adult ) {
			return $ask_an_adult;
		}

		return API::current_user_has_cap_cap();
	}

}
