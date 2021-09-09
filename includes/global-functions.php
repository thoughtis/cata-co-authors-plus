<?php
/**
 * Global Functions
 * Global refers to the namespace.
 * These might also be referred to as template-tags and template-functions.
 * They should all be prefixed with `cata_cap_`
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.1.4
 */

/**
 * Safe Get Object Property
 * 
 * @param string $property Property to retrieve.
 * @param mixed  $object An object, we hope.
 * @param mixed  $default Which defaults to null.
 * @return mixed Object property or default.
 */
function cata_cap_safe_get_object_property( string $property, $object, $default = null ) {
	if ( ! is_object( $object ) ) {
		return $default;
	}
	if ( ! property_exists( $object, $property ) ) {
		return $default;
	}
	return $object->{$property};
}

/**
 * Is Guest Author?
 * 
 * @param stdClass|WP_User $author CoAuthor who might be a Guest Author.
 * @return bool Whether this is a Guest Author rather than WP_User.
 */
function cata_cap_is_guest_author( $author ) : bool {
	return 'guest-author' === cata_cap_safe_get_object_property( 'type', $author, '' );
}

/**
 * Get Global Guest Author
 * 
 * @global CoAuthors_Plus $coauthors_plus Instance of CoAuthors_Plus.
 * @return stdClass|WP_User|false Result of `$coauthors_plus->get_coauthor_by`
 */
function cata_cap_get_global_guest_author() {
	global $coauthors_plus;
	return $coauthors_plus->get_coauthor_by(
		'user_nicename',
		sanitize_user( get_query_var( 'author_name' ) )
	);
}

/**
 * Has Global Guest Author
 * 
 * @return bool Whether the author referred to be the query var `author_name` is a Guest Author.
 */
function cata_cap_has_global_guest_author() : bool {
	return cata_cap_is_guest_author(
		cata_cap_get_global_guest_author()
	);
}

/**
 * Get The CoAuthor Meta
 * Wrap get_the_coauthor_meta that comes with CAP to return a useable value,
 * not an array of values.
 * 
 * @param string                 $property CoAuthor data we want.
 * @param stdClass|WP_User|false $author CoAuthor that is either a user or Guest Author.
 * @param mixed                  $default Default if that property is not set.
 * @return mixed Property or default.
 */
function cata_cap_get_the_coauthor_meta( string $property, $author, $default = '' ) {
	$cap_meta = get_the_coauthor_meta( $property, $author->ID );
	if ( ! isset( $cap_meta[$author->ID] ) || empty( $cap_meta[$author->ID] ) ) {
		return $default;
	}
	return $cap_meta[$author->ID];
}
