<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\CategoryContent\App;

use Framework\Helper as Snow_Monkey_Helper;

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
	 * Return draft root pages
	 *
	 * @return array
	 */
	public static function get_draft_pages() {
		$pages = wp_cache_get( 'snow-monkey-category-content-draft-pages' );
		if ( is_array( $pages ) ) {
			return $pages;
		}

		$pages = get_pages(
			[
				'parent'      => 0,
				'post_status' => 'draft',
			]
		);

		wp_cache_set( 'snow-monkey-category-content-draft-pages', $pages );
		return $pages;
	}

	/**
	 * Return all terms
	 */
	public static function get_terms( $taxonomy ) {
		return Snow_Monkey_Helper::get_terms( $taxonomy );
	}

	/**
	 * Return all taxonomies
	 */
	public static function get_taxonomies() {
		return Snow_Monkey_Helper::get_taxonomies();
	}

	/**
	 * Return meta name of the category
	 */
	public static function get_term_meta_name( $key, $term ) {
		return 'snow-monkey-category-content-' . $term->taxonomy . '-' . $term->term_id . '-' . $key;
	}
}
