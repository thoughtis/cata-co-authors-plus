<?php
$args = array(
	'post_type'      => 'post',
	'post_status'    => 'any',
	'posts_per_page' => 200,
	'paged'          => 1,
	'fields'         => 'ids',
	'meta_query'     => array(
		array(
			'key'     => '_original_author',
			'compare' => 'NOT EXISTS',
		),
	),
);

$total = 0;
while ( true ) {
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() ) {
		break;
	}

	foreach ( $q->posts as $post_id ) {
		$revisions = wp_get_post_revisions( $post_id, array( 'order' => 'ASC', 'posts_per_page' => 1 ) );
		$first     = reset( $revisions );
		if ( ! $first ) {
			continue;
		}
		update_post_meta( $post_id, '_original_author', (int) $first->post_author );
		$total++;
	}

	WP_CLI::log( "Processed page {$args['paged']}, total set: {$total}" );
	$args['paged']++;
}

WP_CLI::success( "Backfill complete. {$total} posts updated." );
