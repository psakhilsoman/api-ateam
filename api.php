<?php
/**
 */
/*
Plugin Name: Api
Plugin URI: https://google.com/
Description: Custom API
Version: 1.0
Author: Akhil
Author URI: https://google.com
License: GPLv2 or later
Text Domain: api
*/

    function generate_public_key( $user_email = '' ) {
        $auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
        $public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
        return $public;
    }

    
    function generate_private_key( $user_id = 0 ) {
        $auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
        $secret   = hash( 'md5', $user_id . $auth_key . date( 'U' ) );
        return $secret;
    }

   function validate_token( $output = true ) {
        // Check that we're trying to authenticate
        if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) ) {
            return $user;
        }

        $public_key = $_SERVER['PHP_AUTH_USER'];
        $token      = $_SERVER['PHP_AUTH_PW'];

        if ( empty( $public_key ) ) {
            return new WP_Error(
                'token_auth_no_key_to_validate',
                __('Public key not sent.', 'wp-api-token-auth'),
                array(
                    'status' => 403,
                )
            );
        }

        if ( empty( $token ) ) {
            return new WP_Error(
                'token_auth_no_token_to_validate',
                __('Token not sent.', 'wp-api-token-auth'),
                array(
                    'status' => 403,
                )
            );
        }

        if ( ! ( $user = $this->get_user( $public_key ) ) ) {
            return new WP_Error(
                'token_auth_invalid_public_key_validated',
                __('Your request could not be authenticated. Invalid public key.', 'wp-api-token-auth'),
                array(
                    'status' => 403,
                )
            );
        } else {
            $token  = $token;
            $secret = $this->get_user_secret_key( $user );
            $public = $public_key;

            if ( hash_equals( md5( $secret . $public ), $token ) ) {
                return $user;
            } else {
                return new WP_Error(
                    'token_auth_invalid_auth_validated',
                    __('Your request could not be authenticated.', 'wp-api-token-auth'),
                    array(
                        'status' => 403,
                    )
                );
            }
        }
    }


//------------Login-start-------------//
add_action( 'rest_api_init', 'localphysio_authn_endpoint' );

function localphysio_authn_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'authn', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_authn'
        )
    );
}


function localphysio_callback_authn( $request_data ) {
    $data = array();
    
    $parameters = $request_data->get_params();
    
    $email     = $parameters['email'];
    $password = $parameters['password'];
    
    if ( isset($email) && isset($password) ) {
        
        $userdata = get_user_by( 'login', $email );
        
        if ( $userdata ) {
            
            $wp_check_password_result = wp_check_password( $password, $userdata->user_pass, $userdata->ID );
            
            if ( $wp_check_password_result ) {
               
                $new_public_key = generate_public_key( $userdata->user_email );
                $new_secret_key = generate_private_key( $userdata->ID );

                update_user_meta( $userdata->ID, 'rest_api_token_auth_public_key', $new_public_key );
                update_user_meta( $userdata->ID, 'rest_api_token_auth_secret_key', $new_secret_key );
               



                $data['status'] = 'OK';
            
                $data['received_data'] = array(
                    'name'     => $email,
                    //'password' => $password,
                    'data'     => $userdata,
                    'public_key' => $new_public_key,
                    'secret_key' => $new_secret_key 

                );
                
                $data['message'] = 'You have reached the server';
                $response = new WP_REST_Response($data, 200); // data => array of returned data
                
            } else {
                $data['status'] = 'Failed';
                $data['message'] = 'You are not authenticated to login!';
                $response = new WP_REST_Response($data, 404);
            }
           
        } else {
            
            $data['status'] = 'Failed';
            $data['message'] = 'The current user does not exist!';
            $response = new WP_REST_Response($data, 404);
        }
        
    } else {
        
        $data['status'] = 'Failed';
        $data['message'] = 'Parameters Missing!';
        $response = new WP_REST_Response($data, 404);

       
        
    }
    
    return $response;
}

//------------Login-end-------------//


//------------Register-start-------------//

add_action( 'rest_api_init', 'localphysio_signin_endpoint' );

function localphysio_signin_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'signin', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_signin'
        )
    );
}

function localphysio_callback_signin( $request_data ) {

    // $key  = $request_data->get_header('Key');

     // $public_key = $_SERVER['PHP_AUTH_USER'];
     // $token      = $_SERVER['PHP_AUTH_PW'];

       
     
    // $user_id = $this->validate_token( false );

     $data = array();
     // $data['pub'] = $public_key;
     // $data['token'] =   $token;
    
    $parameters = $request_data->get_params();
    $title     = $parameters['title'];
    $firstname = $parameters['firstname'];
    $lastname = $parameters['lastname'];
    $email = $parameters['email'];
    $phone = $parameters['phone'];
    $password = $parameters['password'];
    $cfpassword = $parameters['cfpassword'];
    $toc_accept = $parameters['toc_accept']; //1
    $role = $parameters['role']; // staff_type
    $sec_key = $parameters['sec_key'];
    $btn_submit = $parameters['btn_submit'];

    if(is_bool($btn_submit) == 1)
    {

    if($btn_submit == true)
    {


    if ( isset($email) && isset($password) ) {

    $user = get_user_by( 'login', $email);
    if ( $user == null ) {

    $user_id = wp_insert_user( array(
    'user_login' => $email,
    'user_pass' =>  $password,
    'user_email' => $email,
    'first_name' => $firstname,
    'last_name' =>  $lastname,
    'display_name' => $firstname,
    'role' => 'subscriber'
     ));

    if(!is_wp_error($user_login)) {
    
    $auth_key = generate_public_key($email);
    $token = generate_private_key($user_id);

    add_user_meta( $user_id, 'first_name', $firstname);
    add_user_meta( $user_id, 'last_name', $lastname);
    add_user_meta( $user_id, 'nickname', $firstname);
    add_user_meta( $user_id, 'staff_type', $role);
    add_user_meta( $user_id, 'auth_key', $auth_key);
    add_user_meta( $user_id, 'token', $token);

    $data['status'] = 'OK';
    $data['message'] = 'User created successfully!';
    $data['auth_key'] = $auth_key;
    $data['token'] = $token;
    $response = new WP_REST_Response($data, 201);
       }
       else
       {
    $data['status'] = 'Failed';
    $data['message'] = 'wp error occurred!';
    $response = new WP_REST_Response($data, 500);
       }


    }
    else 
    {
        $data['status'] = 'Failed';
        $data['message'] = 'User Exist!';
        $response = new WP_REST_Response($data, 409);
    }

  

     }
     else {
        
        $data['status'] = 'Failed';
        $data['message'] = 'Parameters Missing!';
        $response = new WP_REST_Response($data, 400);
        
    }
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Button not submitted!';
    $response = new WP_REST_Response($data, 400);
}
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Parameters type error';
    $response = new WP_REST_Response($data, 400);
}

    return $response;
    }

//------------Register-end-------------//

//------------Add clinic-start-------------//

    
add_action( 'rest_api_init', 'localphysio_insert_clinic_endpoint' );

function localphysio_insert_clinic_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'authn/2', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_insert_clinic'
        )
    );
}


function localphysio_callback_insert_clinic( $request_data ) {

    $data = array();
    $parameters = $request_data->get_params();
    $provideruid     = $parameters['provideruid'];
    $uid = $parameters['uid'];
    $listings = $parameters['listings'];
    $btn_submit = $parameters['btn_submit'];

    foreach($listings as $key => $value) {
     $title = $value['title'];
     $address_1 = $value['address_1'];
     $address_2 = $value['address_2'];
     $location = $value['location'];
     $postcode = $value['postcode'];
     $telephone_1 = $value['telephone_1'];
     }
    add_user_meta( $user_id, 'first_name', $firstname);
    add_user_meta( $user_id, 'last_name', $lastname);
    add_user_meta( $user_id, 'nickname', $firstname);
    add_user_meta( $user_id, 'staff_type', $role);
    add_user_meta( $user_id, 'auth_key', $auth_key);
    add_user_meta( $user_id, 'token', $token);

}
//------------Add clinic-end-------------//