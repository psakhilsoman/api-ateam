<?php
/**
 */
/*
Plugin Name: Api
Plugin URI: mailto:psakhilsoman@gmail.com
Description: Custom API
Version: 1.0
Author: Akhil
Author URI: mailto:psakhilsoman@gmail.com
License: GPLv2 or later
Text Domain: api
*/


    function generate_public_key( $user_email = '' ) {
        $auth_key = defined( 'AUTH_KEY' ) ? AUTH_KEY : '';
        $public   = hash( 'md5', $user_email . $auth_key . date( 'U' ) );
        return $public;
    }

    
   

   function validate_token( $output = true ) {
        // Check that we're trying to authenticate
    }


//------------Login-start-------------//
add_action( 'rest_api_init', 'localphysio_authn_endpoint' );

function localphysio_authn_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'login', // Endpoint
        array(
            'methods'  => 'GET',
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
               
                $new_public_key = get_user_meta( $userdata->ID, 'auth_key' , true );
                if($new_public_key == '')
                {
                        $new_public_key = generate_public_key($email);
                        update_user_meta( $userdata->ID, 'auth_key', $new_public_key );

                }

  
               



                $data['status'] = 'OK';
            
                $data['received_data'] = array(
                    'name'     => $email,
                    'data'     => $userdata,
                    'auth_key' => $new_public_key,
                    // 'secret_key' => $new_secret_key 

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
        'signup', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_signin'
        )
    );
}

function localphysio_callback_signin( $request_data ) {

 

     $data = array();

    
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
    $domain_flag = $parameters['domain_flag'];


    
    if(is_email($email)) {
    
    if(is_bool($btn_submit) == 1)
    {

    if($btn_submit == true)
    {


    if ( count($parameters) == 12 ) {

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
    //$token = generate_private_key($user_id);

    add_user_meta( $user_id, 'first_name', $firstname);
    add_user_meta( $user_id, 'last_name', $lastname);
    add_user_meta( $user_id, 'nickname', $firstname);
    add_user_meta( $user_id, 'staff_type', $role);
    add_user_meta( $user_id, 'auth_key', $auth_key);
    add_user_meta( $user_id, 'domain_flag', $domain_flag);
    

    $data['status'] = 'OK';
    $data['message'] = 'User created successfully!';
    $data['uid'] = $user_id;
    $data['auth_key'] = $auth_key;
   // $data['token'] = $token;
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
        $data['message'] = 'Parameters Missing! or Check Parameters';
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

}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Email id is not valid';
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
        'signup/2', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_insert_clinic'
        )
    );
}


function localphysio_callback_insert_clinic( $request_data ) {

    $data = array();
    $key  = $request_data->get_header('Auth-key');
    $parameters = $request_data->get_params();
    $provideruid = $parameters['provideruid'];
    $uid = $parameters['uid'];
    $listings = $parameters['listings'];
    $btn_submit = $parameters['btn_submit'];

    $permission = get_user_meta( $uid, 'auth_key' , true );

    if($uid != null) {

    if($key == $permission)
    {
             if(is_bool($btn_submit) == 1)
    {

    if($btn_submit == true)
    {

    if(!get_user_meta( $uid, 'title'))
    {

    foreach($listings as $key => $value) {
     $title = $value['title'];
     $address_1 = $value['address_1'];
     $address_2 = $value['address_2'];
     $location = $value['location'];
     $postcode = $value['postcode'];
     $telephone_1 = $value['telephone_1'];
     $num_workers = $value['num_workers'];
     $specialization = $value['specialization'];
     $treatments = $value['treatments'];
     }

    $specialization_data = implode(",", $specialization );
    $treatments_data = implode(",", $treatments );
    add_user_meta( $uid, 'title',  $title);
    add_user_meta( $uid, 'address_1',  $address_1);
    add_user_meta( $uid, 'address_2', $address_2);
    add_user_meta( $uid, 'location', $location);
    add_user_meta( $uid, 'location', $location);
    add_user_meta( $uid, 'postcode', $postcode);
    add_user_meta( $uid, 'telephone_1', $telephone_1);
    add_user_meta( $uid, 'num_workers', $num_workers);
    add_user_meta( $uid, 'specialization', $specialization_data);
    add_user_meta( $uid, 'treatments', $treatments_data);
   
   
    $data['status'] = 'OK';
    $data['message'] = 'Clinic Added successfully!';
    $response = new WP_REST_Response($data, 201);


    }
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'Clinic exist in this account';
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
    }
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'Authentication error';
    $response = new WP_REST_Response($data, 400);
    }

    }
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'uid cannot empty';
    $response = new WP_REST_Response($data, 400);
    }

 


    return  $response;

}
//------------Add clinic-end-------------//


//------------Add payment start-------------//

add_action( 'rest_api_init', 'localphysio_insert_payment_endpoint' );

function localphysio_insert_payment_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'signup/3', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_insert_payment'
        )
    );
}


function localphysio_callback_insert_payment( $request_data ) {

    $data = array();
    $parameters = $request_data->get_params();
    $key  = $request_data->get_header('Auth-key');
    $provideruid = $parameters['provideruid'];
    $uid = $parameters['uid'];
    $paymentData = $parameters['paymentData'];

    if($uid != null) {

    $permission = get_user_meta( $uid, 'auth_key' , true );

    if($key == $permission)
    {

    foreach($paymentData as $key => $value) {
     $creditor = $value['creditor'];
     $mandate = $value['mandate'];
     $customer = $value['customer'];
     $customer_bank_account = $value['customer_bank_account'];
     }
   if(!get_user_meta( $uid, 'creditor'))
    {
    add_user_meta( $uid, 'creditor',  $creditor);
    add_user_meta( $uid, 'mandate', $mandate);
    add_user_meta( $uid, 'customer', $customer);
    add_user_meta( $uid, 'customer_bank_account', $customer_bank_account);

    $data['status'] = 'OK';
    $data['message'] = 'Payment details added successfully!';
    $response = new WP_REST_Response($data, 201);
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Payment exists in this account';
    $response = new WP_REST_Response($data, 400);
}
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Authentication error';
    $response = new WP_REST_Response($data, 400);
}

}
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'uid cannot empty';
    $response = new WP_REST_Response($data, 400);
    }

    return  $response;
}

//------------Add payment end-------------//

//------------Add clinic timing start-------------//

add_action( 'rest_api_init', 'localphysio_insert_clinic_timing_endpoint' );

function localphysio_insert_clinic_timing_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'signup/clinic-details', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_insert_clinic_timing'
        )
    );
}


function localphysio_callback_insert_clinic_timing( $request_data ) {
   
    $data = array();
    $parameters = $request_data->get_params();
    $key  = $request_data->get_header('Auth-key');
    $provideruid = $parameters['provideruid'];
    $uid = $parameters['uid'];
    $initialAssessment = $parameters['initialAssessment'];
    $followUpTreatment = $parameters['followUpTreatment'];
    $openingTimes = $parameters['openingTimes']; //array
    $parkingMode = $parameters['parkingMode'];
    $paymentMode = $parameters['paymentMode'];
    $insurancesAccepted = $parameters['insurancesAccepted']; //array
    $clinicDescription = $parameters['clinicDescription'];
    $logo = $parameters['logo'];

    if($uid != null) {

    $permission = get_user_meta( $uid, 'auth_key' , true );

    if($key == $permission)
    {
     
      if(!get_user_meta( $uid, 'openingTimes'))
    {
     add_user_meta( $uid, 'initialAssessment',  $initialAssessment);
     add_user_meta( $uid, 'followUpTreatment',  $followUpTreatment);
     add_user_meta( $uid, 'parkingMode',  $parkingMode);
     add_user_meta( $uid, 'paymentMode',  $paymentMode);
     add_user_meta( $uid, 'clinicDescription',  $clinicDescription);
     add_user_meta( $uid, 'logo',  $logo);

    add_user_meta( $uid, 'openingTimes',  $openingTimes);
    add_user_meta( $uid, 'insurancesAccepted', $insurancesAccepted);

    $data['status'] = 'OK';
    $data['message'] = 'Clinic timing added successfully!';
    $response = new WP_REST_Response($data, 201);
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Clinic timing exists in this account';
    $response = new WP_REST_Response($data, 400);
}
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Authentication error';
    $response = new WP_REST_Response($data, 400);
}

}
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'uid cannot empty';
    $response = new WP_REST_Response($data, 400);
    }

    return  $response;
}
//------------Add clinic timing end-------------//


//------------Add practitioner-details start-------------//

add_action( 'rest_api_init', 'localphysio_insert_practitioner_details_endpoint' );

function localphysio_insert_practitioner_details_endpoint(){
    register_rest_route(
        'api/v1/', // Namespace
        'signup/practitioner-details', // Endpoint
        array(
            'methods'  => 'POST',
            'callback' => 'localphysio_callback_insert_practitioner_details'
        )
    );
}


function localphysio_callback_insert_practitioner_details( $request_data ) {

    $data = array();
    $parameters = $request_data->get_params();
    $key  = $request_data->get_header('Auth-key');
    $provideruid = $parameters['provideruid'];
    $uid = $parameters['uid'];
    $firstName = $parameters['firstName'];
    $lastName = $parameters['lastName'];
    $profileImage = $parameters['profileImage'];
    $HCPC = $parameters['HCPC'];
    $CSP = $parameters['CSP'];
    $specialization = implode(",", $parameters['specialization'] ); //array
    $description = $parameters['description'];

       if($uid != null) {

    $permission = get_user_meta( $uid, 'auth_key' , true );
    if($key == $permission)
    {
        if(!get_user_meta( $uid, 'p_firstName'))
    {
      add_user_meta( $uid, 'p_firstName',  $firstName);
      add_user_meta( $uid, 'p_lastName',  $lastName);
      add_user_meta( $uid, 'HCPC',  $HCPC);
      add_user_meta( $uid, 'CSP',  $CSP);
      add_user_meta( $uid, 'p_specialization',  $specialization);
      add_user_meta( $uid, 'p_description',  $description);

    $data['status'] = 'OK';
    $data['message'] = 'Practitioner details  added successfully!';
    $response = new WP_REST_Response($data, 201);
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Practitioner details  exists in this account!';
    $response = new WP_REST_Response($data, 400);
}
}
else
{
    $data['status'] = 'Failed';
    $data['message'] = 'Authentication error';
    $response = new WP_REST_Response($data, 400);
}
}
    else
    {
    $data['status'] = 'Failed';
    $data['message'] = 'uid cannot empty';
    $response = new WP_REST_Response($data, 400);
    }
    return  $response;
}
    
//------------Add practitioner-details end-------------//