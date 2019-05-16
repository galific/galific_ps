jQuery(document).ready(function($){
	
	
	$(document).on('click', '.row-actions .uv_action', function() {
		
		if( ! confirm( L10n_user_verification.confirm_text ) ) return;
		
		_user_id 	= $(this).attr( 'user_id' );
		_do 		= $(this).attr( 'do' );
		
		$(this).parent().prev().html( L10n_user_verification.text_updateing + ' <i class="fa fa-spin fa-cog"></i>' );
		
		$.ajax(
			{
		type: 'POST',
		context: this,
		url:uv_ajax.uv_ajaxurl,
		data: {
			"action"	: "uv_ajax_approve_user_manually", 
			"user_id"	: _user_id, 
			"do"		: _do, 
		},
		success: function(data){
			
			if( _do == 'approve' ) {
				
				$(this).text( L10n_user_verification.text_remove_approve );
				$(this).attr( 'do', 'remove_approval' );
				$(this).removeClass( 'uv_approve' );
				$(this).addClass( 'uv_remove_approval' );
			}
			
			if( _do == 'remove_approval' ) {
				
				$(this).text( L10n_user_verification.text_approve_now );
				$(this).attr( 'do', 'approve' );
				$(this).removeClass( 'uv_remove_approval' );
				$(this).addClass( 'uv_approve' );
			}
			
			if( data.length > 0 ) $(this).parent().prev().html( data );
		}
			});		
	})
		


		
		$(document).on('click', '.reset-email-templates', function()
			{

				if(confirm( L10n_user_verification.reset_confirm_text )){
					
					$.ajax(
						{
					type: 'POST',
					context: this,
					url:uv_ajax.uv_ajaxurl,
					data: {"action": "user_verification_reset_email_templates", },
					success: function(data)
							{	
							
								$(this).val('Reset Done');
							
								location.reload();
							}
						});
					
					}

				})




	});	







