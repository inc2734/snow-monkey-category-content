<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_Customizer_Framework\Framework;
use Snow_Monkey\Plugin\CategoryContent\App\Helper;
use Framework\Controller\Controller;

$all_terms = Helper::get_terms( 'post_tag' );

foreach ( $all_terms as $_term ) {
	Framework::control(
		'checkbox',
		Helper::get_term_meta_name( 'remove-top-margin', $_term ),
		[
			'label'       => __( 'Remove top margin of the content', 'snow-monkey-category-content' ),
			'priority'    => 12,
			'default'     => false,
			'active_callback' => function() {
				return 'archive' === Controller::get_view();
			},
		]
	);
}

if ( ! is_customize_preview() ) {
	return;
}

$panel = Framework::get_panel( 'design' );

foreach ( $all_terms as $_term ) {
	$section = Framework::get_section( 'design-' . $_term->taxonomy . '-' . $_term->term_id );
	$control = Framework::get_control( Helper::get_term_meta_name( 'remove-top-margin', $_term ) );
	$control->join( $section )->join( $panel );
}
