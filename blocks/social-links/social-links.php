<?php
/**
 * Blocks > Social Links
 * 
 * @package Cata\CoAuthors_Plus\Blocks
 */

namespace Cata\CoAuthors_Plus\Blocks;

use WP_Block;

/**
 * Render Social Link with Co-Author Context
 * If there's already a URL or this author doesn't have one, bail to avoid infinite loop.
 * 
 * @param string $block_content
 * @param array $block
 * @param WP_Block $instance
 * @return string
 */
function render_social_link_with_coauthor_context( string $block_content, array $block, WP_Block $instance ): string {

	if ( 'core/social-link' !== (string) $block['blockName'] ) {
		return $block_content;
	}
	
	if ( ! empty( $block['attrs']['url'] ?? '' ) ) {
		return $block_content;
	}
	
	$author = $instance->context['co-authors-plus/author'] ?? array();
	
	if ( empty ( $author ) ) {
		return $block_content;
	}
	
	$service = $block['attrs']['service'];
	
	$links = array_values(
		array_filter(
			$author['social_links'],
			fn( $link ): bool => $service === $link['service']
		)
	);
	
	if ( empty( $links ) ) {
		return $block_content;
	}
	
	$block['attrs']['url'] = $links[0]['url'];
	
	return (new WP_Block( $block, $instance->context ))->render();		
}
add_filter( 'render_block', __NAMESPACE__ . '\\render_social_link_with_coauthor_context', 10, 3 );

/**
 * Add Co-Author Context Support to Social Link Block
 * 
 * @param array $args
 * @param string $block_type
 */
function add_coauthor_context_support_to_social_link_block( array $args, string $block_type ): array {
	if ( 'core/social-link' !== $block_type ) {
		return $args;
	}
	return array(
		...$args,
		'uses_context' => array(
			...$args['uses_context'],
			'co-authors-plus/author'
		)
	);
}
add_filter( 'register_block_type_args', __NAMESPACE__ . '\\add_coauthor_context_support_to_social_link_block', 10, 2 );
