<?php
/**
 * Plugin name: Snow Monkey Category Content
 * Description: Require Snow Monkey v7 or more
 * Version: 0.0.2
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

		$theme = wp_get_theme();
		if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
			return;
		}

		add_action( 'init', [ $this, '_activate_autoupdate' ] );
		add_action( 'snow_monkey_post_load_customizer', [ $this, '_load_customizer' ] );

		add_filter( 'snow_monkey_template_part_render', [ $this, '_replace_content' ], 10, 2 );
		add_filter( 'document_title_parts', [ $this, '_replace_title' ] );

		add_action( 'wp_head', [ $this, '_display_page_title' ] );
	}

	/**
	 * Loads customizer
	 */
	public function _load_customizer() {
		Helper::load( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/customizer' );
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
	 * Replace category archive page content
	 *
	 * @param string $html
	 * @param string $slug
	 * @return string
	 */
	public function _replace_content( $html, $slug ) {
		if ( 'templates/view/archive' !== $slug ) {
			return $html;
		}

		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return $html;
		}

		$term    = get_queried_object();
		$page_id = get_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );

		if ( ! $page_id || 'draft' !== get_post_status( $page_id ) ) {
			return $html;
		}

		setup_postdata( $page_id );
		ob_start();
		the_content();
		$content = ob_get_clean();
		wp_reset_postdata();

		return str_replace(
			'<div class="c-entry__body">',
			'<div class="c-entry__body"><div class="p-entry-content">' . $content . '</div>',
			$html
		);
	}

	/**
	 * Replace category archive page title tag
	 *
	 * @param array $title
	 * @return array
	 */
	public function _replace_title( $title ) {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return $title;
		}

		$term    = get_queried_object();
		$page_id = get_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );

		if ( ! $page_id || 'draft' !== get_post_status( $page_id ) ) {
			return $title;
		}

		$title['title'] = get_the_title( $page_id );
		return $title;
	}

	public function _display_page_title() {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return;
		}

		$term = get_queried_object();
		$display_title = get_theme_mod( Helper::get_term_meta_name( 'display-title', $term ) );
		if ( $display_title ) {
			return;
		}
		?>
		<style id="snow-monkey-category-content-style">
		.c-entry__header { display: none; }
		</style>
		<?php
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

	foreach ( $terms as $term ) {
		remove_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );
		remove_theme_mod( Helper::get_term_meta_name( 'display-title', $term ) );
	}
}

register_uninstall_hook( __FILE__, 'uninstall_callback' );
