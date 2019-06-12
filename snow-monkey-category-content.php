<?php
/**
 * Plugin name: Snow Monkey Category Content
 * Version: 0.0.1
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

define( 'SNOW_MONKEY_CATEGORY_CONTENT_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SNOW_MONKEY_CATEGORY_CONTENT_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

class Bootstrap {

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, '_plugins_loaded' ] );
	}

	public function _plugins_loaded() {
		load_plugin_textdomain( 'snow-monkey-category-content', false, basename( __DIR__ ) . '/languages' );

		$theme = wp_get_theme();
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			return;
		}

		add_action( 'init', [ $this, '_activate_autoupdate' ] );
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
}

require_once( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/vendor/autoload.php' );
new Bootstrap();
