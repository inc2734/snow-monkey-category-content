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
	 *
	 * @param string $taxonomy
	 * @return array
	 */
	public static function get_terms( $taxonomy ) {
		return Snow_Monkey_Helper::get_terms( $taxonomy );
	}

	/**
	 * Return all custom post types
	 *
	 * @return array
	 */
	public static function get_custom_post_types() {
		return Snow_Monkey_Helper::get_custom_post_types();
	}

	/**
	 * Return all taxonomies
	 *
	 * @return array
	 */
	public static function get_taxonomies() {
		return Snow_Monkey_Helper::get_taxonomies();
	}

	/**
	 * Return meta name of the custom post type
	 *
	 * @param string $key
	 * @param string $post_type
	 * @return string
	 */
	public static function get_custom_post_archive_meta_name( $key, $post_type ) {
		return 'snow-monkey-category-content-custom-post-archive-' . $post_type . '-' . $key;
	}

	/**
	 * Return meta name of the term
	 *
	 * @param string $key
	 * @param WP_Term $term
	 * @return string
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
			if ( preg_match( '/^snow-monkey-category-content-custom-post-archive-(.+)-page-id$/', $key ) ) {
				continue;
			}

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

	/**
	 * Return array of assigned custom post types
	 *
	 * @return array
	 */
	protected static function get_assigned_custom_post_types() {
		$custom_post_types = wp_cache_get( 'snow-monkey-category-content', 'custom-post-types' );
		if ( false !== $custom_post_types ) {
			return $custom_post_types;
		}

		$theme_mods = get_theme_mods();
		$custom_post_types = [];

		foreach ( $theme_mods as $key => $value ) {
			if ( ! preg_match( '/^snow-monkey-category-content-custom-post-archive-(.+)-page-id$/', $key, $matches ) ) {
				continue;
			}

			$post_type_object = get_post_type_object( $matches[1] );
			if ( ! $post_type_object ) {
				continue;
			}

			$custom_post_types[ $value ] = $post_type_object;
		}

		wp_cache_set( 'snow-monkey-category-content', $custom_post_types, 'custom-post-types' );
		return $custom_post_types;
	}

	/**
	 * Return assigned custom post type
	 *
	 * @param int $page_id
	 * @return null|object
	 */
	public static function get_custom_post_type_by_page_id( $page_id ) {
		$assigned_custom_post_types = static::get_assigned_custom_post_types();
		if ( isset( $assigned_custom_post_types[ $page_id ] ) ) {
			return $assigned_custom_post_types[ $page_id ];
		}
	}
}
