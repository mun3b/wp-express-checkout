<?php

namespace WP_Express_Checkout;

class Integrations {

	public function __construct() {
		// Simple Membership integration
		if ( defined( 'SIMPLE_WP_MEMBERSHIP_VER' ) ) {
			new Integrations\Simple_WP_Membership();
		}
		// WP eMember integration
		if ( function_exists( 'wp_emember_install' ) ) {
			new Integrations\Emember();
		}
		// License Manager integration.
		if ( defined( 'WP_LICENSE_MANAGER_VERSION' ) ) {
			new Integrations\License_Manager();
		}
		// WooCommerce integration.
		if ( class_exists( 'WC_Payment_Gateway' ) ) {
			add_filter( 'woocommerce_payment_gateways', array( 'WP_Express_Checkout\Integrations\WooCommerce_Gateway', 'add_wc_gateway_class' ) );
		}
	}

}
