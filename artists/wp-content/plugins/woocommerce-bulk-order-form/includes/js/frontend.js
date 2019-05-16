jQuery( function ( $ ) {
	WCBulkOrder.decmultiple = '1';

	while ( WCBulkOrder.decmultiple.length <= WCBulkOrder.num_decimals ) {
		WCBulkOrder.decmultiple += '0';
	}

	jQuery( '.add-to-cart-single' ).hide();

	jQuery( '.wcbulkorderform' ).each( function () {
		var id     = jQuery( this ).attr( 'id' );
		var formid = jQuery( this ).attr( 'data-formid' );
		var newval = new wc_bof_handler();
		newval.init( jQuery( this ), formid );
		wcbof_forms[ formid ] = newval;
		newval                = '';
	} );

	jQuery( 'table.wcbulkorderproducttbl' ).each( function () {
		var cols       = jQuery( this ).find( 'thead tr td' ).length;
		var actionscol = cols - 1;
		jQuery( this ).find( 'tr.wcbulkorderactions td:last' ).attr(
			'colspan', actionscol );
		jQuery( this ).find( 'tr.wcbulkordertotals td:first' ).attr(
			'colspan', actionscol );
	} );

	var $wcbulkorderform = jQuery( 'form.wcbulkorderform' );
	
	var $body = jQuery( 'body' );

	$wcbulkorderform.on( 'click', '.wcbofaddrow', function () {
		var formid = jQuery( this ).attr( 'data-formid' );
		var form   = get_wc_bof( formid );
		form.add_row( formid, jQuery( this ) );
	} );

	$wcbulkorderform.on( 'change click', 'input.product_qty', function () {
		var formid  = jQuery( this ).parent().parent().attr(
			'data-formid' );
		var form    = get_wc_bof( formid );
		var clicked = jQuery( this );

		if ( form.settings.template === 'standard' ) {
			form.calculate_standard_template_price( formid,
				jQuery( this ) )
		} else if ( form.settings.template === 'variation' ) {
			form.calculate_variation_template_price( formid,
				jQuery( this ) )
		} else {
			jQuery( "body" ).trigger( "wc_bof_single_qty_update",
				[ form.settings.template, clicked, form ] );
		}

	} );

	$wcbulkorderform.on( 'found_variation', '.variations_form', function ( event, variation ) {
		var formid  = jQuery( this ).attr( 'data-formid' );
		var form    = get_wc_bof( formid );
		var clicked = jQuery( this );
		form.map_select_variation( formid, clicked, variation );
	} );

	$wcbulkorderform.on( 'click', '.reset_variations', function ( event ) {
		var formid  = jQuery( this ).parent().attr( 'data-formid' );
		var form    = get_wc_bof( formid );
		var clicked = jQuery( this );
		form.reset_variations( formid, clicked, event );
	} );

	$wcbulkorderform.on( 'reset_data', '.variations_form', function ( event, variation ) {
		if ( !variation ) {
			var row     = jQuery( this ).closest( 'tr' );
			var formid  = jQuery( this ).attr( 'data-formid' );
			var form    = get_wc_bof( formid );
			var clicked = jQuery( this ).find( '.reset_variations' );
			form.reset_variations( formid, clicked, event );
			row.find( '.product_qty' ).val( '' );
			row.find( '.wc_bof_product_price .amount').empty();
		}
	} );

	$wcbulkorderform.on( 'click', 'a.add-to-cart-single', function () {
		var formid  = jQuery( this ).attr( 'data-formid' );
		var form    = get_wc_bof( formid );
		var clicked = jQuery( this );
		form.single_add_to_cart_handler( formid, clicked );

	} );

	$wcbulkorderform.on( 'click', '.wcbofaddtocart', function ( event ) {
		event.preventDefault();
		var formid  = jQuery( this ).attr( 'data-formid' );
		var form    = get_wc_bof( formid );
		var clicked = jQuery( this );
		form.add_to_cart_handler( formid, clicked, event );
	} );

	$body.on( 'wc_bof_on_item_add', function ( type, trigger_id, productID, itemDATA, selectbox ) {
		var form = get_wc_bof( trigger_id );
		form.trigger_item_add( trigger_id, productID, itemDATA,
			selectbox );
	} );

	$body.on( 'wc_bof_single_added_to_cart', function ( clickElem, inputRow, Response ) {
		jQuery( 'body' ).trigger( {
			type: 'added_to_cart'
		} );
		jQuery( document.body ).trigger( 'wc_fragment_refresh' );
	} );

	$body.on( 'wc_bof_added_to_cart', function ( clickElem, inputRow, Response ) {
		jQuery( 'body' ).trigger( {
			type: 'added_to_cart'
		} );
		jQuery( document.body ).trigger( 'wc_fragment_refresh' );
	} );

	jQuery( '.product_name_search_field' ).each( function () {
		var count = jQuery( this ).parent().attr( 'data-count' );
		if ( count === 'removeHidden' ) {
		} else {
			wc_bof_init_selectize( jQuery( this ) );
		}
	} );
} );

function wc_bof_init_selectize ( selectbox ) {
	var formid           = selectbox.parent().parent().attr( 'data-formid' );
	var bof_class        = wcbof_forms[ formid ];
	var bof_form         = wcbof_forms[ formid ].form;
	var already_searched = false;
	var enable_score     = false;
	var last_used_query  = '';
	var bofxhr           = null;
	selectbox.selectize( {
		valueField: 'id',
		labelField: 'label',
		searchField: 'label',
		create: false,
		preload: 'focus',
		plugins: [ 'restore_on_backspace', 'remove_button' ],
		onItemAdd: function ( productID ) {
			var itemDATA = this.options[ productID ];
			var formid   = selectbox.parent().parent().attr( 'data-formid' );
			jQuery( 'body' ).trigger( 'wc_bof_on_item_add', [ formid, productID, itemDATA, selectbox ] );
		},
		onType: function () {
			already_searched = false;
			enable_score     = false;
		},
		score: function ( search ) {
			var score = this.getScoreFunction( search );
			return function ( item ) {
				var s = score( item );
				if ( s === 0 ) {
					return 1;
				}
				return s * 1;
				// return score(item) * (1 + Math.min(item.watchers /
				// 100, 1));
			};
		},
		render: {
			option: function ( item, escape ) {
				if ( WCBulkOrder.display_images ) {
					return '<div class="wcbofprod_list"><span class="image"> <img src="'
						+ item.imgsrc
						+ '" /></span><span class="title">'
						+ item.label + '</span></div>';
				} else {
					return '<div class="wcbofprod_list"><span class="title">'
						+ item.label + '</span></div>';
				}

			}
		},
		load: function ( query, callback ) {
			// require minimum number of characters to execute query (default = 3)
			if ( !query || query.length < WCBulkOrder.minLength ) {
				callback();
				return;
			}
			// clear options from previous searches
			this.clearOptions();
			this.refreshItems();
			if ( ( already_searched === true && query === '' && last_used_query !== '' ) ) {
				callback();
			} else {
				if ( query !== '' ) {
					already_searched = true;
				}

				POSTDATAS = 'action=wcbulkorder_product_search&term=' + query + '&';
				POSTDATAS = POSTDATAS + bof_form.find( ".form_hidden_fileds :input" ).serialize();
				bofxhr    = jQuery.ajax( {
					url: WCBulkOrder.url,
					type: 'POST',
					data: POSTDATAS,
					beforeSend: function () {
						if ( bofxhr != null ) {
							bofxhr.abort();
						}
					},
					error: function () {
						callback();
					},
					success: function ( res ) {
						last_used_query = query;
						callback( res );
					}
				} );
			}
		}
	} )
}

function get_wc_bof ( formid ) {
	if ( wcbof_forms[ formid ] === undefined ) {
		return false;
	} else {
		return wcbof_forms[ formid ];
	}
}

function number_format ( number, decimals, dec_point, thousands_sep ) {
	// http://kevin.vanzonneveld.net
	// + original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// + bugfix by: Michael White (http://getsprink.com)
	// + bugfix by: Benjamin Lupton
	// + bugfix by: Allan Jensen (http://www.winternet.no)
	// + revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	// + bugfix by: Howard Yeend
	// + revised by: Luke Smith (http://lucassmith.name)
	// + bugfix by: Diogo Resende
	// + bugfix by: Rival
	// + input by: Kheang Hok Chin (http://www.distantia.ca/)
	// + improved by: davook
	// + improved by: Brett Zamir (http://brett-zamir.me)
	// + input by: Jay Klehr
	// + improved by: Brett Zamir (http://brett-zamir.me)
	// + input by: Amir Habibi (http://www.residence-mixte.com/)
	// + bugfix by: Brett Zamir (http://brett-zamir.me)
	// + improved by: Theriault
	// + input by: Amirouche
	// + improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// * example 1: number_format(1234.56);
	// * returns 1: '1,235'
	// * example 2: number_format(1234.56, 2, ',', ' ');
	// * returns 2: '1 234,56'
	// * example 3: number_format(1234.5678, 2, '.', '');
	// * returns 3: '1234.57'
	// * example 4: number_format(67, 2, ',', '.');
	// * returns 4: '67,00'
	// * example 5: number_format(1000);
	// * returns 5: '1,000'
	// * example 6: number_format(67.311, 2);
	// * returns 6: '67.31'
	// * example 7: number_format(1000.55, 1);
	// * returns 7: '1,000.6'
	// * example 8: number_format(67000, 5, ',', '.');
	// * returns 8: '67.000,00000'
	// * example 9: number_format(0.9, 0);
	// * returns 9: '1'
	// * example 10: number_format('1.20', 2);
	// * returns 10: '1.20'
	// * example 11: number_format('1.20', 4);
	// * returns 11: '1.2000'
	// * example 12: number_format('1.2000', 3);
	// * returns 12: '1.200'
	// * example 13: number_format('1 000,50', 2, '.', ' ');
	// * returns 13: '100 050.00'
	// Strip all characters but numerical ones.
	number                                           = ( number + '' ).replace( /[^0-9+\-Ee.]/g, '' );
	var n = !isFinite( +number ) ? 0 : +number, prec = !isFinite( +decimals ) ? 0
		: Math.abs( decimals ), sep                  = ( typeof thousands_sep === 'undefined' ) ? ','
		: thousands_sep, dec                         = ( typeof dec_point === 'undefined' ) ? '.'
		: dec_point, s                               = '', toFixedFix              = function ( n, prec ) {
		var k = Math.pow( 10, prec );
		return '' + Math.round( n * k ) / k;
	};
	// Fix for IE parseFloat(0.55).toFixed(0) = 0;
	s                                                = ( prec ? toFixedFix( n, prec ) : '' + Math.round( n ) ).split( '.' );
	if ( s[ 0 ].length > 3 ) {
		s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
	}
	if ( ( s[ 1 ] || '' ).length < prec ) {
		s[ 1 ] = s[ 1 ] || '';
		s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
	}
	return s.join( dec );
}