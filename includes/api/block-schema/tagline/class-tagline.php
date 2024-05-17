<?php
/**
 * Tagline
 * 
 * @package Cata\CoAuthors_Plus
 * @since
 */

namespace Cata\CoAuthors_Plus\API\Block_Schema;

/**
 * Tagline
 */
class Tagline {
	/**
	 * Construct
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ __CLASS__ , 'register_tagline_rest_field' ] );
		add_filter( 'coauthors_blocks_store_data', [ __CLASS__, 'tagline_coauthor_blocks_store_data' ] );
	}

	/**
	 * Register REST Field
	 */
	public static function register_tagline_rest_field(): void {
		register_rest_field(
			'coauthor',
			'tagline',
			array(
				'get_callback' => [ __CLASS__, 'rest_get_tagline' ],
				'schema'       => array(
					'context'     => array( 'view' ),
					'description' => 'Author tagline.',
					'readonly'    => true,
					'type'        => 'object',
					'properties'  => array(
						'raw'      => array(
							'description' => __( 'Author tagline as stored in database.', 'cata-co-authors-plus' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
						'rendered' => array(
							'description' => __( 'Author tagline as rendered in HTML content.', 'cata-co-authors-plus' ),
							'type'        => 'string',
							'context'     => array( 'view' ),
							'readonly'    => true,
						),
					),
				),
			)
		);
	}

	/**
	 * REST Get Tagline
	 * 
	 * @param array $data The existing data about this object.
	 * @return array The value of our example field.
	 */
	public static function rest_get_tagline( array $data ): array {
		
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
	public static function tagline_coauthor_blocks_store_data( array $block_store_data ): array {
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
}
