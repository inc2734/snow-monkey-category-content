<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_Customizer_Framework\Framework;
use Snow_Monkey\Plugin\CategoryContent\App\Helper;

$all_terms = Helper::get_all_categories();
$all_pages = Helper::get_pages();

$choices = [
	0 => __( 'None', 'snow-monkey-category-content' ),
];
foreach ( $all_pages as $_page ) {
	$choices[ $_page->ID ] = $_page->post_title;
}

foreach ( $all_terms as $_term ) {
	Framework::control(
		'select',
		Helper::get_term_meta_name( 'page-id', $_term ),
		[
			'label'    => __( 'The page used as content', 'snow-monkey-category-content' ),
			'priority' => 10,
			'default'  => 0,
			'choices'  => $choices,
		]
	);
}

if ( ! is_customize_preview() ) {
	return;
}

$panel = Framework::get_panel( 'design' );

foreach ( $all_terms as $_term ) {
	$section = Framework::get_section( 'design-category-' . $_term->term_id );
	$control = Framework::get_control( Helper::get_term_meta_name( 'page-id', $_term ) );
	$control->join( $section )->join( $panel );
}
