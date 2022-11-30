<?php
/**
 * Cata Co-Authors Plus
 *
 * @package   Cata\CoAuthors_Plus
 * @author    Thought & Expression Co. <devjobs@thought.is>
 * @copyright 2021 Thought & Expression Co.
 * @license   GNU GENERAL PUBLIC LICENSE
 *
 * @wordpress-plugin
 * Plugin Name: Cata Co-Authors Plus
 * Description: Common functions, configuration and compatibility fixes for Co-Authors Plus when used in Cata child themes. Not a fork or replacement for CAP.
 * Author:      Thought & Expression Co. <devjobs@thought.is>
 * Author URI:  https://thought.is
 * Version:     0.6.0
 * License:     GPL v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Require Global Functions
 */
require_once __DIR__ . '/includes/global-functions.php';

/**
 * Require classes
 */
require_once __DIR__ . '/includes/api/class-api.php';
require_once __DIR__ . '/includes/api/coauthor-controller/class-coauthor-controller.php';
require_once __DIR__ . '/includes/api/guest-author-controller/class-guest-author-controller.php';
require_once __DIR__ . '/includes/editor/class-editor.php';
require_once __DIR__ . '/includes/editor/block/class-block.php';
require_once __DIR__ . '/includes/editor/classic/class-classic.php';
require_once __DIR__ . '/includes/jetpack-compat/class-jetpack-compat.php';
require_once __DIR__ . '/includes/fields/class-fields.php';
require_once __DIR__ . '/includes/oembed/class-oembed.php';

/**
 * Instantiate Classes
 */
new Cata\CoAuthors_Plus\API();
new Cata\CoAuthors_Plus\Fields();
new Cata\CoAuthors_Plus\Jetpack_Compat();
new Cata\CoAuthors_Plus\oEmbed();

/**
 * Enable CoAuthors_Template_Filters
 */
add_filter( 'coauthors_auto_apply_template_tags', '__return_true' );

/**
 * No Web Stories Support
 *
 * @link https://github.com/thoughtis/cata-co-authors-plus/issues/30
 * @param $post_types Post types supporting the author taxonomy.
 * @return array Updated array of post types, without web-story.
 */
function cata_cap_no_web_stories_support( array $post_types ) : array {
	return array_values(
		array_diff(
			$post_types,
			array( 'web-story' )
		)
	);
}
add_filter( 'coauthors_supported_post_types', 'cata_cap_no_web_stories_support' );

/**
 * Get Plugin Directory URL
 * 
 * @return string
 */
function cata_cap_get_plugin_directory_url() : string {
	return plugin_dir_url( __FILE__ );
}

/**
 * Get Plugin Directory Path
 * 
 * @return string
 */
function cata_cap_get_plugin_directory_path() : string {
	return plugin_dir_path( __FILE__ );
}

/**
 * Use Block Editor
 * Allow themes to opt in to block editor support for Guest Authors.
 */
function cata_cap_use_block_editor() : void {
	if ( ! apply_filters( 'cata_cap_use_block_editor', false ) ) {
		return;
	}
	new Cata\CoAuthors_Plus\Editor();
	new Cata\CoAuthors_Plus\Editor\Block();
	new Cata\CoAuthors_Plus\Editor\Classic();
}
add_action( 'after_setup_theme', 'cata_cap_use_block_editor' );
