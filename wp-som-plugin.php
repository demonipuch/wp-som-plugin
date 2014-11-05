<?php
/**
 * Plugin Name: WP SoM Plugin
 * Plugin URI: http://bitbucket.org/demonipuch/wp-som-plugin
 * Description: This plugin enables support for custom post types and advanced custom fields to manage Season of Mist's bands and releases.
 * Version: 0.1
 * Author: Cedric Puchalver
 * License: GPLv3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('ABSPATH') or die();
define( 'ACF_LITE', true );

/**
 *
 * Register required plugin features. THIS SHOULD BE PLACED WITHIN THE THEME
 *
 * @link http://codex.wordpress.org/Function_Reference/add_theme_support
 *
 */
function wp_som_plugin_features()  {
	global $wp_version;

	// Add theme support for featured images for post, band and release
	add_theme_support( 'post-thumbnails', array( 'post', 'band', 'release' ) );
}
add_action( 'after_theme_setup', 'wp_som_plugin_features' );

/**
 *
 * Includes Advanced Custom Fields Pro
 *
 * @link http://www.advancedcustomfields.com/resources/including-acf-in-a-plugin-theme/
 *
 */
function acf_settings_path( $path ) {
	$path = plugin_dir_path( __FILE__ ) . '/includes/plugins/acf/';
	return $path;
}
add_filter( 'acf/settings/path', 'acf_settings_path' );

function acf_settings_dir( $dir ) {
	$dir = plugin_dir_url( __FILE__  ) . '/includes/plugins/acf/';
	return $dir;
}
add_filter( 'acf/settings/dir', 'acf_settings_dir' );

/**
 *
 * Register advanced custom fields
 *
 * @link http://www.advancedcustomfields.com
 *
 */
require plugin_dir_path( __FILE__ ) . '/includes/plugins/acf/acf.php';

/**
 *
 * Register custom post types
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 *
 */
require 'includes/custom_post_types/band.php';
require 'includes/custom_post_types/release.php';

/**
 *
 * Register custom widgets
 *
 * @link 
 *
 */
require 'includes/widgets/widget-release.php';

/**
 *
 * Plugin functions
 *
 */
require 'include/functions.php';

?>