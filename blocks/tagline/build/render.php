<?php
/**
 * Render Co-Author Template
 *
 * @link https://github.com/WordPress/WordPress/blob/6.5.2/wp-includes/blocks.php#L498-L523
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 */

$author  = $block->context['co-authors-plus/author'] ?? array();
$tagline = $author['tagline']['raw'] ?? '';

if ( '' === $tagline ) {
	return '';
}
?>
<div <?php echo get_block_wrapper_attributes( apply_filters( "{$block->name}_block_wrapper_attributes", [], $attributes ) ); ?>>
	<?php echo wp_kses_post( wpautop( wptexturize( $tagline ) ) ); ?>
</div>
