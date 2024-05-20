<?php
/**
 * Social Links
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.6.3
 */

namespace Cata\CoAuthors_Plus\API\Block_Schema;

/**
 * Social Links
 */
class Social_Links {
	/**
	 * Construct
	 */
	public function __construct() {
		add_action( 'rest_api_init', [ __CLASS__, 'register_social_links_rest_field' ] );
	}

	/**
	 * Register REST Field
	 */
	public static function register_social_links_rest_field(): void {
		register_rest_field(
			'coauthor',
			'social_links',
			array(
				'get_callback' => [ __CLASS__, 'rest_get_social_links' ],
				'schema'       => array(
					'context'     => array( 'view' ),
					'description' => 'Author\'s social profile URLs.',
					'readonly'    => true,
					'type'        => 'array',
					'items' 	  => array(
						'type'       => 'object',
						'properties' => array(
							'url' => array(
								'description' => __( 'URL of social profile.', 'cata-co-authors-plus' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
							'service' => array(
								'description' => __( 'Related social platform.', 'cata-co-authors-plus' ),
								'type'        => 'string',
								'context'     => array( 'view' ),
								'readonly'    => true,
							),
						),
					),
				),
			)
		);
	}

	/**
	 * REST Get Social Links
	 * 
	 * @param array $data The existing data about this object.
	 * @return array The value of our example field.
	 */
	public static function rest_get_social_links( array $data ): array {
		
		global $coauthors_plus;

		$author = $coauthors_plus->get_coauthor_by(
			'user_nicename',
			$data['user_nicename']
		);

		return array_map( 
			function( $a ) {
				return array_intersect_key(
					$a,
					array_flip(
						[
							'service',
							'url'
						]
					)
				);
			},
			cata_cap_get_author_social_services( $author )
		);
	}

}
