<?php
/**
 * Plugin Name:       WP Express Checkout
 * Description:       This plugin allows you to generate a customizable PayPal payment button that lets the customers pay quickly in a popup via PayPal.
 * Version:           1.3
 * Author:            Tips and Tricks HQ
 * Author URI:        https://www.tipsandtricks-hq.com/
 * Plugin URI:        https://wp-express-checkout.com/
 * License:           GPL2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

//slug wpec_

if ( ! defined( 'ABSPATH' ) ) {
    exit; //Exit if accessed directly
}

//Define constants
define( 'WPEC_PLUGIN_VER', '1.3' );
define( 'WPEC_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WPEC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPEC_PLUGIN_FILE', __FILE__ );
define( 'WPEC_LOG_FILE', WPEC_PLUGIN_PATH . 'wpec-debug-log.txt' );

/* ----------------------------------------------------------------------------*
 * Includes
 * ---------------------------------------------------------------------------- */
include_once( WPEC_PLUGIN_PATH . 'includes/class-wpec-utility-functions.php');
include_once( WPEC_PLUGIN_PATH . 'includes/class-wpec-debug-logger.php');
include_once( WPEC_PLUGIN_PATH . 'includes/class-wpec-init-time-tasks.php');
include_once( WPEC_PLUGIN_PATH . 'includes/class-wpec-process-ipn.php');

require_once( WPEC_PLUGIN_PATH . 'public/class-wpec-main.php' );
require_once( WPEC_PLUGIN_PATH . 'public/includes/class-shortcode-ppec.php' );

require_once( WPEC_PLUGIN_PATH . 'admin/includes/class-products.php' );
require_once( WPEC_PLUGIN_PATH . 'admin/includes/class-order.php' );

//Load admin side class
if ( is_admin() ) {
    require_once( WPEC_PLUGIN_PATH . 'admin/class-wpec-admin.php' );
    add_action( 'plugins_loaded', array( 'WPEC_Admin', 'get_instance' ) );
}

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 */
register_activation_hook( __FILE__, array( 'WPEC_Main', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'WPEC_Main', 'deactivate' ) );

//Plugins loaded hook
add_action( 'plugins_loaded', array( 'WPEC_Main', 'get_instance' ) );
add_action( 'plugins_loaded', array( 'WPECShortcode', 'get_instance' ) );

/*
 * Do init time tasks
 */
$init_time_tasks = new WPEC_Init_Time_Tasks();

/*
 * Listen and handle payment processing. IPN handling.
 */
WPEC_Process_IPN::get_instance();
