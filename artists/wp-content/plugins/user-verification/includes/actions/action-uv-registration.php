<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


	add_action( 'user_register', 'uv_action_user_register_function', 30 );

	if ( ! function_exists( 'uv_action_user_register_function' ) ) {
		function uv_action_user_register_function( $user_id ) {

			$permalink_structure = get_option('permalink_structure');

            $user_verification_verification_page = get_option('user_verification_verification_page');
            $uv_exclude_user_roles = get_option('uv_exclude_user_roles');

            $verification_page_url = get_permalink($user_verification_verification_page);

			$user_activation_key =  md5(uniqid('', true) );
			
			update_user_meta( $user_id, 'user_activation_key', $user_activation_key );
			update_user_meta( $user_id, 'user_activation_status', 0 );
			
			$user_data 	= get_userdata( $user_id );




			$user_roles = !empty($user_data->roles) ? $user_data->roles : array();


			foreach ($uv_exclude_user_roles as $role):

                if(in_array($role, $user_roles)){
                    //update_option('uv_custom_option', $role);
                    update_user_meta( $user_id, 'user_activation_status', 1 );
                    return;
                }

            endforeach;


			if(empty($permalink_structure)){
				$link 		= $verification_page_url.'&activation_key='.$user_activation_key;

			}else{

				$link 		= $verification_page_url.'?activation_key='.$user_activation_key;
			}

			
			uv_mail( 
				$user_data->user_email,
				array( 
					'action' 	=> 'user_registered',
					'user_id' 	=> $user_id,
					'link'		=> $link
				)
			);
			
			
		}
	}
