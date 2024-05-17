<?php
/**
 * Authors - Global Functions
 *
 * @package Cata\CoAuthors_Plus
 * @since
 */

/**
 * Get Social Services
 * 
 * @return array $services_with_url_to_fill
 */
function cata_cap_get_social_services() : array {

	$default_services =  array(
		array(
			'service' => 'instagram',
			'pattern' => 'https://www.instagram.com/%s/',
			'field'   => 'instagram',
		),
		array(
			'service' => 'tiktok',
			'pattern' => 'https://www.tiktok.com/@%s',
			'field'   => 'tiktok',
		),
		array(
			'service' => 'twitter',
			'pattern' => 'https://twitter.com/%s',
			'field'   => 'twitter',
		),
		array(
			'service' => 'chain',
			'pattern' => '%s',
			'field'   => 'website',
		)
	);

	$services = apply_filters(
		'cata_cap_get_social_services', $default_services
	);

	$services_with_url_to_fill = array_map(
		fn( array $s ): array => [...$s, 'url' => '' ],
		$services
	);

	return $services_with_url_to_fill;
}

/**
 * Apply Author to Social Services
 * 
 * @param array            $services
 * @param stdClass|WP_User $author
 * @return array
 */
function cata_cap_apply_author_to_social_services( array $services, stdClass|WP_User $author ) : array {
	
	$services_with_urls = array_map(
		'cata_cap_apply_author_to_social_service',
		$services,
		array_fill( 0, count( $services ), $author )
	);

	return array_values(
		array_filter(
			$services_with_urls,
			function( $service ) : bool {
				return ! empty( $service['url'] );
			}
		)
	);
}

/**
 * Apply Author to Social Service
 * The values for each service in an author's data is user generated.
 * They may have input any string. Ultimately we want a URL.
 *
 * @param array            $service
 * @param stdClass|WP_User $author
 * @return array $service Service possibly updated with a string value for it's url property.
 */
function cata_cap_apply_author_to_social_service( array $service, stdClass|WP_User $author ): array {

	$initial_value = cata_cap_get_the_coauthor_meta( $service['field'], $author, '' );

	// Initial value might a value URL.
	if ( wp_http_validate_url( $initial_value ) ) {

		$service['url'] = $initial_value;

	} else {

		// Sanitize username by removing white space and prefixed @ symbols, then encoding.
		$sanitized_value = rawurlencode( trim( ltrim( $initial_value, '@' ) ) );

		if ( empty( $sanitized_value ) ) {
			return $service;
		}

		// Plug the sanitized URL into the service's URL pattern.
		$formatted_value = sprintf( $service['pattern'], $sanitized_value );

		if ( wp_http_validate_url( $formatted_value ) ) {
			$service['url'] = $formatted_value;
		}
	}

	return $service;
}

/**
 * Get Author Social Platforms
 * 
 * @param stdClass|WP_User $author
 * @return array
 */
function cata_cap_get_author_social_services( stdClass|WP_User $author ): array {
	return cata_cap_apply_author_to_social_services( cata_cap_get_social_services(), $author );
}
