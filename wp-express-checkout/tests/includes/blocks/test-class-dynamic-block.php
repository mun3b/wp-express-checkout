<?php

namespace WP_Express_Checkout\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-07-14 at 07:00:27.
 *
 * @covers WP_Express_Checkout\Blocks\Dynamic_Block
 * @group blocks
 */
class Dynamic_BlockTest extends WP_UnitTestCase {

	/**
	 * @var Dynamic_Block
	 */
	protected $object;

	/**
	 * @var WP_Block_Type_Registry
	 */
	protected $registry;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp() {
		require_once WPEC_TESTS_DIR . '/mocks/mock-dynamic-block.php';
		$this->registry = WP_Block_Type_Registry::get_instance();
		$this->object   = new Dynamic_Block_Test( [ 'test' => true ] );
	}

	public function test__construct() {
		$registered = $this->registry->get_registered( 'wp-express-checkout/dynamic-block-test' );
		$this->assertTrue( $this->registry->is_registered( 'wp-express-checkout/dynamic-block-test' ) );
		$this->assertTrue( $registered->test );
		$this->assertEquals( $registered->render_callback, array( $this->object, 'render_callback' ) );
	}

}
