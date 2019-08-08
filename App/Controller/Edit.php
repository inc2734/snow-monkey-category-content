<?php
/**
 * @package snow-monkey-member-post
 * @author inc2734
 * @license GPL-2.0+
 */

namespace Snow_Monkey\Plugin\CategoryContent\App\Controller;

use Snow_Monkey\Plugin\CategoryContent\App\Helper;

class Edit {

	public function __construct() {
		add_filter( 'display_post_states', [ $this, '_display_post_states' ], 10, 2 );
	}

	/**
	 * Add post status comment
	 *
	 * @param array $post_states
	 * @param WP_Post $post
	 * @return array
	 */
	public function _display_post_states( $post_states, $post ) {
		if ( 'page' !== $post->post_type || ! array_key_exists( 'draft', $post_states ) ) {
			return $post_states;
		}

		$term = Helper::get_term_by_page_id( $post->ID );
		if ( ! $term ) {
			return $post_states;
		}

		$taxonomy = get_taxonomy( $term->taxonomy );

		$post_states[] = sprintf(
			/* translators: %1: Taxonomy label, %2: Term name */
			esc_html__( 'Assigned %1$s: %2$s', 'snow-monkey-category-content' ),
			$taxonomy->label,
			$term->name
		);

		return $post_states;
	}
}
