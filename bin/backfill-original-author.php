<?php
global $wpdb;

$dry_run    = in_array( '--dry-run', $args, true );
$batch_size = 200;

if ( $dry_run ) {
	WP_CLI::log( 'Dry run — no changes will be made.' );
}

$query_args = array(
	'post_type'      => 'post',
	'post_status'    => 'any',
	'posts_per_page' => $batch_size,
	'fields'         => 'ids',
	'no_found_rows'  => true,
	'meta_query'     => array(
		array(
			'key'     => '_original_author',
			'compare' => 'NOT EXISTS',
		),
	),
);

$total_set     = 0;
$total_skipped = 0;
$batch         = 0;

while ( true ) {
	// Real run: always fetch page 1 — processed posts drop out of the NOT EXISTS query.
	// Dry run: paginate forward since data is unchanged.
	$query_args['paged'] = $dry_run ? ( $batch + 1 ) : 1;
	$batch++;

	$q = new WP_Query( $query_args );
	if ( ! $q->have_posts() ) {
		break;
	}

	$post_ids    = array_map( 'intval', $q->posts );
	$placeholder = implode( ',', $post_ids );

	// Fetch the author of the earliest revision for each post in this batch in one query.
	$rows = $wpdb->get_results(
		"SELECT r.post_parent, r.post_author
		 FROM {$wpdb->posts} r
		 INNER JOIN (
		     SELECT post_parent, MIN(ID) AS min_id
		     FROM {$wpdb->posts}
		     WHERE post_parent IN ({$placeholder})
		     AND post_type = 'revision'
		     GROUP BY post_parent
		 ) first_rev ON r.ID = first_rev.min_id"
	);

	$author_by_post = array();
	foreach ( $rows as $row ) {
		$author_by_post[ (int) $row->post_parent ] = (int) $row->post_author;
	}

	foreach ( $post_ids as $post_id ) {
		if ( ! isset( $author_by_post[ $post_id ] ) ) {
			$total_skipped++;
			continue;
		}
		if ( ! $dry_run ) {
			update_post_meta( $post_id, '_original_author', $author_by_post[ $post_id ] );
		}
		$total_set++;
	}

	array_map( 'clean_post_cache', $post_ids );

	WP_CLI::log( "Batch {$batch}: set {$total_set}, skipped {$total_skipped} (no revisions)." );
}

$action = $dry_run ? 'Would update' : 'Updated';
WP_CLI::success( "Backfill complete. {$action} {$total_set} posts; {$total_skipped} skipped (no revisions)." );
