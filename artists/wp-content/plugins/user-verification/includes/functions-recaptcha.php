<?php
/*
* @Author 		pickplugins
* Copyright: 	pickplugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


// Google recaptcha for Default WordPress Login form.


add_action('login_form', 'wdm_login_form_captcha');
function wdm_login_form_captcha(){

    $uv_recaptcha_login_page = get_option('uv_recaptcha_login_page', 'no');


    if($uv_recaptcha_login_page == 'yes'):
	    $uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
	    ?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>

	    <?php
    endif;
}



add_filter('wp_authenticate_user','wdm_validate_login_captcha',10,2);
function wdm_validate_login_captcha($user, $password) {
	$return_value = $user;
	$uv_recaptcha_login_page = get_option('uv_recaptcha_login_page', 'no');
	$uv_message_captcha_error = get_option('uv_message_captcha_error', __('Captcha Error. Please try again.','user-verification'));

	if($uv_recaptcha_login_page == 'yes' && isset($_POST['g-recaptcha-response'])):
		$captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

		if(empty($captcha)){

			$return_value = new WP_Error( 'loginCaptchaError', $uv_message_captcha_error );
		}
    endif;


	return $return_value;
}






add_action('register_form', 'uv_recaptcha_register_form');
function uv_recaptcha_register_form(){

	$uv_recaptcha_register_page = get_option('uv_recaptcha_register_page', 'no');


	if($uv_recaptcha_register_page == 'yes'):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
		?>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>

		<?php
	endif;


}

add_filter( 'registration_errors', 'uv_registration_errors', 10, 3 );

function uv_registration_errors( $errors, $sanitized_user_login, $user_email ) {
	$uv_recaptcha_register_page = get_option('uv_recaptcha_register_page', 'no');
	$uv_message_captcha_error = get_option('uv_message_captcha_error', __('Captcha Error. Please try again.','user-verification'));

	if($uv_recaptcha_register_page == 'yes'):
		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			$errors->add( 'loginCaptchaError',  $uv_message_captcha_error  );
		}
    endif;



	return $errors;
}




add_action('lostpassword_form', 'uv_recaptcha_password_reset_form');
function uv_recaptcha_password_reset_form(){

	$uv_recaptcha_lostpassword_page = get_option('uv_recaptcha_lostpassword_page', 'no');


	if($uv_recaptcha_lostpassword_page == 'yes'):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
		?>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>
        <br>


		<?php
	endif;


}



add_filter( 'lostpassword_post', 'uv_lostpassword_post_errors', 10, 3 );
function uv_lostpassword_post_errors( $errors ) {
	$uv_recaptcha_lostpassword_page = get_option('uv_recaptcha_lostpassword_page', 'no');
	$uv_message_captcha_error = get_option('uv_message_captcha_error', __('Captcha Error. Please try again.','user-verification'));

	if($uv_recaptcha_lostpassword_page == 'yes' && isset($_POST['g-recaptcha-response'])):
		$captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			$errors->add( 'loginCaptchaError',  $uv_message_captcha_error  );
		}
	endif;

	return $errors;
}


add_action('woocommerce_login_form', 'uv_recaptcha_wc_login_form');
function uv_recaptcha_wc_login_form(){

	$uv_recaptcha_wc_login_form = get_option('uv_recaptcha_wc_login_form', 'no');


	if($uv_recaptcha_wc_login_form == 'yes'):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
		?>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>
        <br>
		<?php
	endif;


}




add_action('woocommerce_register_form', 'uv_recaptcha_wc_register_form');
function uv_recaptcha_wc_register_form(){

	$uv_recaptcha_wc_register_form = get_option('uv_recaptcha_wc_register_form', 'no');


	if($uv_recaptcha_wc_register_form == 'yes' ):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
        ?>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>
        <br>

        <?php
	endif;


}


function wooc_validate_extra_register_fields( $username, $email, $validation_errors ) {

	$uv_recaptcha_wc_register_form = get_option('uv_recaptcha_wc_register_form', 'no');
	$uv_message_captcha_error = get_option('uv_message_captcha_error', __('Captcha Error. Please try again.','user-verification'));

	if($uv_recaptcha_wc_register_form == 'yes' && isset($_POST['g-recaptcha-response'])):

		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			$validation_errors->add( 'loginCaptchaError', $uv_message_captcha_error );
		}

	endif;


         return $validation_errors;
}

add_action( 'woocommerce_register_post', 'wooc_validate_extra_register_fields', 10, 3 );




add_action('woocommerce_lostpassword_form', 'uv_recaptcha_wc_lostpassword_form');
function uv_recaptcha_wc_lostpassword_form(){

	$uv_recaptcha_wc_lostpassword_form = get_option('uv_recaptcha_wc_lostpassword_form', 'no');


	if($uv_recaptcha_wc_lostpassword_form == 'yes' ):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
		?>

        <script src='https://www.google.com/recaptcha/api.js'></script>
        <div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>
        <br>

		<?php
	endif;


}
















add_filter( 'comment_form_defaults', 'uv_recaptcha_comment_form');
function uv_recaptcha_comment_form( $default ) {

	$uv_recaptcha_comment_form = get_option('uv_recaptcha_comment_form', 'no');


	if($uv_recaptcha_comment_form == 'yes'):
		$uv_recaptcha_sitekey = get_option('uv_recaptcha_sitekey', '');
		$default[ 'fields' ][ 'recaptcha' ] = '<script src="https://www.google.com/recaptcha/api.js"></script><div class="g-recaptcha" data-sitekey="<?php echo $uv_recaptcha_sitekey; ?>"></div>';
    endif;


	return $default;
}


add_filter( 'preprocess_comment', 'uv_verify_recaptcha_comment_form' );
function uv_verify_recaptcha_comment_form( $commentdata ) {

	$uv_recaptcha_comment_form = get_option('uv_recaptcha_comment_form', 'no');
	if($uv_recaptcha_comment_form == 'yes'):
		if ( empty( $_POST['g-recaptcha-response'] ) ) {
			wp_die( __('Captcha Error. Please try again.','user-verification') );
		}
    endif;

	return $commentdata;
}








