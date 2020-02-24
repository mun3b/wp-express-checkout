var ppecHandler = function( data ) {
	this.data = data;
	var parent = this;

	this.processPayment = function( payment ) {
		jQuery.post( ppecFrontVars.ajaxUrl, { action: "wpec_process_payment", wp_ppdg_payment: payment } )
				.done( function( data ) {
					var ret = true;
					var dlgTitle = ppecFrontVars.str.paymentCompleted;
					var dlgMsg = ppecFrontVars.str.redirectMsg;
					try {
						var res = JSON.parse( data );
						var redirect_url = res.redirect_url;
					} catch ( e ) {
						dlgTitle = ppecFrontVars.str.errorOccurred;
						dlgMsg = data;
						ret = false;
					}
					jQuery( 'div#wp-ppdg-dialog-message' ).attr( 'title', dlgTitle );
					jQuery( 'p#wp-ppdg-dialog-msg' ).html( dlgMsg );
					jQuery( 'div.wp-ppec-overlay[data-ppec-button-id="' + parent.data.id + '"]' ).hide();
					if ( redirect_url ) {
						location.href = redirect_url;
					}
					return ret;
				} );
	};

	this.isValidCustomQuantity = function() {
		var input = jQuery( 'input#wp-ppec-custom-quantity[data-ppec-button-id="' + parent.data.id + '"]' );
		var errMsgCont = input.siblings( '.wp-ppec-form-error-msg' );
		var val_orig = input.val();
		var val = parseInt( val_orig );
		var error = false;
		var errMsg = ppecFrontVars.str.enterQuantity;
		// Preserve original quantity.
		if ( parent.data.quantity && ! parent.data.orig_quantity ) {
			parent.data.orig_quantity = parent.data.quantity;
		}
		if ( isNaN( val ) ) {
			error = true;
		} else if ( val_orig % 1 !== 0 ) {
			error = true;
		} else if ( val <= 0 ) {
			error = true;
		} else if ( parent.data.orig_quantity && val > parent.data.orig_quantity ) {
			error = true;
		} else {
			input.removeClass( 'hasError' );
			errMsgCont.fadeOut( 'fast' );
			parent.data.quantity = val;
		}
		if ( error ) {
			input.addClass( 'hasError' );
			errMsgCont.html( errMsg );
			errMsgCont.fadeIn( 'slow' );
		}
		return !error;
	};

	this.isValidCustomAmount = function() {
		var input = jQuery( 'input#wp-ppec-custom-amount[data-ppec-button-id="' + parent.data.id + '"]' );
		var errMsgCont = input.siblings( '.wp-ppec-form-error-msg' );
		var val_orig = input.val();
		var val = parseFloat( val_orig );
		var error = false;
		var errMsg = ppecFrontVars.str.enterAmount;
		if ( ! isNaN( val ) && 0 < val ) {
			input.removeClass( 'hasError' );
			errMsgCont.fadeOut( 'fast' );
			parent.data.price = val;
		} else {
			input.addClass( 'hasError' );
			errMsgCont.html( errMsg );
			errMsgCont.fadeIn( 'slow' );
			error = true;
		}
		return !error;
	};

	if ( this.data.btnStyle.layout === 'horizontal' ) {
		this.data.btnStyle.tagline = false;
	}

	this.clientVars = { };

	this.clientVars[this.data.env] = this.data.client_id;

	this.scCont = jQuery( '.wp-ppec-shortcode-container[data-ppec-button-id="' + parent.data.id + '"]' );

	paypal.Buttons( {
		env: parent.data.env,
		client: parent.clientVars,
		style: parent.data.btnStyle,
		commit: true,
		onInit: function( data, actions ) {
			var enable_actions = true;
			if ( parent.data.custom_quantity === "1" ) {
				jQuery( 'input#wp-ppec-custom-quantity[data-ppec-button-id="' + parent.data.id + '"]' ).change( function() {
					if ( ! parent.isValidCustomQuantity() ) {
						enable_actions = false;
					}
				} );
			}
			if ( parent.data.custom_amount === "1" ) {
				jQuery( 'input#wp-ppec-custom-amount[data-ppec-button-id="' + parent.data.id + '"]' ).change( function() {
					if ( ! parent.isValidCustomAmount() ) {
						enable_actions = false;
					}
				} );
			}
			if ( enable_actions ) {
				actions.enable();
			} else {
				actions.disable();
			}
		},
		onClick: function() {
			var errInput = parent.scCont.find( '.hasError' ).first();
			if ( errInput ) {
				errInput.focus();
				errInput.trigger( 'change' );
			}
		},
		createOrder: function( data, actions ) {
			parent.data.total = parent.data.price * parent.data.quantity;
			return actions.order.create( {
				purchase_units: [ {
						amount: {
							value: parent.data.total,
							currency_code: parent.data.currency,
							breakdown: {
								item_total: {
									currency_code: parent.data.currency,
									value: parent.data.total
								}
							}
						},
						items: [ {
								name: parent.data.name,
								quantity: parent.data.quantity,
								unit_amount: {
									value: parent.data.price,
									currency_code: parent.data.currency
								}
							} ]
					} ]
			} );
		},
		onApprove: function( data, actions ) {
			jQuery( 'div.wp-ppec-overlay[data-ppec-button-id="' + parent.data.id + '"]' ).css( 'display', 'flex' );
			return actions.order.capture().then( function( details ) {
				parent.processPayment( details );
			} );
		},
		onError: function( err ) {
			alert( err );
		}
	} ).render( '#' + parent.data.id );
};
