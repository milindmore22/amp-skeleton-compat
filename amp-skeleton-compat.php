<?php
/**
 * AMP plugin name compatibility plugin bootstrap.
 *
 * @package   Google\AMP_Plugin_Name_Compat
 * @author    Your Name, Google
 * @license   GPL-2.0-or-later
 * @copyright 2020 Google Inc.
 *
 * @wordpress-plugin
 * Plugin Name: AMP Plugin Name Compat
 * Plugin URI: https://plugin-name.com/
 * Description: Plugin to add <a href="https://wordpress.org/plugins/amp/">AMP plugin</a> compatibility to (theme name )/ ( plugin name ).
 * Version: 0.1
 * Author: Your Name, Google
 * Author URI: https://yoursite.com
 * License: GNU General Public License v2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace Google\AMP_Plugin_Name_Compat;

/**
 * Whether the page is AMP.
 *
 * @return bool Is AMP.
 */
function is_amp() {
	return function_exists( 'amp_is_request' ) && amp_is_request();
}

/**
 * Run Hooks.
 */
function add_hooks() {

	/**
	 *  Keep this if you are using theme.
	 */
	if ( 'themename' === get_template() && is_amp() ) {
		/**
		 *  Remove action which might add scripts or inline scripts.
		 *
		 * @see https://developer.wordpress.org/reference/functions/remove_action/
		 */
		remove_action( 'wp_head', 'enequeue_themes_scripts', 1 );

		/**
		 * The Action will override the scripts and styles.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/wp_enqueue_scripts/
		 */
		add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\override_scripts_and_styles', 11 );

		/**
		 * Add sanitizers to convert non-AMP functions to AMP components.
		 *
		 * @see https://amp-wp.org/reference/hook/amp_content_sanitizers/
		 */
		add_filter( 'amp_content_sanitizers', __NAMESPACE__ . '\filter_sanitizers' );
	}
}

add_action( 'wp', __NAMESPACE__ . '\add_hooks' );

/**
 * Remove enqueued JS.
 *
 * @see lovecraft_load_javascript_files()
 */
function override_scripts_and_styles() {
	/**
	 * If you are unable to remove any scripts by remove action, you can dequeue them here.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_dequeue_script/
	 */
	wp_dequeue_script( 'your-handle' );

	/**
	 * Adds your custom inline style.
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_add_inline_style/
	 */
	wp_add_inline_style( 'plugin_name_style', file_get_contents( __DIR__ . '/css/amp-style.css' ) );
}

/**
 * Add sanitizer to fix up the markup.
 *
 * @param array $sanitizers Sanitizers.
 * @return array Sanitizers.
 */
function filter_sanitizers( $sanitizers ) {
	require_once __DIR__ . '/sanitizers/class-sanitizer.php';
	$sanitizers[ __NAMESPACE__ . '\Sanitizer' ] = array();
	return $sanitizers;
}

/**
 * Bonus improvement: add font-display:swap to the Google Fonts!
 *
 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 * @see https://developers.google.com/fonts/docs/getting_started
 * @see https://developer.wordpress.org/reference/hooks/style_loader_src/
 *
 * @param string $src    Stylesheet URL.
 * @param string $handle Style handle.
 * @return string Filtered stylesheet URL.
 */
function filter_font_style_loader_src( $src, $handle ) {
	if ( 'google-font-handle' === $handle ) {
		$src = add_query_arg( 'display', 'swap', $src );
	}
	return $src;
}

add_filter( 'style_loader_src', __NAMESPACE__ . '\filter_font_style_loader_src', 10, 2 );
