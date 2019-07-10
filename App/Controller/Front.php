<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\CategoryContent\App\Controller;

use Snow_Monkey\Plugin\CategoryContent\App\Helper;

class Front {

	public function __construct() {
		if ( is_search() ) {
			return;
		}

		if ( ! is_category() && ! is_tag() ) {
			return;
		}

		add_filter( 'snow_monkey_template_part_render', [ $this, '_replace_content' ], 10, 2 );
		add_filter( 'document_title_parts', [ $this, '_replace_title' ] );

		add_action( 'wp_enqueue_scripts', [ $this, '_wp_enqueue_scripts' ], 100 );
		add_action( 'wp_head', [ $this, '_hide_page_title' ] );
		add_action( 'wp_head', [ $this, '_remove_top_margin' ] );
		add_action( 'admin_bar_menu', [ $this, '_admin_bar_menu' ], 100 );
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
			'<div class="c-entry__body" id="snow-monkey-category-content-body"><div class="p-entry-content">' . $content . '</div>',
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
		$term    = get_queried_object();
		$page_id = get_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );

		if ( ! $page_id || 'draft' !== get_post_status( $page_id ) ) {
			return $title;
		}

		$title['title'] = get_the_title( $page_id );
		return $title;
	}

	/**
	 * Enqueue assets
	 *
	 * @return void
	 */
	public function _wp_enqueue_scripts() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		wp_enqueue_style(
			'snow-monkey-category-content',
			SNOW_MONKEY_CATEGORY_CONTENT_URL . '/dist/css/app.min.css',
			[],
			filemtime( SNOW_MONKEY_CATEGORY_CONTENT_PATH . '/dist/css/app.min.css' )
		);
	}

	/**
	 * Hide page title
	 *
	 * @return void
	 */
	public function _hide_page_title() {
		$term = get_queried_object();
		$display_title = get_theme_mod( Helper::get_term_meta_name( 'display-title', $term ) );
		if ( $display_title ) {
			return;
		}
		?>
		<style id="snow-monkey-category-content-style-display-title">
		.c-entry__header { display: none !important; }
		</style>
		<?php
	}

	/**
	 * Remove top margin of the content
	 *
	 * @return void
	 */
	public function _remove_top_margin() {
		$term = get_queried_object();
		$remove_top_margin = get_theme_mod( Helper::get_term_meta_name( 'remove-top-margin', $term ) );
		if ( ! $remove_top_margin ) {
			return;
		}
		?>
		<style id="snow-monkey-category-content-style-remove-top-margin">
		.l-contents__inner, .l-contents__main > .c-entry { margin-top: 0 !important; }
		</style>
		<?php
	}

	/**
	 * Add edit page link to adminbar
	 *
	 * @param WP_Admin_Bar $wp_adminbar
	 * @return void
	 */
	public function _admin_bar_menu( $wp_adminbar ) {
		$term    = get_queried_object();
		$page_id = get_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) );

		if ( ! $page_id || 'draft' !== get_post_status( $page_id ) ) {
			return;
		}

		$wp_adminbar->add_node(
			[
				'id'    => 'snow-monkey-category-content-edit-page',
				'title' => __( 'Edit the page used as content', 'snow-monkey-category-content' ),
				'href'  => get_edit_post_link( $page_id, 'url' ),
			]
		);
	}
}
