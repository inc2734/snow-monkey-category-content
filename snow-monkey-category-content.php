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
}

require_once( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/vendor/autoload.php' );
new Bootstrap();
