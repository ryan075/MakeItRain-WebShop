jQuery( document ).ready( function( $ ) {

	/* show or hide box if cookie set */
	if ( 'accepted' !== Cookies.get('agy') || 'on' === options.is_debug ) {
		$( '.agy' ).css( 'display', 'table' );
		$( '.overlay-verify' ).css( 'display', 'block' );
	} else {
		$( options.blur_container ).css('filter', 'blur(0px)');
	}

	/* exit button */
	$( '#agy-exit' ).on( 'click',function() {
			window.location.href = options.exit_url;
	} );

	/* age verification modes */

	/* yes or no */
	if ( 'age-submit' == options.display_mode ) {
		$( '#agy-accept' ).on( 'click',function() {
			$( '.agy' ).hide();
			$( '.overlay-verify' ).hide();
			/* set cookie */
			if( 'on' !== options.is_debug ) {
				Cookies.set('agy', 'accepted', { expires: parseInt( options.cookie_lifetime ) });
			}
			/* remove blur */
			$( options.blur_container ).css('filter', 'blur(0px)');
		} );
	}

	/* date selection */
	if ( 'age-select' == options.display_mode ) {
		$( '#age-check' ).on( 'click', ( function() {

			var day    = $( '#agy-day' ).val();
			var month  = $( '#agy-month' ).val();
			var year   = $( '#agy-year' ).val();
			var age    = options.age;
			var mydate = new Date();

			mydate.setFullYear( year, month - 1, day);

			var currdate = new Date();

			currdate.setFullYear( currdate.getFullYear() - age );

			/* check if selection is empty */
			if( '' == day || '' == month  ||'' == year ) {
				$( '.age-message' ).html( '<p>' + options.error_message + '</p>' );
				return false;
			}
		
			/* check if date is valid */
			if ( ( currdate - mydate ) < 0 )  {
				$( '.age-message' ).html( '<p>' + options.error_message + '</p>' );
				return false;
			}

			$( '.agy' ).hide();
			$( '.overlay-verify' ).hide();
			/* set cookie */
			if( 'on' !== options.is_debug ) {
				Cookies.set('agy', 'accepted', { expires: parseInt( options.cookie_lifetime ) });
			}
			/* remove blur */
			$( options.blur_container ).css('filter', 'blur(0px)');
		
		} ) );
	}

	/* range slider */
	if ( 'age-slider' == options.display_mode ) {
		
		var rangeSlider = function(){
			var slider = $('.range-slider'),
				range = $('.range-slider__range'),
				value = $('.range-slider__value');
				
			slider.each(function(){
				value.each(function(){
				var value = $(this).prev().attr('value');
				$(this).html(value);
				});

				range.on('input', function(){
				$(this).next(value).html(this.value);
				});
			});
		};

		rangeSlider();

		$( '#age-check' ).on( 'click', ( function() {
				var age = $('#rangeslider').val();

				if( age >= options.age ) {
					$( '.agy' ).hide();
					$( '.overlay-verify' ).hide();
					/* set cookie */
					if( 'on' !== options.is_debug ) {
						Cookies.set('agy', 'accepted', { expires: parseInt( options.cookie_lifetime ) });
					}
					/* remove blur */
					$( options.blur_container ).css('filter', 'blur(0px)');
				} else {
					$( '.age-message' ).html( '<p>' + options.error_message + '</p>' );
				}
			})
		);
	}

	/* check if IE */
	if( navigator.userAgent.indexOf('MSIE')!==-1 || navigator.appVersion.indexOf('Trident/') > 0 ) {
		$('.agy .box').css('width','100%');
		$('.agy .box').css('height','55%');
		$('.agy .box').css('display','block');
	}

	// check if #place_order needs to be hidden
	$( document ).on('updated_checkout', function() {
		if ( 'yes' === options.block_checkout ) {
			$('#place_order').hide();
		} else {
			$('#ident_age').hide();
		}
	});
	/* Ajax for age verification */
	$( document ).on('click', '#ident_age', function() {

		// find country id.
		if ( $( "#billing_country" ).length ) {
			var country_id = $('#billing_country').val();
		} else {
			var country_id = $( "#billing_country option:selected" ).val();
		}

		let data = {
			'action'             : 'request_verification',
			'first_name'         : $('#billing_first_name').val(),
			'last_name'          : $('#billing_last_name').val(),
			'street'             : $('#billing_address_1').val(),
			'city'               : $('#billing_city').val(),
			'zipcode'            : $('#billing_postcode').val(),
			'address_country_id' : country_id,
		}

		$.ajax({
			type: 'POST',
			url: options.ajax_url,
			data: data,
			dataType: 'json',
			success: function(data) {
				var verification_cookie = data.cookies;
				var ident_token = verification_cookie[0]['value'];
				window.location.href = 'https://www.sofort.com/payment/agecheck/go/login?SOFUEB=' + ident_token;
			}
		});
    });
} );
