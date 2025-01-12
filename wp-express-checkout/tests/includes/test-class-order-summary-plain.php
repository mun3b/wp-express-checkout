<?php

namespace WP_Express_Checkout;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2021-08-06 at 08:34:15.
 *
 * @group shortcodes
 *
 * @covers WP_Express_Checkout\Order_Summary_Plain
 */
class Order_Summary_PlainTest extends \WP_UnitTestCase {

	/**
	 * @var Order_Summary_Plain
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	public function setUp() {
		$product_id = $this->factory->post->create(
			[
				'post_type'  => Products::$products_slug,
				'meta_input' => [
					'ppec_product_upload' => 'dummy'
				]
			]
		);
		$order = Orders::create();
		$order->set_resource_id( "test-resource-id-{$product_id}-{$order->get_id()}" );
		$order->add_data(
			'payer',
			[
				'name' => [
					'given_name' => 'John',
					'surname' => 'Connor',
				],
				'email_address' => 'test@example.com'
			]
		);
		$order->add_data( 'state', 'COMPLETED' );
		$order->add_data( 'shipping_address', 'test shipping address' );
		$order->add_item( Products::$products_slug, $product_id, 42, 2, $product_id );
		$order->add_item( 'coupon', 'Coupon Code: test', -2, 1, 0, false, [ 'code' => "test_$product_id" ] );
		$order->add_item( 'dummy_var', 'Dummy Var', 3, 1, $product_id );

		$order->set_currency( 'TEST' );

		$this->product_id = $product_id;
		$this->order      = $order;
		$this->object = new Order_Summary_Plain( $order );
	}

	/**
	 * @covers WP_Express_Checkout\Order_Summary_Plain::show
	 */
	public function testShow() {
		ob_start();
		$this->object->show();
		$output = ob_get_clean();
		$this->assertEquals( ""
			. "Product Name: {$this->product_id}" . "\n"
			. "Quantity: 2" . "\n"
			. "Price: TEST42.00" . "\n"
			. "Dummy Var: TEST3.00" . "\n"
			. "--------------------------------" . "\n"
			. "Subtotal: TEST87.00" . "\n"
			. "--------------------------------" . "\n"
			. "Coupon Code: test: TEST-2.00" . "\n"
			. "--------------------------------" . "\n"
			. "Total: TEST85.00" . "\n"
			. "",
		$output );
	}
}
