<?php

/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 





$settings_general = array(
    'page_nav' 	=> __( 'General', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('Basic Settings','user-verification'),
            'description' 	=> __('Some basic settings to get started','user-verification'),
            'options' 	=> array(
                array(
                    'id'		=> 'user_verification_verification_page',
                    'title'		=> __('Choose verification page','user-verification'),
                    'details'	=> __('Verification checker page where you place the shortcode <code>[user_verification_check]</code>, please create a page and use this shortcode uder post content.','user-verification'),
                    'type'		=> 'select2',
                    'args'		=> user_verification_get_pages_list(),
                ),
                array(
                    'id'		=> 'user_verification_redirect_verified',
                    'title'		=> __('Redirect after verification','user-verification'),
                    'details'	=> __('Redirect to any page after successfully verified account.','user-verification'),
                    'type'		=> 'select2',
                    'args'		=> user_verification_get_pages_list(),
                ),

                array(
                    'id'		=> 'user_verification_login_automatically',
                    'title'		=> __('Automatically login after verification','user-verification'),
                    'details'	=> __('Yes means, users click on the Account activation link from email and they login automatically to your website, No means they don\'t','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_exclude_user_roles',
                    'title'		=> __('Exclude these user role to verification?','user-verification'),
                    'details'	=> __('You can exclude verification for these user roles','user-verification'),
                    'type'		=> 'select_multi',
                    'args'		=> uv_all_user_roles(),
                ),


            )
        ),

        'woocommerce' => array(
            'title' 	=> 	__('WooCommerce','user-verification'),
            'description' 	=> __('Integration for WooCommerce plugin','user-verification'),
            'options' 	=> array(
                array(
                    'id'		=> 'uv_wc_disable_auto_login',
                    'title'		=> __('Disable auto login after registration on WooCommerce?','user-verification'),
                    'details'	=> __('You can disable auto login after registration via WooCommerce register form. this also disable login on checkout page','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_wc_message_after_registration',
                    'title'		=> __('Display Message after successfully registration','user-verification'),
                    'details'	=> __('You can display custom message on after successfully registration via WooCommerce register form.','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Thanks for your registration, please follow email we sent.','user-verification'),
                ),

                array(
                    'id'		=> 'uv_wc_redirect_after_payment',
                    'title'		=> __('Redirect after payment','user-verification'),
                    'details'	=> __('You can set custom page to redirect after successfully payment, and this page should check verification status and take action to stay logged-in or logged-out the user automatically. 
                    <br>please use following shortcode <code>[user_verification_message message="Please check email to verify account first"]</code> to check verification status, it will automatically logged-out the unverified user and display the custom message.
                    ','user-verification'),
                    'type'		=> 'select2',
                    'args'		=> user_verification_get_pages_list(),
                ),




            )
        ),
        'ultimate_member' => array(
            'title' 	=> 	__('Ultimate Member','user-verification'),
            'description' 	=> __('Integration for Ultimate Member plugin','user-verification'),
            'options' 	=> array(
                array(
                    'id'		=> 'uv_um_disable_auto_login',
                    'title'		=> __('Disable auto login after registration on Ultimate Member?','user-verification'),
                    'details'	=> __('You can disable auto login after registration via Ultimate Member register form.','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),
                array(
                    'id'		=> 'uv_um_message_before_header',
                    'title'		=> __('Display Message after successfully registration','user-verification'),
                    'details'	=> __('You can display custom message at profile header after redirect profile page via Ultimate Member.','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Thanks for your registration, please follow email we sent.','user-verification'),
                ),


            )
        ),
        'paid-memberships-pro' => array(
            'title' 	=> 	__('Paid Memberships Pro','user-verification'),
            'description' 	=> __('Integration for Paid Memberships Pro plugin','user-verification'),
            'options' 	=> array(
                array(
                    'id'		=> 'uv_pmpro_disable_auto_login',
                    'title'		=> __('Disable auto login after checkout on Paid Memberships Pro?','user-verification'),
                    'details'	=> __('You can disable auto login after registration via Paid Memberships Pro checkout(register) form.','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),


                array(
                    'id'		=> 'uv_pmpro_message_checkout_page',
                    'title'		=> __('Display message on checkout confirmation page','user-verification'),
                    'details'	=> __('You can display custom message on checkout confirmation page.','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('We have sent a confirmation mail please follow to verify account first.','user-verification'),
                ),

                array(
                    'id'		=> 'uv_pmpro_redirect_timout',
                    'title'		=> __('Automatically logout after second','user-verification'),
                    'details'	=> __('After successfully checkout user will wait for few second to display the message and then redirect to another page. <br> 1000 = 1 second','user-verification'),
                    'type'		=> 'text',
                    'placeholder' => __('3000','user-verification'),
                ),

                array(
                    'id'		=> 'uv_pmpro_redirect_after_checkout_page_id',
                    'title'		=> __('Redirect to this page after checkout','user-verification'),
                    'details'	=> __('You can set custom page to redirect and logout after few second passed, where user can see instruction what to do next to get verified.','user-verification'),
                    'type'		=> 'select2',
                    'args'		=> user_verification_get_pages_list(),
                ),
            )
        ),







    ),
);




$settings_security = array(
    'page_nav' 	=> __( 'Protect Spam', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('Protect Spam Settings','user-verification'),
            'description' 	=> __('Protect your site from Spam','user-verification'),
            'options' 	=> array(
                array(
                    'id'		=> 'user_verification_enable_block_domain',
                    'title'		=> __('Enable blocking email domain on registration','user-verification'),
                    'details'	=> __('You can enable email domain name blocking for spammy/temporary email account services','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_settings_blocked_domain',
                    'title'		=> __('Blocked Domains','user-verification'),
                    'details'	=> __('One domain per line. wihtout http:// or https:// or www','user-verification'),
                    'type'		=> 'text_multi',
                    'placeholder' => __('domain.com','user-verification'),
                ),



                array(
                    'id'		=> 'user_verification_enable_block_username',
                    'title'		=> __('Enable blocking username on registration.','user-verification'),
                    'details'	=> __('User will not able to register blocked username, like admin, info, etc.','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_settings_blocked_username',
                    'title'		=> __('Blocked Usernames','user-verification'),
                    'details'	=> __('You can following string match <ul><li><b>^username</b> : String start with <b><i>username</i></b></li><li><b>username$</b> : String end by <b><i>username</i></b></li><li><b>username</b> : String contain <b><i>username</i></b></b></li></ul>','user-verification'),
                    'type'		=> 'text_multi',
                    'placeholder' => __('username','user-verification'),
                ),





            )
        ),


    ),
);




$settings_messages = array(
    'page_nav' 	=> __( 'Messages', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('Custom Messages','user-verification'),
            'description' 	=> __('Customize error messages','user-verification'),
            'options' 	=> array(

                array(
                    'id'		=> 'uv_message_invalid_key',
                    'title'		=> __('Invalid activation key','user-verification'),
                    'details'	=> __('Show custom message when user activation key is invalid or wrong','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Sorry! Invalid activation key','user-verification'),
                ),
                array(
                    'id'		=> 'uv_message_activation_sent',
                    'title'		=> __('Activation key sent','user-verification'),
                    'details'	=> __('Show custom message when activation key is sent to user email','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Hey! You activation key has been sent to your mail','user-verification'),
                ),

                array(
                    'id'		=> 'uv_message_verify_email',
                    'title'		=> __('Verify email address','user-verification'),
                    'details'	=> __('Show custom message when user try to login without verifying his/her email with proper activation key','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Please verify account first.','user-verification'),
                ),
                array(
                    'id'		=> 'user_verification_registered_message',
                    'title'		=> __('Registration success message','user-verification'),
                    'details'	=> __('User will get this message as soon as registered on your website','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Hey! Thanks for registration','user-verification'),
                ),


                array(
                    'id'		=> 'uv_message_verification_success',
                    'title'		=> __('Verification successful','user-verification'),
                    'details'	=> __('Show custom message when user successfully verified','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Hey! Thanks for verification','user-verification'),
                ),

                array(
                    'id'		=> 'uv_message_key_expired',
                    'title'		=> __('Activation key Expired','user-verification'),
                    'details'	=> __('Show custom message when user activation key is expired','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Hey! Your activation key has expired.','user-verification'),
                ),

                array(
                    'id'		=> 'uv_message_captcha_error',
                    'title'		=> __('Captcha error message','user-verification'),
                    'details'	=> __('Show custom message when captcha error occurred','user-verification'),
                    'type'		=> 'textarea',
                    'placeholder' => __('Sorry! You missed the Captcha or Wrong input.','user-verification'),
                ),


            )
        ),


    ),
);




$settings_recaptcha = array(
    'page_nav' 	=> __( 'reCAPTCHA', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('reCAPTCHA Settings','user-verification'),
            'description' 	=> __('Protect your site by reCAPTCHA','user-verification'),
            'options' 	=> array(

                array(
                    'id'		=> 'uv_recaptcha_sitekey',
                    'title'		=> __('reCAPTCHA sitekey','user-verification'),
                    'details'	=> __('Google reCAPTCHA sitekey, please register here <a href="https://www.google.com/recaptcha/">https://www.google.com/recaptcha/</a>','user-verification'),
                    'type'		=> 'text',
                    'placeholder' => '',
                ),


                array(
                    'id'		=> 'uv_recaptcha_login_page',
                    'title'		=> __('reCAPTCHA on default login page','user-verification'),
                    'details'	=> __('Enable recaptcha on default login page','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_recaptcha_register_page',
                    'title'		=> __('reCAPTCHA on default registration page','user-verification'),
                    'details'	=> __('Enable recaptcha on default registration page','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_recaptcha_lostpassword_page',
                    'title'		=> __('reCAPTCHA on default reset password page','user-verification'),
                    'details'	=> __('Enable recaptcha on default reset password page','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_recaptcha_comment_form',
                    'title'		=> __('reCAPTCHA on comment form','user-verification'),
                    'details'	=> __('Enable recaptcha on comment form','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

            )
        ),

        'woocommerce' => array(
            'title' 	=> 	__('WooCommerce','user-verification'),
            'description' 	=> __('Integration for WooCommerce','user-verification'),
            'options' 	=> array(


                array(
                    'id'		=> 'uv_recaptcha_wc_login_form',
                    'title'		=> __('reCAPTCHA on WooCommerce login from','user-verification'),
                    'details'	=> __('Enable reCAPTCHA on WooCommerce login from','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

                array(
                    'id'		=> 'uv_recaptcha_wc_register_form',
                    'title'		=> __('reCAPTCHA on WooCommerce register from','user-verification'),
                    'details'	=> __('Enable reCAPTCHA on WooCommerce register from','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),

                    ),
                ),


                array(
                    'id'		=> 'uv_recaptcha_wc_lostpassword_form',
                    'title'		=> __('reCAPTCHA on WooCommerce lost password from','user-verification'),
                    'details'	=> __('Enable reCAPTCHA on WooCommerce lost password from','user-verification'),
                    'type'		=> 'select',
                    'args'		=> array(
                        'no'	=> __('No','user-verification'),
                        'yes'	=> __('Yes','user-verification'),
                    ),
                ),

            )
        ),

    ),
);







$args = array(
    'add_in_menu'     => true,
    'menu_type'       => 'main',
    'menu_title'      => __( 'User Verification', 'user-verification' ),
    'menu_name'      => __( 'User Verification - Settings', 'user-verification' ),
    'page_title'      => __( 'User Verification - Settings', 'user-verification' ),
    'menu_page_title' => __( 'User Verification - Settings', 'user-verification' ),
    'capability'      => "manage_options",
    'menu_slug'       => "user-verification",
    'menu_icon'       => "dashicons-shield-alt",
    'pages' 	  => array(
        'uv-general' => $settings_general,
        'uv-security' => $settings_security,
        'uv-messages' => $settings_messages,
        'uv-recaptcha' => $settings_recaptcha,
    ),
);

$WPAdminMenu = new WPAdminMenu( $args );

















$class_uv_emails = new class_uv_emails();
$templates_data = $class_uv_emails->uv_email_templates_data();


$uv_email_templates_parameters = $class_uv_emails->uv_email_templates_parameters();

$parameter_html = '';

ob_start();

foreach ($uv_email_templates_parameters as $key=>$parameter_group){

    $parameter_title = $parameter_group['title'];
    $parameters = $parameter_group['parameters'];

   // foreach ($parameter_group as $parameter){
    $parameter_html .= '<div >'.$parameter_title.'</div>';

        $parameter_html .= '<ul>';

        foreach ($parameters as $value){
            $parameter_html .= '<li>'.$value.'</li>';
        }


        $parameter_html .= '</ul>';

   // }

}




$settings_email_templates = array(
    'page_nav' 	=> __( 'Email Templates', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('Email Templates','user-verification'),
            'description' 	=> __('You can customize email templates here.','user-verification'),
            'options' 	=> array(

                array(
                    'id'		=> 'uv_email_templates_data',
                    'title'		=> __('Emails Templates Settings','user-verification'),
                    'details'	=> sprintf(__('Emails Templates Options %s','user-verification'), $parameter_html) ,
                    'type'		=> 'email_templates',
                    'args'		=> $templates_data,
                ),



            )
        ),



    ),
);




$email_templates_args = array(
    'add_in_menu' => true,
    'menu_type' => 'submenu',
    'menu_title' => __( 'Email Templates', 'user-verification' ),
    'page_title' => __( 'User Verification - Email Templates', 'user-verification' ),
    'menu_page_title' => __( 'User Verification - Email Templates', 'user-verification' ),
    'capability' => "manage_options",
    'menu_slug' => "user-verification-email-template",
    'parent_slug' => "user-verification",
    'pages' 	  => array(
        'templates' => $settings_email_templates,


    ),
);

$WPAdminMenu_sub = new WPAdminMenu( $email_templates_args );




$help = array(
    'page_nav' 	=> __( 'Help', 'user-verification' ),
    'page_settings' => array(
        'section_1' => array(
            'title' 	=> 	__('Help & Support','user-verification'),
            'description' 	=> __('Here is some question and answer for your quick help.','user-verification'),
            'options' 	=> array(

                array(
                    'id'		=> 'uv_faq',
                    'title'		=> __('Frequently Asked Question','user-verification'),
                    'details'	=> __('If you have more question please asked on our forum <a href="https://www.pickplugins.com/questions/">https://www.pickplugins.com/questions/</a>','user-verification'),
                    'type'		=> 'faq',
                    'args'		=> array(
                        array('title'=>'How to setup plugin?','link'=>'https://www.pickplugins.com/documentation/user-verification/faq/how-to-setup-plugin/', 'content'=>'Please see the documentation here <a href="https://www.pickplugins.com/documentation/user-verification/faq/how-to-setup-plugin/">https://www.pickplugins.com/documentation/user-verification/faq/how-to-setup-plugin/</a>'),
                        array('title'=>'How to check user verification status?
','link'=>'#', 'content'=>'Please see the documentation here <a href="https://www.pickplugins.com/documentation/user-verification/faq/how-to-check-user-verification-status/">https://www.pickplugins.com/documentation/user-verification/faq/how-to-check-user-verification-status/</a>'),
                        array('title'=>'How to stop auto login on WooCommerce registration?
','link'=>'#', 'content'=>'Please see the documentation here <a href="https://www.pickplugins.com/documentation/user-verification/faq/how-to-stop-auto-login-on-woocommerce-registration/">https://www.pickplugins.com/documentation/user-verification/faq/how-to-stop-auto-login-on-woocommerce-registration/</a>'),
                        array('title'=>'How to Automatically login after verification?
','link'=>'#', 'content'=>'Please see the documentation here <a href="https://www.pickplugins.com/documentation/user-verification/faq/automatically-login-after-verification/">https://www.pickplugins.com/documentation/user-verification/faq/automatically-login-after-verification/</a>'),


                    ),
                ),



            )
        ),



    ),
);





$our_plugins = array(
    'page_nav' 	=> __( 'Our Plugins', 'user-verification' ),
    'page_settings' => array(
        'section_2' => array(
            'title' 	=> 	__('Our plugins you may looking for','user-verification'),
            'description' 	=> __('Please take a look on our plugin list may help on your projects..','user-verification'),
            'options' 	=> array(

                array(
                    'id'		=> 'uv_faq',
                    'title'		=> __('Popular Plugins','user-verification'),
                    'details'	=> __('See our all plugins here <a href="https://www.pickplugins.com/plugins/">https://www.pickplugins.com/plugins/</a>','user-verification'),
                    'type'		=> 'grid',
                    'args'		=> array(
                        array('title'=>'Post Grid','link'=>'https://www.pickplugins.com/item/post-grid-create-awesome-grid-from-any-post-type-for-wordpress/', 'content'=>'', 'thumb'=>'https://www.pickplugins.com/wp-content/uploads/2015/12/3814-post-grid-thumb-500x262.jpg'),
                        array('title'=>'Accordion','link'=>'https://www.pickplugins.com/item/accordions-html-css3-responsive-accordion-grid-for-wordpress/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/01/3932-product-thumb-500x250.png' ),
                        array('title'=>'Woocommerce Product Slider','link'=>'https://www.pickplugins.com/item/woocommerce-products-slider-for-wordpress/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/03/4357-woocommerce-products-slider-thumb-500x250.jpg'),
                        array('title'=>'Team Showcase','link'=>'https://www.pickplugins.com/item/team-responsive-meet-the-team-grid-for-wordpress/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/06/5145-team-thumb-500x250.jpg'),

                        array('title'=>'Breadcrumb','link'=>'https://www.pickplugins.com/item/breadcrumb-awesome-breadcrumbs-style-navigation-for-wordpress/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2016/03/4242-breadcrumb-500x252.png'),

                        array('title'=>'Wishlist for WooCommerce','link'=>'https://www.pickplugins.com/item/woocommerce-wishlist/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2017/10/12047-woocommerce-wishlist-500x250.png'),

                        array('title'=>'Job Board Manager','link'=>'https://www.pickplugins.com/item/job-board-manager-create-job-site-for-wordpress/', 'content'=>'','thumb'=>'https://www.pickplugins.com/wp-content/uploads/2015/08/3466-job-board-manager-thumb-500x250.png'),

                    ),
                ),



            )
        ),







    ),
);













$help_menu_args = array(
    'add_in_menu' => true,
    'menu_type' => 'submenu',
    'menu_title' => __( 'Help', 'user-verification' ),
    'page_title' => __( 'User Verification - Help', 'user-verification' ),
    'menu_page_title' => __( 'User Verification - Help', 'user-verification' ),
    'capability' => "manage_options",
    'menu_slug' => "user-verification-help",
    'parent_slug' => "user-verification",
    'pages' 	  => array(
        'help' => $help,
        'our-plugins' => $our_plugins,
    ),
);

$WPAdminMenu_sub = new WPAdminMenu( $help_menu_args );

