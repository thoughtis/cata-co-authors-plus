<?php
/**
 * Render Co-Author Content
 *
 * @link https://github.com/WordPress/WordPress/blob/6.5.2/wp-includes/blocks.php#L498-L523
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

$author    = $block->context['co-authors-plus/author'] ?? array();
$author_id = $author['id'] ?? 0;

if ( 0 === $author_id || 'guest-author' !== get_post_type( $author_id ) ) {
	return;
}

$author_content = wp_kses_post( get_the_content( null, false, $author_id ) );

if ( '' === $author_content ) {
	return;
}

$wrapper_attributes = get_block_wrapper_attributes( apply_filters( "{$block->name}_block_wrapper_attributes", [], $attributes ) );
?>

<div <?php echo $wrapper_attributes ?>>
	<?php echo do_blocks( $author_content ) ?>
</div>
