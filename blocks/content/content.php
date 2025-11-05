<?php
/**
 * Blocks > Guest Author Content
 * 
 * @package Cata\CoAuthors_Plus\Blocks
 * @since 0.1.0
 */

namespace Cata\CoAuthors_Plus\Blocks;

/**
 * Register Guest Author Content Block
 */
function register_guest_author_content_block() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', __NAMESPACE__ . '\\register_guest_author_content_block' );
