<?php
/**
 * VIP Compat
 * 
 * @package Cata\CoAuthors_Plus
 * @since 0.1.1
 */

namespace Cata\CoAuthors_Plus;

/**
 * VIP Compat
 */
class VIP_Compat {
	/**
	 * Construct
	 */
	public function __construct() {
		add_filter( 'block_editor_settings_all', array( __CLASS__, 'image_sitemap_skip_avatars' ) );
	}

	/**
	 * Replace Image Dimensions Editor Setting
	 * The imageDimensions editor setting is missing because of the removal of intermediate images sizes.
	 * Bring them back just so we can use theme defined images sizes in the editor.
	 * This fixes a bug in the Co-Author Featured Image block.
	 *
	 * @link https://github.com/Automattic/vip-go-mu-plugins/blob/97c10f8f2f2f871c9a24731a66037962367e2c66/a8c-files.php#L676-L698
	 * @param array $settings
	 * @return array $settings
	 */
	public static function replace_image_dimensions_editor_setting( array $settings ): array {
		if ( ! defined( 'VIP_GO_APP_ENVIRONMENT' ) ) {
			return $settings;
		}

		remove_filter( 'intermediate_image_sizes', 'wpcom_intermediate_sizes' );

		$settings['imageDimensions'] = get_default_block_editor_settings()['imageDimensions'];

		add_filter( 'intermediate_image_sizes', 'wpcom_intermediate_sizes' );

		return $settings;
	}
}
