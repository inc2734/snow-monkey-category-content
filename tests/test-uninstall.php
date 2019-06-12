<?php
use Snow_Monkey\Plugin\CategoryContent\App\Helper;

class Uninstall_Test extends WP_UnitTestCase {

	/**
	 * @test
	 */
	public function uninstall_term_meta() {
		$category_ids = $this->factory()->category->create_many( 5 );
		$post_id      = $this->factory()->post->create( [ 'post_type' => 'post' ] );
		$page_id      = $this->factory()->post->create( [ 'post_type' => 'page' ] );

		wp_set_object_terms( $post_id, $category_ids, 'category' );

		$terms = Helper::get_all_categories();
		$pages = Helper::get_pages();

		foreach ( $terms as $term ) {
			set_theme_mod( Helper::get_term_meta_name( 'page-id', $term ), $page_id );
		}

		\Snow_Monkey\Plugin\CategoryContent\uninstall_callback();

		foreach ( $terms as $term ) {
			$this->assertFalse( get_theme_mod( Helper::get_term_meta_name( 'page-id', $term ) ) );
		}
	}

		/**
		 * @test
		 */
		public function uninstall_page_meta() {
			$category_id = $this->factory()->category->create();
			$post_id     = $this->factory()->post->create( [ 'post_type' => 'post' ] );
			$page_id     = $this->factory()->post->create( [ 'post_type' => 'page' ] );

			wp_set_object_terms( $post_id, $category_id, 'category' );

			$terms = Helper::get_all_categories();
			$pages = Helper::get_pages();

			foreach ( $pages as $page ) {
				update_post_meta( $page->ID, Helper::get_page_meta_name( 'category-id' ), $category_id );
			}

			\Snow_Monkey\Plugin\CategoryContent\uninstall_callback();

			foreach ( $pages as $page ) {
				$this->assertSame( '', get_post_meta( $page->ID, Helper::get_page_meta_name( 'category-id' ), true ) );
			}
		}
}
