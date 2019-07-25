<?php
/**
 * Plugin name: Snow Monkey Category Content
 * Description: Require Snow Monkey v7 or more
 * Version: 0.2.0
 * Author: inc2734
 * Author URI: https://2inc.org
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: snow-monkey-category-content
 *
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\CategoryContent;

use Snow_Monkey\Plugin\CategoryContent\App\Helper;
use Framework;

define( 'SNOW_MONKEY_CATEGORY_CONTENT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SNOW_MONKEY_CATEGORY_CONTENT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

class Bootstrap {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, '_plugins_loaded' ] );
	}

	public function _plugins_loaded() {
		load_plugin_textdomain( 'snow-monkey-category-content', false, basename( __DIR__ ) . '/languages' );

		$theme = wp_get_theme( get_template() );
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			return;
		}
		if ( ! version_compare( $theme->get( 'Version' ), '7.0.0', '>=' ) ) {
			return;
		}

		add_action( 'init', [ $this, '_activate_autoupdate' ] );
		add_action( 'snow_monkey_post_load_customizer', [ $this, '_load_customizer' ] );
		add_action( 'wp', [ $this, '_front_hooks' ] );
	}

	/**
	 * Activate auto update using GitHub
	 *
	 * @return void
	 */
	public function _activate_autoupdate() {
		new \Inc2734\WP_GitHub_Plugin_Updater\Bootstrap(
			plugin_basename( __FILE__ ),
			'inc2734',
			'snow-monkey-category-content'
		);
	}

	/**
	 * Loads customizer
	 */
	public function _load_customizer() {
		Helper::load( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/customizer' );
	}

	/**
	 * Setup for front page
	 *
	 * @return void
	 */
	public function _front_hooks() {
		new App\Controller\Front();
	}
}

require_once( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/vendor/autoload.php' );
new Bootstrap();

/**
 * Uninstall callback function
 *
 * @return void
 */
function uninstall_callback() {
	$categories = Helper::get_terms( 'category' );
	$post_tags  = Helper::get_terms( 'post_tag' );
	$terms      = array_merge( $categories, $post_tags );

	$taxonomies = Helper::get_taxonomies();
	foreach ( $taxonomies as $_taxonomy ) {
		$terms = array_merge( $terms, Helper::get_terms( $_taxonomy ) );
	}

	foreach ( $terms as $term ) {
		remove_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );
		remove_theme_mod( Helper::get_term_meta_name( 'display-title', $term ) );
		remove_theme_mod( Helper::get_term_meta_name( 'remove-top-margin', $term ) );
	}
}

register_uninstall_hook( __FILE__, 'uninstall_callback' );
