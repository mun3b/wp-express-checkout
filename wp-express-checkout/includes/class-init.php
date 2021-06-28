<?php

namespace WP_Express_Checkout;

use WP_Express_Checkout\Admin\Orders_List;
use WP_Express_Checkout\Debug\Logger;

/*
 * This class handles various init time (init hook) tasks.
 */

class Init {

	public function __construct() {

		add_action( 'init', array( $this, 'do_init_time_tasks' ) );
	}

	public function do_init_time_tasks() {
		/*
		 * General init time tasks
		 */

		//Register the post types
		$products = Products::get_instance();
		$products->register_post_type();

		$orders = Orders::get_instance();
		$orders->register_post_type();

		add_action( 'wp_ajax_wpec_reset_log', array( $this, 'wpec_handle_reset_log' ) );

		if ( is_admin() ) {
			/*
			 * Do admin side only tasks
			 */

			$this->handle_view_log_action();
			Orders_List::init();
		} else {
			/*
			 * Front-end only tasks
			 */

			//NOP
		}
	}

	public function wpec_handle_reset_log() {
		Logger::reset_log();
		echo '1';
		wp_die();
	}

	public function handle_view_log_action() {

		if ( user_can( wp_get_current_user(), 'administrator' ) ) {
			// user is an admin
			if ( isset( $_GET['wpec-debug-action'] ) ) {
				if ( $_GET['wpec-debug-action'] === 'view_log' ) {
					$logfile = fopen( WPEC_LOG_FILE, 'rb' );
					header( 'Content-Type: text/plain' );
					fpassthru( $logfile );
					die;
				}
			}
		}
	}

}