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

		$assigned_terms = static::get_assigned_terms();

		foreach ( $pages as $key => $page ) {
			if ( isset( $assigned_terms[ $page->ID ] ) ) {
				unset( $pages[ $key ] );
			}
		}

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

	/**
	 * Return array of assigned terms
	 *
	 * @return array
	 */
	protected static function get_assigned_terms() {
		$term_metas = wp_cache_get( 'snow-monkey-category-content', 'terms' );
		if ( false !== $term_metas ) {
			return $term_metas;
		}

		$theme_mods = get_theme_mods();
		$term_metas = [];

		foreach ( $theme_mods as $key => $value ) {
			if ( ! preg_match( '/^snow-monkey-category-content-(.+)-(\d+)-page-id$/', $key, $matches ) ) {
				continue;
			}

			$term = get_term( $matches[2], $matches[1] );
			if ( is_wp_error( $term ) ) {
				continue;
			}

			$term_metas[ $value ] = $term;
		}

		wp_cache_set( 'snow-monkey-category-content', $term_metas, 'terms' );
		return $term_metas;
	}

	/**
	 * Return assigned term
	 *
	 * @param int $page_id
	 * @return null|WP_Term
	 */
	public static function get_term_by_page_id( $page_id ) {
		$assigned_terms = static::get_assigned_terms();
		if ( isset( $assigned_terms[ $page_id ] ) ) {
			return $assigned_terms[ $page_id ];
		}
	}
}
