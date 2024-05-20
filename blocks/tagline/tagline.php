<?php
/**
 * Blocks > Tagline
 * 
 * @package Cata\CoAuthors_Plus\Blocks
 */

namespace Cata\CoAuthors_Plus\Blocks;

/**
 * Register Tagline Block
 */
function register_tagline_block() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', __NAMESPACE__ . '\\register_tagline_block' );

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
