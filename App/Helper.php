<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\CategoryContent\App;

class Helper {

	/**
	 * Load files
	 *
	 * @param string $directory
	 */
	public static function load( $directory ) {
		foreach ( glob( untrailingslashit( $directory ) . '/*' ) as $file ) {
			if ( is_dir( $file ) ) {
				static::load( $file );
			} else {
				require_once( $file );
			}
		}
	}

	/**
	 * Return public root pages
	 *
	 * @return array
	 */
	public static function get_pages() {
		return get_pages(
			[
				'parent' => 0,
			]
		);
	}

	/**
	 * Return all categories
	 */
	public static function get_all_categories() {
		$terms = wp_cache_get( 'all-categories' );
		if ( false !== $terms ) {
			return $terms;
		}

		$terms = get_terms( [ 'category' ] );
		wp_cache_set( 'all-categories', $terms );
		return $terms;
	}

	/**
	 * Return meta name of the category
	 */
	public static function get_term_meta_name( $key, $term ) {
		return $term->taxonomy . '-category-content-' . $term->term_id . '-' . $key;
	}

	/**
	 * Return meta name of the page
	 */
	public static function get_page_meta_name( $key ) {
		return 'snow-monkey-category-content-' . $key;
	}
}
