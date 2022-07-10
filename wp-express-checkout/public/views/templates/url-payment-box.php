<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta charset="utf-8">
		<title><?php echo esc_html( $a['page_title'] ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php
		require WPEC_PLUGIN_PATH . '/vendor/autoload.php';

		global $wp_scripts;

		$wp_scripts->print_scripts( "jquery" );

		$min = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$scriptFrontEnd = WPEC_PLUGIN_URL . "/assets/js/public{$min}.js";
		$styleFrontEnd = WPEC_PLUGIN_URL . "/assets/css/public{$min}.css";
		$localVars = array(
			'str' => array(
				'errorOccurred' => __( 'Error occurred', 'wp-express-checkout' ),
				'paymentFor' => __( 'Payment for', 'wp-express-checkout' ),
				'enterQuantity' => __( 'Please enter a valid quantity', 'wp-express-checkout' ),
				'stockErr' => __( 'You cannot order more items than available: %d', 'wp-express-checkout' ),
				'enterAmount' => __( 'Please enter a valid amount', 'wp-express-checkout' ),
				'acceptTos' => __( 'Please accept the terms and conditions', 'wp-express-checkout' ),
				'paymentCompleted' => __( 'Payment Completed', 'wp-express-checkout' ),
				'redirectMsg' => __( 'You are now being redirected to the order summary page.', 'wp-express-checkout' ),
				'strRemoveCoupon' => __( 'Remove coupon', 'wp-express-checkout' ),
				'strRemove' => __( 'Remove', 'wp-express-checkout' ),
				'required' => __( 'This field is required', 'wp-express-checkout' ),
			),
			'ajaxUrl' => get_admin_url() . 'admin-ajax.php',
		);
		?>

        <link rel="stylesheet" href="<?php echo $styleFrontEnd ?>" />

        <script type="text/javascript">
			var ppecFrontVars = <?php echo json_encode( $localVars ) ?>
        </script>

        <script src="<?php echo $scriptFrontEnd ?>"></script>

        <style>
            #wp-express-checkout-button-cont .wpec-modal-open {
                display: none;
            }

            #wp-express-checkout-button-cont .wpec-modal-close {
                display: none;
            }

            .wpec-modal-overlay {
                pointer-events: none;
            }

            .wpec-modal-content {
                pointer-events: auto !important;
            }

            #wp-express-checkout-error-product-id {
                text-align: center;
                font-size: 18px;
                background: red;
                color: white;
                top: 40%;
                position: relative;
                width: 300px;
                left: calc(50% - 150px);
            }
        </style>

        <script type="text/javascript">
			window.addEventListener( "load", () => {
				const btnElement = document.getElementsByClassName( "wpec-modal-open" )[0];
				btnElement.click();
			} );
        </script>
	</head>
	<body>
		<?php
		$product_id = filter_input( INPUT_GET, 'product_id', FILTER_SANITIZE_NUMBER_INT );
		echo $product_id;
		
		if ( get_post( $product_id ) ){
			//The product exists.
			echo '<div id="wp-express-checkout-button-cont">';
			echo do_shortcode( sprintf( "[wp_express_checkout product_id='%d']", $product_id ) );
			echo '</div>';
		} else {
			//Product does not exist.
			echo '<div id="wp-express-checkout-error-product-id">';
			echo '<p><strong>Product does not exist.</strong></p>';
			echo '</div>';
		}
		?>

		<?php 
		WP_Express_Checkout\Main::get_instance()->load_paypal_sdk();
		?>
		
	</body>
</html>