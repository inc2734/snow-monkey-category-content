<?php
/**
 * @package snow-monkey-category-content
 * @author inc2734
 * @license GPL-2.0+
 */

use Inc2734\WP_Customizer_Framework\Framework;
use Snow_Monkey\Plugin\CategoryContent\App\Helper;
use Framework\Controller\Controller;

$custom_post_types = Helper::get_custom_post_types();

$all_pages = Helper::get_draft_pages();

$choices = [
	0 => __( 'None', 'snow-monkey-category-content' ),
];
foreach ( $all_pages as $_page ) {
	$choices[ $_page->ID ] = $_page->post_title;
}

foreach ( $custom_post_types as $custom_post_type ) {
	Framework::control(
		'select',
		Helper::get_custom_post_archive_meta_name( 'page-id', $custom_post_type ),
		[
			'label'       => __( 'The page used as content', 'snow-monkey-category-content' ),
			'description' => __( 'You can select from the draft pages.', 'snow-monkey-category-content' ),
			'priority'    => 10,
			'default'     => 0,
			'choices'     => $choices,
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

foreach ( $custom_post_types as $custom_post_type ) {
	$section = Framework::get_section( 'design-' . $custom_post_type . '-archive' );
	$control = Framework::get_control( Helper::get_custom_post_archive_meta_name( 'page-id', $custom_post_type ) );
	$control->join( $section )->join( $panel );
}
