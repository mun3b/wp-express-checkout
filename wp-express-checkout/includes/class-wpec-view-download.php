<?php
/**
 * Download request handler.
 *
 * Views the requests for the `wpec_download_file` parameter, once one given,
 * triggers the product download process.
 */

/**
 * Download request class.
 */
class WPEC_View_Download {

	/**
	 * The class instance.
	 *
	 * @var WPEC_Process_IPN
	 */
	protected static $instance = null;

	/**
	 * Construct the instance.
	 */
	public function __construct() {
		if ( isset( $_GET['wpec_download_file'] ) && $this->verify_request() ) {
			$this->process_download();
		}
	}

	/**
	 * Retrieves the instance.
	 *
	 * @return WPEC_View_Download
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Checks whether a download request is valid.
	 *
	 * @return boolean
	 */
	private function verify_request() {

		if ( empty( $_GET['wpec_download_file'] ) || empty( $_GET['order_id'] ) || empty( $_GET['key'] ) ) {
			wp_die( esc_html__( 'Invalid download URL!', 'wp-express-checkout' ) );
		}

		$order_id = absint( $_GET['order_id'] );
		$order    = get_post_meta( $order_id, 'ppec_payment_details', true );

		if ( empty( $order ) ) {
			wp_die( esc_html__( 'Invalid Order ID!', 'wp-express-checkout' ) );
		}

		$product = get_post( absint( $_GET['wpec_download_file'] ) );

		if ( empty( $product ) || PPECProducts::$products_slug !== $product->post_type || $order['item_name'] !== $product->post_name ) {
			wp_die( esc_html__( 'Invalid product ID!', 'wp-express-checkout' ) );
		}

		if ( ! $product->ppec_product_upload ) {
			wp_die( esc_html__( 'The product has no file for download!', 'wp-express-checkout' ) );
		}

		$order_timestamp = get_the_time( 'U', $order_id );

		$key  = "{$product->ID}|{$order_id}|{$order_timestamp}";
		$hash = substr( wp_hash( $key ), 0, 20 );

		if ( $_GET['key'] !== $hash ) {
			wp_die( esc_html__( 'Invalid product key!', 'wp-express-checkout' ) );
		}

		return apply_filters( 'wpec_verify_download_product_request', true, $order, $product );
	}

	/**
	 * Processes the product download.
	 */
	private function process_download() {
		$product = get_post( absint( $_GET['wpec_download_file'] ) );

		// clean the fileurl.
		$file_url = stripslashes( trim( $product->ppec_product_upload ) );
		// get filename.
		$file_name = basename( $product->ppec_product_upload );

		// get file extension.
		$file_extension = pathinfo( $file_name );
		$file_new_name  = $file_name;
		$content_type   = '';

		if ( ! isset( $file_extension['extension'] ) ) {
			wp_die( esc_html__( 'Invalid file!', 'wp-express-checkout' ) );
		}

		switch ( $file_extension['extension'] ) {
			case 'png':
				$content_type = 'image/png';
				break;
			case 'gif':
				$content_type = 'image/gif';
				break;
			case 'tiff':
				$content_type = 'image/tiff';
				break;
			case 'jpeg':
			case 'jpg':
				$content_type = 'image/jpg';
				break;
			default:
				$content_type = 'application/force-download';
		}

		$content_type = apply_filters( 'wpec_download_product_content_type', $content_type, $file_extension['extension'] );

		header( 'Expires: 0' );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Cache-Control: pre-check=0, post-check=0, max-age=0', false );
		header( 'Pragma: no-cache' );
		header( "Content-type: {$content_type}" );
		header( "Content-Disposition:attachment; filename={$file_new_name}" );

		readfile( "{$file_url}" );
		exit();
	}

}
