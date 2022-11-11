<?php
/**
* Plugin Name: Bar d'Office API
* Description: This plugin contains all functions with regards to API settings and calls.
* Author: Christophe Demeulemeester
* Company: Appelblauwzeegroen bv
* Version: 0.1
* Last updated: 1st of August 2022
*/

/* Your code goes below here. */

/**
 * Get API key depending on the source
 *
 * @param [string] $source
 * @return void
 */
function get_api_url_key($source, $request) {
    global $wpdb;
    $page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", $source, 'api-setting' ) );
    $baseURL = get_post_meta($page, 'wpcf-api-settings-base-url', true);
    $key = get_post_meta($page, 'wpcf-api-settings-key', true);
    return $baseURL.$request.$key;
}

function get_request_from_source($source, $request){
    $url = get_api_url_key($source, $request);
    $out = wp_remote_get( $url);
	$body = wp_remote_retrieve_body( $out);	
	$json_data = file_get_contents($url);
    return $json_data;
}

function get_bdo_customer_credentials(){
    $bdoCredentials = [
        'contactId' =>  28402,
        'contactName' =>    'Info Bar dOffice',
        'contactEmail'  =>  'info@bardoffice.com',
        'customerflowId'    =>  710780,
        'customerflowKey'   =>  'CUST/001257',
        'customerflowName'  =>  'BAR D OFFICE'
    ];
    //$bdoCredentials = wp_json_encode( $bdoCredentials );
    return $bdoCredentials;
}

function refresh_zoho_token(){
    global $wpdb;
    $page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", 'Zoho', 'api-setting' ) );
    $baseURL = 'https://accounts.zoho.eu/oauth/v2/token?refresh_token=';
    $refresh_token = get_post_meta($page, 'wpcf-api-settings-zohorefreshtoken',true);
    $client_id = get_post_meta($page, 'wpcf-api-settings-zohoclientid',true);
    $client_secret = get_post_meta($page, 'wpcf-api-settings-zohoclientsecret',true);
    $redirect_uri = get_post_meta($page, 'wpcf-api-settings-zohoredirecturi',true);
    $grant_type = 'refresh_token';
    $url = $baseURL.$refresh_token.'&client_id='.$client_id.'&client_secret='.$client_secret.'&redirect_uri='.$redirect_uri.'&grant_type='.$grant_type;
    $respons = wp_remote_post($url);
    $responsbody = $respons['body'];
    $responsbody = json_decode($responsbody);
    $newtoken = $responsbody->access_token;
    update_post_meta($page, 'wpcf-api-settings-zohoaccesstoken', $newtoken);
}

function get_zoho_api_credentials(){
    global $wpdb;
    $page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type= %s", 'Zoho', 'api-setting' ) );
    $client_id = get_post_meta($page, 'wpcf-api-settings-zohoclientid',true);
    $client_secret = get_post_meta($page, 'wpcf-api-settings-zohoclientsecret',true);
    $redirect_uri = get_post_meta($page, 'wpcf-api-settings-zohoredirecturi',true);
    $token = get_post_meta($page, 'wpcf-api-settings-zohoaccesstoken', true);
    $zohoCredentials = [
        'clientID'      =>  $client_id,
        'clientSecret'  =>  $client_secret,
        'redirect_uri'  =>  $redirect_uri,
        'token'         =>  $token
    ];
    return $zohoCredentials;
}

function get_zoho_curl($url){
    $credentials = get_zoho_api_credentials();
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
    "Authorization: Zoho-oauthtoken ".$credentials['token'],
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    //var_dump($resp);
    $resp2 = (json_decode($resp, true));
    $resp2 = $resp2['code'];

    if($resp2 !=0){
        refresh_zoho_token();
        get_curl($url);
    }else{
        return ($resp);
    }
}

function post_zoho_curl($url, $body){
    $credentials = get_zoho_api_credentials();
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $headers = array(
    "Content-Type: application/json;charset=UTF-8",
    "Authorization: Zoho-oauthtoken ".$credentials['token'],
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = $body;

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

    $resp = curl_exec($curl);
   
    print_r($body);
    print_r('<br><br>');
    print_r($resp);
    curl_close($curl);
    //var_dump($resp);
    $resp2 = (json_decode(($resp), true));
    if($resp2['code'] !=0){
        refresh_zoho_token();
        get_zoho_curl($url);
    }else{
        return ($resp2);
    }
}

function put_zoho_curl($url, $body){
    $credentials = get_zoho_api_credentials();
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $headers = array(
    "Content-Type: application/json;charset=UTF-8",
    "Authorization: Zoho-oauthtoken ".$credentials['token'],
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = $body;

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

    $resp = curl_exec($curl);
   
    print_r($body);
    print_r('<br><br>');
    print_r($resp);
    curl_close($curl);
    //var_dump($resp);
    $resp2 = (json_decode(($resp), true));
    if($resp2['code'] !=0){
        refresh_zoho_token();
        get_zoho_curl($url);
    }else{
        return ($resp2);
    }
}



/* Your code goes above here. */