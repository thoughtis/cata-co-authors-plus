<?php
/**
 * CLI
 *
 * @package Cata\CoAuthors_Plus
 */

namespace Cata\CoAuthors_Plus;

/**
 * CLI
 */
class CLI extends \WP_CLI_Command {

	/**
	 * Backfill _original_author meta from the earliest revision of each post.
	 *
	 * Posts with no revisions are skipped and left unset.
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Preview changes without writing to the database.
	 *
	 * ## EXAMPLES
	 *
	 *     wp cata-cap backfill-original-author
	 *     wp cata-cap backfill-original-author --dry-run
	 *
	 * @subcommand backfill-original-author
	 *
	 * @param array $args       Positional arguments.
	 * @param array $assoc_args Named arguments.
	 */
	public function backfill_original_author( array $args, array $assoc_args ) : void {
		global $wpdb;

		$dry_run    = (bool) \WP_CLI\Utils\get_flag_value( $assoc_args, 'dry-run', false );
		$batch_size = 200;
		$min_id     = 0;
		$batch      = 0;
		$total_set  = 0;
		$total_skipped = 0;

		if ( $dry_run ) {
			\WP_CLI::log( 'Dry run — no changes will be made.' );
		}

		while ( true ) {
			$batch++;

			// Paginate by ID so each post is visited exactly once, whether or not it gets meta set.
			$post_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT p.ID
					 FROM {$wpdb->posts} p
					 WHERE p.post_type = 'post'
					 AND p.post_status NOT IN ('auto-draft', 'trash')
					 AND p.ID > %d
					 AND NOT EXISTS (
					     SELECT 1 FROM {$wpdb->postmeta} pm
					     WHERE pm.post_id = p.ID AND pm.meta_key = '_original_author'
					 )
					 ORDER BY p.ID ASC
					 LIMIT %d",
					$min_id,
					$batch_size
				)
			);

			if ( empty( $post_ids ) ) {
				break;
			}

			$post_ids    = array_map( 'intval', $post_ids );
			$min_id      = max( $post_ids );
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

			if ( ! $dry_run ) {
				array_map( 'clean_post_cache', $post_ids );
			}

			\WP_CLI::log( "Batch {$batch}: set {$total_set}, skipped {$total_skipped} (no revisions)." );
		}

		$action = $dry_run ? 'Would update' : 'Updated';
		\WP_CLI::success( "Backfill complete. {$action} {$total_set} posts; {$total_skipped} skipped (no revisions)." );
	}
}
