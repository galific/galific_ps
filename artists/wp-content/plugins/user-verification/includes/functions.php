<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 









function user_verification_is_verified($userid){

    $status = get_user_meta($userid, 'user_activation_status', true);

    if ( $status == 1 ){
        return true;

    }else{
        return false;
    }

}






add_filter('bulk_actions-users','user_verification_bulk_approve');
function user_verification_bulk_approve($actions){
	//unset( $actions['delete'] );

	$actions['uv_bulk_approve'] = __('Approve', 'user-verification');
	$actions['uv_bulk_disapprove'] = __('Disapprove', 'user-verification');

	return $actions;
}





add_filter( 'handle_bulk_actions-users', 'user_verification_bulk_approve_handler', 10, 3 );
function user_verification_bulk_approve_handler( $redirect_to, $doaction, $items ) {

	if ( $doaction == 'uv_bulk_approve' ){

		foreach ( $items as $user_id ) {
			// Perform action for each post.
			update_user_meta( $user_id, 'user_activation_status', 1 );

		}

		$redirect_to = add_query_arg( 'uv_bulk_approve', count( $items ), $redirect_to );
	}
	elseif ($doaction =='uv_bulk_disapprove'){

		foreach ( $items as $user_id ) {
			// Perform action for each post.
			update_user_meta( $user_id, 'user_activation_status', 0 );

		}

		$redirect_to = add_query_arg( 'uv_bulk_disapprove', count( $items ), $redirect_to );

	}


	return $redirect_to;

}



add_action( 'admin_notices', 'user_verification_bulk_action_admin_notice' );
function user_verification_bulk_action_admin_notice() {
	if ( isset($_REQUEST['uv_bulk_approve']) ) {

		$user_count = intval( $_REQUEST['uv_bulk_approve'] );

		echo '<div id="message" class="notice notice-success is-dismissible">';
		echo sprintf(__('%s user account marked as approved.'), $user_count);
		echo '</div>';

	}
	elseif (isset($_REQUEST['uv_bulk_disapprove'])){

		$user_count = intval( $_REQUEST['uv_bulk_disapprove'] );

		echo '<div id="message" class="notice notice-success is-dismissible">';
		echo sprintf(__('%s user account marked as disapproved.'), $user_count);
		echo '</div>';


	}
}



function uv_ajax_approve_user_manually(){
	
	$user_id 	= isset( $_POST['user_id'] ) ? $_POST['user_id'] : '';
	$do 		= isset( $_POST['do'] ) ? $_POST['do'] : '';
	
	if( empty( $user_id ) || empty( $do ) ) die();
	
	if( $do == 'approve' ) update_user_meta( $user_id, 'user_activation_status', 1 );
	if( $do == 'remove_approval' ) update_user_meta( $user_id, 'user_activation_status', 0 );
	
	$user_activation_status = get_user_meta( $user_id, 'user_activation_status', true );
	$user_activation_status = empty( $user_activation_status ) ? 0 : $user_activation_status;
	$uv_status 				= $user_activation_status == 1 ? __('Approved', 'user-verification') : __('Pending approval', 'user-verification');
	
	if( $user_activation_status == 1 ){
		
		$user_data 	= get_userdata( $user_id );
		uv_mail( $user_data->user_email, array(
			'action' => 'email_confirmed',
			'user_id' => $user_id,
		) );
	}
	
	echo $uv_status;
	die();
}
add_action('wp_ajax_uv_ajax_approve_user_manually', 'uv_ajax_approve_user_manually');
add_action('wp_ajax_nopriv_uv_ajax_approve_user_manually', 'uv_ajax_approve_user_manually');





















function user_verification_is_username_blocked($username){

    $response = false;
    $user_verification_enable_block_username 	= get_option('user_verification_enable_block_username');
    $uv_settings_blocked_username 				= get_option('uv_settings_blocked_username');

    if( $user_verification_enable_block_username == "yes" ):
        foreach( $uv_settings_blocked_username as $blocked ){
            $status = preg_match("/$blocked/", $username);
            if($status == 1):
                $response = true;
                break;
            endif;
        }
    endif;

    return $response;
}




function user_verification_is_emaildomain_blocked($user_email){

    $response = false;
    $user_verification_enable_block_domain 		= get_option('user_verification_enable_block_domain', 'no');
    $uv_settings_blocked_domain 				= get_option('uv_settings_blocked_domain', array());

    if( $user_verification_enable_block_domain == "yes" ):

        $email_domain = explode('@', $user_email);

        if( !empty( $email_domain ) && in_array( $email_domain[1], $uv_settings_blocked_domain ) ){
            $response = true;
        }

    endif;


    return $response;
}








add_filter( 'registration_errors', 'uv_registration_protect_username', 10, 3 );
function uv_registration_protect_username( $errors, $sanitized_user_login, $user_email ){

    $is_blocked = user_verification_is_username_blocked($sanitized_user_login);
    if($is_blocked){
        $errors->add( 'blocked_username', __( "<strong>{$sanitized_user_login}</strong> username is not allowed!", 'user-verification' ));
    }
    return $errors;

}





add_filter( 'registration_errors', 'uv_registration_protect_blocked_domain', 10, 3 );
function uv_registration_protect_blocked_domain( $errors, $sanitized_user_login, $user_email ){

    $is_blocked = user_verification_is_emaildomain_blocked($user_email);
    if($is_blocked){
        $errors->add( 'blocked_domain', __( "This email domain is not allowed!", 'user-verification' ) );
    }
    return $errors;

}







add_filter( 'wp_login_errors', 'user_verification_registered_message', 10, 2 );

function user_verification_registered_message( $errors, $redirect_to ) {

	$user_verification_registered_message = get_option('user_verification_registered_message');

	if( isset( $errors->errors['registered'] ) ) {
		
		$tmp = $errors->errors;

		$old = 'Registration complete. Please check your email.';
		$new = $user_verification_registered_message;

		foreach( $tmp['registered'] as $index => $msg ){
			if( $msg === $old )
			$tmp['registered'][$index] = $new;
		}
		$errors->errors = $tmp;

		unset( $tmp );
	}
	
	return $errors;
}





function user_verification_get_pages_list(){
	$array_pages['none'] = __('None', 'user-verification');

	$args = array(
		'sort_order' => 'asc',
		'sort_column' => 'post_title',
		'hierarchical' => 1,
		'exclude' => '',
		'include' => '',
		'meta_key' => '',
		'meta_value' => '',
		'authors' => '',
		'child_of' => 0,
		'parent' => -1,
		'exclude_tree' => '',
		'number' => '',
		'offset' => 0,
		'post_type' => 'page',
		'post_status' => 'publish,private'
	);
	$pages = get_pages($args);

    //$array_pages[0] = 'None';

    foreach( $pages as $page ){
        if ( $page->post_title ) $array_pages[$page->ID] = $page->post_title;
    }


    return $array_pages;
}


function user_verification_reset_email_templates( ) {
		
	if(current_user_can('manage_options')){
		delete_option('uv_email_templates_data');
	}
}	
add_action('wp_ajax_user_verification_reset_email_templates', 'user_verification_reset_email_templates');
add_action('wp_ajax_nopriv_user_verification_reset_email_templates', 'user_verification_reset_email_templates');
	
function uv_filter_check_activation() {
	
	
	$uv_message_invalid_key = get_option( 'uv_message_invalid_key' );
	if( empty( $uv_message_invalid_key ) ) 
	$uv_message_invalid_key = __( 'Invalid activation Key', 'user-verification' );
	
	$uv_message_key_expired = get_option( 'uv_message_key_expired' );
	if( empty( $uv_message_key_expired ) ) 
	$uv_message_key_expired = __( 'Your key is expired', 'user-verification' );
	
	$uv_message_verification_success = get_option( 'uv_message_verification_success' );
	if( empty( $uv_message_verification_success ) ) 
	$uv_message_verification_success = __( 'Your account is now verified', 'user-verification' );
	
	$uv_message_activation_sent = get_option( 'uv_message_activation_sent' );
	if( empty( $uv_message_activation_sent ) ) 
	$uv_message_activation_sent = __( 'Activation email sent, Please check latest item on email inbox', 'user-verification' );

    $user_verification_login_automatically = get_option( 'user_verification_login_automatically', 'no' );


	
    $html = '<div class="user-verification check">';

	if( isset( $_GET['activation_key'] ) ){
		$activation_key = sanitize_text_field($_GET['activation_key']);
		global $wpdb;
		$table = $wpdb->prefix . "usermeta";
		$meta_data	= $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE meta_value = %s", $activation_key ) );
		if( empty( $meta_data ) ) {
			$html.= "<div class='wrong-key'><i class='fas fa-times'></i> $uv_message_invalid_key</div>";
		}
		else{
			$user_activation_status = get_user_meta( $meta_data->user_id, 'user_activation_status', true );
			if( $user_activation_status != 0 ) {
				$html.= "<div class='expired'><i class='far fa-calendar-times'></i> $uv_message_key_expired</div>";
			}
            else {
                $user_verification_redirect_verified = get_option('user_verification_redirect_verified');
                if($user_verification_redirect_verified=='none'){
	                $redirect_page_url = '';
                }else{
	                $redirect_page_url = get_permalink($user_verification_redirect_verified);
                }


				$html.= "<div class='verified'><i class='fas fa-check-square'></i> $uv_message_verification_success</div>";
                update_user_meta( $meta_data->user_id, 'user_activation_status', 1 );

                $user_data = get_userdata( $meta_data->user_id );
                uv_mail( $user_data->user_email, array( 'action' => 'email_confirmed', 'user_id' => $meta_data->user_id, ) );

                if( $user_verification_login_automatically ==  "yes"  ){


					$user = get_user_by( 'id', $meta_data->user_id );


					//var_dump($user);


					wp_set_current_user( $meta_data->user_id, $user->user_login );
					//wp_set_auth_cookie( $meta_data->user_id );
					//do_action( 'wp_login', $user->user_login );
					$redirect_page_url = $redirect_page_url.'?uv_autologin=yes&key='.$activation_key;

				}
				
				if(($user_verification_redirect_verified != 'none')):
					$html.= "<script>jQuery(document).ready(function($){window.location.href = '$redirect_page_url';})</script>";
				else:
				endif;
			}
		}
	}

	elseif (isset( $_GET['uv_action']) && isset($_GET['id'])){

            $uv_action = sanitize_text_field($_GET['uv_action']);
            $user_id = (int) sanitize_text_field($_GET['id']);

            if($uv_action=='resend'):

                $user_activation_key = md5(uniqid('', true) );

                update_user_meta( $user_id, 'user_activation_key', $user_activation_key );

                $user_verification_verification_page = get_option('user_verification_verification_page');
                $verification_page_url = get_permalink($user_verification_verification_page);

                $user_data 	= get_userdata( $user_id );
                $link 		= $verification_page_url.'?activation_key='.$user_activation_key;
				
                uv_mail(
                    $user_data->user_email,
                    array(
                        'action' 	=> 'email_resend_key',
                        'user_id' 	=> $user_id,
                        'link'		=> $link
                    )
                );

                $html.= "<div class='resend'><i class='fas fa-paper-plane'></i> $uv_message_activation_sent</div>";
				
				
            endif;
        }
        else $html.= "<i class='fas fa-exclamation-triangle'></i> $uv_message_invalid_key";
    

		$html.= '</div>';
		return $html;
	}	

add_shortcode('user_verification_check', 'uv_filter_check_activation');



add_shortcode('user_verification_message', 'uv_filter_check_status');

function uv_filter_check_status($attr) {

    $uv_check = isset($_GET['uv_check']) ? $_GET['uv_check'] : '';

    $msg = isset($attr['message']) ? $attr['message'] : 'Please check email to get verify frist.';
    if(is_user_logged_in() && $uv_check == 'true'){
        $userid = get_current_user_id();
        $status = user_verification_is_verified($userid);

        if(!$status){
            $html = $msg;
            wp_logout();
            return $html;
        }


    }


}



add_shortcode('uv_resend_verification_form', 'uv_resend_verification_form');


function uv_resend_verification_form($attr){

	ob_start();


	if(!empty($_POST['resend_verification_hidden'])){

		$nonce = $_POST['_wpnonce'];


		if(wp_verify_nonce( $nonce, 'nonce_resend_verification' ) && $_POST['resend_verification_hidden'] == 'Y') {

			$html = '';

			$email = sanitize_email($_POST['email']);

			$user_data = get_user_by('email', $email);

			if(!empty($user_data)):

				$user_id = $user_data->ID;

				$user_activation_key = md5(uniqid('', true) );

				update_user_meta( $user_id, 'user_activation_key', $user_activation_key );


				$uv_message_activation_sent = get_option( 'uv_message_activation_sent' );
				if( empty( $uv_message_activation_sent ) )
					$uv_message_activation_sent = __( 'Activation email sent, Please check latest item on email inbox', 'user-verification' );

				$user_verification_verification_page = get_option('user_verification_verification_page');
				$verification_page_url = get_permalink($user_verification_verification_page);

				$user_data 	= get_userdata( $user_id );
				$link 		= $verification_page_url.'?activation_key='.$user_activation_key;

				uv_mail(
					$user_data->user_email,
					array(
						'action' 	=> 'email_resend_key',
						'user_id' 	=> $user_id,
						'link'		=> $link
					)
				);

				$html.= "<div class='resend'><i class='fas fa-paper-plane'></i> $uv_message_activation_sent</div>";


            else:
	            $html.= "<div class='resend'><i class='fas fa-times'></i> ".__("Sorry user doesn't exist.","user-verification")."</div>";
            endif;



			echo $html;


		}

	}




	?>




	<form action="" method="post">

		<?php
		wp_nonce_field( 'nonce_resend_verification' );
		?>
		<input type="hidden" name="resend_verification_hidden" value="Y">


		<input type="email" name="email" placeholder="hello@hi.com" value="">
		<input type="submit" value="Resend" name="submit">


	</form>
	<?php

	return ob_get_clean();


}















add_action('init','user_verification_auto_login');
function user_verification_auto_login(){


	if( isset( $_GET['uv_autologin'] ) && $_GET['uv_autologin']=='yes' && isset( $_GET['key'] ) ){

		global $wpdb;
		$table = $wpdb->prefix . "usermeta";
		$activation_key = $_GET['key'];
		$meta_data	= $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE meta_value = %s", $activation_key ) );

		$user = get_user_by( 'id', $meta_data->user_id );

		$user_activation_status = get_user_meta( $meta_data->user_id, 'user_activation_status', true );

		if($user_activation_status != 0){

			wp_set_current_user( $meta_data->user_id, $user->user_login );
			wp_set_auth_cookie( $meta_data->user_id );
			do_action( 'wp_login', $user->user_login, $user );

		}

	}

}












//add_action( 'wp_footer', 'uv_filter_check_activation', 100 );

	
function uv_filter_resend_activation_link( ) {
		
		if( isset( $_GET['uv_action'] ) ) $uv_action = $_GET['uv_action'];
		else return;
		
		if( isset( $_GET['id'] ) ) $user_id = $_GET['id'];
		else return;
		
		$user_activation_key = md5(uniqid('', true) );
		
		update_user_meta( $user_id, 'user_activation_key', $user_activation_key );

        $user_verification_verification_page = get_option('user_verification_verification_page');
        $verification_page_url = get_permalink($user_verification_verification_page);

		$user_data 	= get_userdata( $user_id );
		$link 		= $verification_page_url.'?activation_key='.$user_activation_key;
		// $message 	= "<h3>Please verify your account by clicking the link below</h3>";
		// $message   .= "<a href='$link' style='padding:10px 25px; background:#16A05C; color:#fff;font-size:17px;text-decoration:none;'>Activate</a>";
		// $headers 	= array('Content-Type: text/html; charset=UTF-8');
	  
		uv_mail( 
			$user_data->user_email,
			array( 
				'action' 	=> 'email_confirmed',
				'user_id' 	=> $user_id,
				'link'		=> $link
			)
		);
			
			
		// uv_mail( $user_data->user_email, 'Verify Your Account', $message );
		
		uv_show_box_resend_email();
	}
//add_action( 'wp_footer', 'uv_filter_resend_activation_link', 101 );


// Login Check
add_action( 'authenticate', 'uv_user_authentication', 9999, 3 );
function uv_user_authentication( $errors, $username, $passwords ) { 

		if( isset( $errors->errors['incorrect_password'] ) ) return $errors;
		
		if( ! $username ) return $errors;
		$user = get_user_by( 'email', $username );
		if( empty( $user ) ) $user = get_user_by( 'login', $username );
		if( empty( $user ) ) return $errors;

		$user_activation_status = get_user_meta( $user->ID, 'user_activation_status', true ); 
		
		if( $user_activation_status == 0 && $user->ID != 1 ) {

            $user_verification_verification_page = get_option('user_verification_verification_page');
            $verification_page_url = get_permalink($user_verification_verification_page);


			$resend_link = $verification_page_url.'?uv_action=resend&id='. $user->ID;
			
			$uv_message_verify_email = get_option( 'uv_message_verify_email' );
			if( empty( $uv_message_verify_email ) ) 
			$uv_message_verify_email = __( 'Verify your email first!', 'user-verification' );
		
			$message = apply_Filters(
				'account_lock_message', 
				sprintf(
					'<strong>%s</strong> %s <a href="%s">%s</a>', 
					__('Error:', 'user-verification'),
					$uv_message_verify_email,
					$resend_link,
					__('Resend verification email','user-verification' )
				), 
				$username
			);
			
            return new \WP_Error('authentication_failed', $message);
		}		
        return $errors;
    }

	function uv_mail( $email_to_add = '', $args = array() ) {
		
		if( empty( $email_to_add ) ) return false;
		
		$action 	= isset( $args['action'] ) ? $args['action'] : '';
		$user_id 	= isset( $args['user_id'] ) ? $args['user_id'] : 1;
		$link 		= isset( $args['link'] ) ? $args['link'] : '';
		$user_info 	= get_userdata( $user_id );
		
		//update_option( 'uv_check_data', $action );

		if( empty( $action ) ) return false; 
		
		$parametar_vars = array(
			'{site_name}'			=> get_bloginfo('name'),
			'{site_description}' 	=> get_bloginfo('description'),
			'{site_url}' 			=>  get_bloginfo('url'),						
			// '{site_logo_url}'		=> $logo_url,
			'{user_name}' 			=> $user_info->user_login,						  
			'{user_avatar}' 		=> get_avatar( $user_id, 60 ),
			'{ac_activaton_url}'	=> $link
		);
		
		
		$uv_email_templates_data = get_option( 'uv_email_templates_data' );
		if(empty($uv_email_templates_data)){
				
			$class_uv_emails = new class_uv_emails();
			$templates_data = $class_uv_emails->uv_email_templates_data();
		
		} else {

			$class_uv_emails = new class_uv_emails();
			$templates_data = $class_uv_emails->uv_email_templates_data();
				
			$templates_data = array_merge($templates_data, $uv_email_templates_data);
		}
		
		
		$message_data = isset( $templates_data[$action] ) ? $templates_data[$action] : '';
		if( empty( $message_data ) ) return false; 
		
		
		$email_to 			= strtr( $message_data['email_to'], $parametar_vars );	
		$email_subject 		= strtr( $message_data['subject'], $parametar_vars );
		$email_body 		= strtr( $message_data['html'], $parametar_vars );
		$email_from 		= strtr( $message_data['email_from'], $parametar_vars );	
		$email_from_name 	= strtr( $message_data['email_from_name'], $parametar_vars );				
		$enable 			= strtr( $message_data['enable'], $parametar_vars );	
			
		// wp_update_post( array(
			// 'ID'	=> 1,
			// 'post_content' => $email_body,
		// ) );
		
		$headers = "";
		$headers .= "From: ".$email_from_name." <".$email_from."> \r\n";
		$headers .= "Bcc: ".$email_to." \r\n";		
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$attachments = '';	
		
		$status = wp_mail( $email_to_add, $email_subject, $email_body, $headers, $attachments );
		
		return $status;
	}
		
	function uv_show_box_resend_email() {
		
		$uv_message_activation_sent = get_option( 'uv_message_activation_sent' );
		if( empty( $uv_message_activation_sent ) ) 
		$uv_message_activation_sent = __( 'Activation Email Sent', 'user-verification' );
		
		echo "<div class='uv_popup_box_container'><div class='uv_popup_box_content'>
		<span class='uv_popup_box_close'><i class='fas fa-times-circle'></i></span><i class='fas fa-check-square'></i>
		<h3 class='uv_popup_box_data'>$uv_message_activation_sent</h3></div></div>";
	}
	
	function uv_show_box_key_error() {
		
		$uv_message_invalid_key = get_option( 'uv_message_invalid_key' );
		if( empty( $uv_message_invalid_key ) ) 
		$uv_message_invalid_key = __( 'Invalid activation Key', 'user-verification' );
	
		echo "<div class='uv_popup_box_container'><div class='uv_popup_box_content'>
		<span class='uv_popup_box_close'><i class='fa fa-times-circle-o'></i></span>
		<i class='fas fa-exclamation-triangle'></i><h3 class='uv_popup_box_data'>$uv_message_invalid_key</h3></div></div>";
	}
	
	function uv_show_box_finished() {
		
		$uv_message_verification_success = get_option( 'uv_message_verification_success' );
		if( empty( $uv_message_verification_success ) )
		$uv_message_verification_success = __( 'Your account is now verified', 'user-verification' );
	
		echo "<div class='uv_popup_box_container'><div class='uv_popup_box_content'>
		<span class='uv_popup_box_close'><i class='fas fa-times-circle'></i></span>
		<i class='fas fa-check-square'></i><h3 class='uv_popup_box_data'>$uv_message_verification_success</h3></div></div>";
	}
	
	function uv_show_box_key_expired() {
		
		$uv_message_key_expired = get_option( 'uv_message_key_expired' );
		if( empty( $uv_message_key_expired ) )
		$uv_message_key_expired = __( 'Your account is now verified', 'user-verification' );
	
		echo "<div class='uv_popup_box_container'><div class='uv_popup_box_content'>
		<span class='uv_popup_box_close'><i class='fas fa-times-circle'></i></span>
		<i class='fas fa-exclamation-triangle'></i><h3 class='uv_popup_box_data'>$uv_message_key_expired</h3></div></div>";
	}





function uv_all_user_roles() {

	$wp_roles = new WP_Roles();

	//var_dump($wp_roles);
	$roles = $wp_roles->get_names();

	return  $roles;
	// Below code will print the all list of roles.
	//echo '<pre>'.var_export($wp_roles, true).'</pre>';

}



add_action('pick_settings_action_custom_field_grid','pick_settings_action_custom_field_grid', 0,1);


function pick_settings_action_custom_field_grid($option) {

    $id 			= isset( $option['id'] ) ? $option['id'] : "";
    $placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
    $args 			= isset( $option['args'] ) ? $option['args'] : "";

    $values 	 		= get_option( $id );

    ?>
    <div class="grid">
        <?php

        foreach($args as $key=>$grid_item){

            $title = isset($grid_item['title']) ? $grid_item['title'] : '';
            $link = isset($grid_item['link']) ? $grid_item['link'] : '';
            $thumb = isset($grid_item['thumb']) ? $grid_item['thumb'] : '';

            ?>

            <div class="item">
                <div class="thumb"><a href="<?php echo $link; ?>"><img src="<?php echo $thumb; ?>"></img></a></div>
                <div class="name"><a href="<?php echo $link; ?>"><?php echo $title; ?></a></div>
            </div>
            <?php

        }
        ?>
    </div>

    <style type="text/css">
        .grid{}
        .grid .item{
            width: 300px;
            display: inline-block;
            vertical-align: top;
            margin: 10px;
            background: #ddd;
            overflow: hidden;
        }
        .grid .name{

        }

        .grid .name a{
            margin: 10px;
            text-decoration: none;
            display: block;
            font-weight: 600;
        }
        }

        .grid .thumb{ }
        .grid .thumb img{
            width: 100%;
            height: auto;
        }


    </style>
    <?php



}


add_action('pick_settings_action_custom_field_email_templates','pick_settings_action_custom_field_email_templates', 0,1);


function pick_settings_action_custom_field_email_templates($option) {


    $id 			= isset( $option['id'] ) ? $option['id'] : "";
    $placeholder 	= isset( $option['placeholder'] ) ? $option['placeholder'] : "";
    $values 	 		= get_option( $id );



    if(empty($values)){

        $class_uv_emails = new class_uv_emails();
        $values = $class_uv_emails->uv_email_templates_data();

    }

    ?>
    <div class="button reset-email-templates">Reset Templates</div>
    <div class="templates_editor">
    <?php

    foreach($values as $key=>$templates){

        $email_to = isset($templates['email_to']) ? $templates['email_to'] : '';
        $email_from = isset($templates['email_from']) ? $templates['email_from'] : '';
        $email_from_name = isset($templates['email_from_name']) ? $templates['email_from_name'] : '';
        $enable = isset($templates['enable']) ? $templates['enable'] : '';
        $description = isset($templates['description']) ? $templates['description'] : '';

        ?>

        <h2 class="header"><?php echo $templates['name']; ?>
            <input type="hidden" name="uv_email_templates_data[<?php echo $key; ?>][name]" value="<?php echo $templates['name']; ?>" />
        </h2>

        <div class="options">
            <div class="description"><?php echo $description; ?></div>
            <label><?php echo __('Enable ?', 'user-verification'); ?>
                <select name="uv_email_templates_data[<?php echo $key; ?>][enable]" >
                    <?php
                    if($enable=='yes'){
                        ?>
                        <option selected  value="yes" ><?php echo __('Yes', 'user-verification'); ?></option>
                        <?php
                    }
                    else{
                        ?>
                        <option value="yes" ><?php echo __('Yes', 'user-verification'); ?></option>
                        <?php
                    }
                    if($enable=='no'){
                        ?>
                        <option selected value="no" ><?php echo __('No', 'user-verification'); ?></option>
                        <?php
                    }
                    else{
                        ?>
                        <option value="no" ><?php echo __('No', 'user-verification'); ?></option>
                        <?php
                    }
                    ?>

                </select>

            </label><br>
            <label><?php echo __('Email To: (Copy)', 'user-verification'); ?>
                <input placeholder="support@hello.com,hello_2@hello.com" type="text" name="uv_email_templates_data[<?php echo $key; ?>][email_to]" value="<?php echo $email_to; ?>" />
            </label><br>

            <label><?php echo __('Email from name:', 'user-verification'); ?>
                <input placeholder="My Site Name" type="text" name="uv_email_templates_data[<?php echo $key; ?>][email_from_name]" value="<?php echo $email_from_name; ?>" />
            </label><br>

            <label><?php echo __('Email from:', 'user-verification'); ?>
                <input placeholder="support@hello.com" type="text" name="uv_email_templates_data[<?php echo $key; ?>][email_from]" value="<?php echo $email_from; ?>" />
            </label><br>

            <label><?php echo __('Email Subject:','user-verification'); ?>
                <input type="text" name="uv_email_templates_data[<?php echo $key; ?>][subject]" value="<?php echo $templates['subject']; ?>" />
            </label><br>




            <?php

            ob_start();
            wp_editor( $templates['html'], $key, $settings = array('textarea_name'=>'uv_email_templates_data['.$key.'][html]','media_buttons'=>false,'wpautop'=>true,'teeny'=>true,'editor_height'=>'200px', ) );
            $editor_contents = ob_get_clean();

            ?>
            <label><?php echo __('Email Body:','user-verification'); ?><br/>
                <?php
                echo $editor_contents;

                ?>
            </label>
        </div>

        <?php
            }
        ?>
    </div>


    <script>
        jQuery(document).ready(function($){

            $('.templates_editor').accordion({collapsible: true, active:999});

        })

    </script>
    <?php

    }

















