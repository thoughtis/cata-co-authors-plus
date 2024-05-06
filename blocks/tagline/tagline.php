<?php
/**
 * Blocks > Tagline
 * 
 * @package Cata\CoAuthors_Plus\Blocks
 */

namespace Cata\CoAuthors_Plus\Blocks;

use WP_REST_Request;

/**
 * Register Tagline Block
 */
function register_tagline_block() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', __NAMESPACE__ . '\\register_tagline_block' );

/**
 * Register REST Field
 */
function register_tagline_rest_field(): void {
	register_rest_field(
		'coauthor',
		'tagline',
		array(
			'get_callback' => __NAMESPACE__ . '\\rest_get_tagline',
			'schema'       => array(
				'context'     => array( 'view' ),
				'description' => 'This is an example value for an example REST field.',
				'readonly'    => true,
				'type'        => 'object',
				'properties'  => array(
					'raw'      => array(
						'description' => __( 'Author tagline as stored in database.', 'co-authors-plus' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
					'rendered' => array(
						'description' => __( 'Author tagline as rendered in HTML content.', 'co-authors-plus' ),
						'type'        => 'string',
						'context'     => array( 'view' ),
						'readonly'    => true,
					),
				),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\\register_tagline_rest_field' );

/**
 * REST Get Tagline
 * 
 * @param array           $data The existing data about this object.
 * @param string          $field_name The name of the field we are adding: `example_field`.
 * @param WP_REST_Request $request The request that expects we will want to add this data.
 * @param string          $object_type What the REST API object type was named, in this case `coauthor`. 
 * @return array The value of our example field.
 */
function rest_get_tagline( array $data, string $field_name, WP_REST_Request $request, string $object_type ): array {
	
	global $coauthors_plus;

	$author = $coauthors_plus->get_coauthor_by(
		'user_nicename',
		$data['user_nicename']
	);

	$tagline = cata_cap_get_the_coauthor_meta( 'tagline', $author );

	return array(
		'raw'      => $tagline,
		'rendered' =>  wp_kses_post( wpautop( wptexturize( $tagline ) ) )
	);
}

/**
 * Tagline CoAuthor Blocks Store Data
 * 
 * @param array $block_store_data Data we'll pass to the `co-authors-plus/blocks` store for use in the editor.
 * @return array Updated data with placeholder text for our example field.
 */
function tagline_coauthor_blocks_store_data( array $block_store_data ): array {
	return array_merge(
		$block_store_data,
		array(
			'authorPlaceholder' => array_merge(
				$block_store_data['authorPlaceholder'],
				array(
					'tagline' => array(
						'raw'      => 'Example contents of author tagline. <strong>May contain HTML</strong>.',
						'rendered' => wp_kses_post( wpautop( wptexturize( 'Example contents of author tagline. <strong>May contain HTML</strong>.' ) ) ),
					)
				)
			)
		)
	);
}
add_filter( 'coauthors_blocks_store_data', __NAMESPACE__ . '\\tagline_coauthor_blocks_store_data' );

/**
 * Get Tagline Custom Block Wrapper Attributes
 * 
 * @param array $wrapper_attributes
 * @param array $attributes
 * @return array
 */
function get_tagline_custom_block_wrapper_attributes( array $wrapper_attributes, array $attributes ): array {
	$default = array(
		'class' => 'is-layout-flow',
	);

	$text_align = $attributes['textAlign'] ?? '';

	if ( empty( $text_align ) ) {
		return $default;
	}

	return array(
		'class' => $default['class'] . ' ' . sanitize_html_class( "has-text-align-{$text_align}" ),
	);
}
add_filter( 'cata-cap/tagline_block_wrapper_attributes', __NAMESPACE__ . '\\get_tagline_custom_block_wrapper_attributes', 10, 2 );
