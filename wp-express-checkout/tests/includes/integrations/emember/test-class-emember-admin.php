<?php

namespace WP_Express_Checkout\Integrations;

use WP_Express_Checkout\Products;
use WP_UnitTestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-06-28 at 07:52:47.
 *
 * @group integrations
 * @group admin
 *
 * @covers WP_Express_Checkout\Integrations\Emember
 */
class EmemberAdminTest extends WP_UnitTestCase {

	/**
	 * @var Emember
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp() {
		set_current_screen( 'post.php' );
		$this->object = new Emember;
	}

	/**
	 * @covers WP_Express_Checkout\Integrations\Emember::admin
	 */
	public function testAdmin() {
		$this->assertTrue( !! has_action( 'add_meta_boxes', array( $this->object, 'add_meta_boxes' ) ) );
		$this->assertTrue( !! has_action( 'wpec_save_product_handler', array( $this->object, 'save_product_handler' ) ) );
	}

	/**
	 * @covers WP_Express_Checkout\Integrations\Emember::add_meta_boxes
	 */
	public function testAdd_meta_boxes() {
		$this->assertTrue( empty( $GLOBALS['wp_meta_boxes'][ Products::$products_slug ]['normal']['high']['wpec_emember_meta_box'] ) );
		$this->object->add_meta_boxes();
		$this->assertNotEmpty( $GLOBALS['wp_meta_boxes'][ Products::$products_slug ]['normal']['high']['wpec_emember_meta_box'] );
	}

	/**
	 * @covers WP_Express_Checkout\Integrations\Emember::display_meta_box
	 */
	public function testDisplay_meta_box__no_emember() {
		$product = $this->factory->post->create_and_get(
			[
				'post_type' => Products::$products_slug,
				'meta_input' => [
					'wpec_product_emember_level' => 42,
				]
			]
		);

		$this->expectOutputRegex( '/^((?!42).)*$/' );
		$this->object->display_meta_box( $product );
	}

	/**
	 * @covers WP_Express_Checkout\Integrations\Emember::display_meta_box
	 */
	public function testDisplay_meta_box__reflects() {

		require_once WPEC_TESTS_DIR . '/mocks/mock-emember-functions.php';

		$product = $this->factory->post->create_and_get(
			[
				'post_type' => Products::$products_slug,
				'meta_input' => [
					'wpec_product_emember_level' => 42,
				]
			]
		);

		$this->expectOutputRegex( '(answer to life the universe and everything)' );
		$this->expectOutputRegex( '(selected)' );
		$this->object->display_meta_box( $product );
	}

	/**
	 * @covers WP_Express_Checkout\Integrations\Emember::save_product_handler
	 */
	public function testSave_product_handler() {
		$product_id = $this->factory->post->create( [ 'post_type' => Products::$products_slug ] );

		$this->object->save_product_handler( $product_id );
		$this->assertEquals( '', get_post_meta( $product_id, 'wpec_product_emember_level', true ) );

		$_POST['wpec_product_emember_level'] = 42;
		$this->object->save_product_handler( $product_id );
		$this->assertEquals( '42', get_post_meta( $product_id, 'wpec_product_emember_level', true ) );
	}

}