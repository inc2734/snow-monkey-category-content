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

foreach ( $custom_post_types as $custom_post_type ) {
	Framework::control(
		'checkbox',
		Helper::get_custom_post_archive_meta_name( 'remove-top-margin', $custom_post_type ),
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

foreach ( $custom_post_types as $custom_post_type ) {
	$section = Framework::get_section( 'design-' . $custom_post_type . '-archive' );
	$control = Framework::get_control( Helper::get_custom_post_archive_meta_name( 'remove-top-margin', $custom_post_type ) );
	$control->join( $section )->join( $panel );
}
