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
	if ( ! isset( $object->{$property} ) ) {
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
 * Has Queried CoAuthor
 * 
 * @return bool Whether the coauthor referred to be the query var `author_name` was found.
 */
function cata_cap_has_queried_coauthor() : bool {
	$author = get_queried_object();
	return cata_cap_is_guest_author( $author ) || is_a( $author, 'WP_User' );
}

/**
 * Get The CoAuthor Meta
 * 
 * @param string                 $property CoAuthor data we want.
 * @param stdClass|WP_User|false $author CoAuthor that is either a user or Guest Author.
 * @param mixed                  $default Default if that property is not set.
 * @return mixed Property or default.
 */
function cata_cap_get_the_coauthor_meta( string $property, $author, $default = '' ) {
	return cata_cap_safe_get_object_property( $property, $author, $default );
}

/**
 * Get Guest Author Post Content
 *
 * @param stdClass $author Guest Author
 */
function cata_cap_get_guest_author_post_content( stdClass $author ) : string {
	$guest_author_post = get_post( $author->ID );
	if ( ! is_a( $guest_author_post, 'WP_Post' ) || 'guest-author' !== $guest_author_post->post_type ) {
		return '';
	}
	return $guest_author_post->post_content;
}

/**
 * CoAuthor has Post Content
 *
 * @param stdClass|WP_User $author CoAuthor who might be a Guest Author.
 */
function cata_cap_coauthor_has_post_content( $author ) : bool {
	return cata_cap_is_guest_author( $author ) && '' !== cata_cap_get_guest_author_post_content( $author );
}

/**
 * Queried CoAuthor has Post Content
 *
 * @param stdClass|WP_User $author CoAuthor who might be a Guest Author.
 */
function cata_cap_queried_coauthor_has_post_content() : bool {
	return cata_cap_coauthor_has_post_content( get_queried_object() );
}
