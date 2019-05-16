<?php

add_action( 'um_registration_after_auto_login', 'uv_um_registration_after_auto_login', 100, 1 );
function uv_um_registration_after_auto_login( $user_id ) {

    $uv_um_disable_auto_login = get_option('uv_um_disable_auto_login', 'no');

    if($uv_um_disable_auto_login == 'yes'){

        wp_logout();

    }

}


add_action( 'um_profile_before_header', 'um_profile_before_header', 10, 1 );
function um_profile_before_header( $args ) {

    $profile_id = um_profile_id();
    $uv_um_message_before_header = get_option('uv_um_message_before_header', '');
    $is_verified = user_verification_is_verified($profile_id);

    if(!$is_verified){
        echo $uv_um_message_before_header;
        //wp_logout();
    }


}

add_action( 'um_add_error_on_form_submit_validation', 'my_add_error_on_form_submit_validation', 10, 3 );
function my_add_error_on_form_submit_validation( $field, $key, $args ) {
    // your code here

    $enable_block_username = get_option('user_verification_enable_block_username');

    if($enable_block_username == 'yes' && $key == 'user_login'){

        $is_blocked = user_verification_is_username_blocked($args[$key]);
        if($is_blocked){
            UM()->form()->add_error('user_login', __('Username is blocked','ultimate-member') );
        }
    }


    if($enable_block_username == 'yes' && $key == 'user_email'){

        $is_blocked = user_verification_is_emaildomain_blocked($args[$key]);
        if($is_blocked){
            UM()->form()->add_error('user_email', __('This email domain is not allowed!','ultimate-member') );
        }
    }



}








//add_action( 'um_registration_complete', 'uv_uv_registration_complete', 90, 2 );
//function uv_uv_registration_complete( $user_id, $args ) {
//    $is_verified = user_verification_is_verified($user_id);
//    $uv_um_message_before_header = get_option('uv_um_message_before_header', '');
//
//    if(!$is_verified){
//        $url = get_permalink($uv_um_redirect_after_registration).'?uv_check=true';
//        wp_redirect($url);
//        //wp_logout();
//
//    }
//}







/*
 *
 * After complete UM user registration.

*/

//add_action( 'um_user_register', 'my_user_register', 10, 2 );
//function my_user_register( $user_id, $args ) {
//    var_dump($args);
//}



/*
 *
 * After complete UM user registration. Redirects handlers at 100 priority, you can add some info before redirects
 * */

//add_action( 'um_registration_complete', 'my_registration_complete', 10, 2 );
//function my_registration_complete( $user_id, $args ) {
//    // your code here
//}


/*
 *
 * After complete UM user registration and autologin.
 *
 * */

//add_action( 'um_registration_after_auto_login', 'my_registration_after_auto_login', 10, 1 );
//function my_registration_after_auto_login( $user_id ) {
//    // your code here
//}