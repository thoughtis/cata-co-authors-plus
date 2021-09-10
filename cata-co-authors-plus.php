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
 * Version:     0.2.1
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
require_once __DIR__ . '/includes/jetpack-compat/class-jetpack-compat.php';
require_once __DIR__ . '/includes/fields/class-fields.php';
require_once __DIR__ . '/includes/rss/class-rss.php';

/**
 * Instantiate Classes
 */
new Cata\CoAuthors_Plus\API();
new Cata\CoAuthors_Plus\Fields();
new Cata\CoAuthors_Plus\Jetpack_Compat();
new Cata\CoAuthors_Plus\RSS();
