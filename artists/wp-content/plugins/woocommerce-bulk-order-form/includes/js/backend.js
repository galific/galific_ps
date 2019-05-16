jQuery( document ).ready( function () {
	if ( jQuery( '.wc_bof_settings_submenu' ).size() > 0 ) {
		var id = window.location.hash;
		jQuery( '.wc_bof_settings_submenu a' ).removeClass( 'current' );
		jQuery( '.wc_bof_settings_submenu a[href="' + id + '" ]' ).addClass( 'current' );
		if ( id === '' ) {
			jQuery( '.wc_bof_settings_submenu a:first' ).addClass( 'current' );
			id = jQuery( '.wc_bof_settings_submenu a:first' ).attr( 'href' );
		}
		http_reffer = jQuery( 'input[name=_wp_http_referer]' ).val();
		settings_showHash( id );
	}

	jQuery( '.wrap.wc_bof_settings :checkbox' ).each( function () {
		var datalabel  = jQuery( this ).attr( 'data-label' );
		var separator  = jQuery( this ).attr( 'data-separator' );
		var dataulabel = jQuery( this ).attr( 'data-ulabel' );

		jQuery( this ).labelauty( {
			label: true,
			separator: separator,
			checked_label: datalabel,
			unchecked_label: dataulabel,

		} );
	} );

	jQuery( '.wrap.wc_bof_settings :radio' ).each( function () {
		jQuery( this ).labelauty( {
			label: false,
		} );
	} );

	jQuery( '.wc_bof_settings_submenu a' ).click( function () {
		var id = jQuery( this ).attr( 'href' );
		jQuery( '.wc_bof_settings_submenu a' ).removeClass( 'current' );
		jQuery( this ).addClass( 'current' );
		settings_showHash( id );
		jQuery( 'input[name=_wp_http_referer]' ).val( http_reffer + id )
	} );
} );

function settings_showHash ( id ) {
	jQuery( 'div.wc_bof_settings_content' ).hide();
	id = id.replace( '#', '#settings_' );
	jQuery( id ).show();
}

/* Hide Irrelevant Settings */

jQuery( document ).ready( function () {
	jQuery( '#wc_bof_general_wc_bof_template_type' ).on( 'change', function () {
		jQuery( 'table.form-table tr' ).show();
		var chosen_template = jQuery( '#wc_bof_general_wc_bof_template_type' ).val();
		if ( chosen_template === 'prepopulated' ) {
			jQuery( '#wc_bof_general_wc_bof_no_of_rows' ).parents( 'tr' ).hide();
			jQuery( '#wc_bof_general_wc_bof_add_rows' ).parents( 'tr' ).hide();
			jQuery( '#wc_bof_general_wc_bof_single_addtocart' ).parents( 'tr' ).hide();
		}
	} );
} );