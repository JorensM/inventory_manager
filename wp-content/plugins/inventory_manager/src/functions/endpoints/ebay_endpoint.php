<?php


/**
 * This endpoint is required by eBay's policy. It is only called by eBay and should not be called
 * by the user.
 * 
 * @param $data data sent to the endpoint
 * 
 * @return void
 */
function ebay_endpoint( $data ) {

    //Store request method in a variable
    $method = $_SERVER['REQUEST_METHOD'];

    //Check request method and act accoringly
    if ( $method == 'GET' ) {
        //If request method is GET

        //Get challenge code sent by eBay
        $challenge_code = $data['challenge_code'];

        //Get verification code (Found in the eBay developer dashboard)
        $verification_token = '1234567890123456789012345678901234567890';

        //URL of this endpoint. Used as part of encoding the response code
        $endpoint = 'https://wordpress-963862-3367102.cloudwaysapps.com/wp-json/inv-mgr/ebay';

        //Encode a code by using the challenge code, verification code and endpoint url.
        $hash = hash_init('sha256');
        hash_update($hash, $challenge_code);
        hash_update($hash, $verification_token);
        hash_update($hash, $endpoint);

        //Store code into a variable
        $response_hash = hash_final($hash);

        //Make assoc. array of the JSON response
        $response = array(
            'challengeResponse' => $response_hash
        );

        //Echo response array as JSON
        echo json_encode($response);
    } else if ( $method == 'POST' ) {
        //If request method is POST

        //When a user requests account deletion, this endpoint is called with POST.
        //Since this is a private app, there is technically no need for this POST endpoint
        //But it must return status code 200, otherwise you will get a warning from eBay
    }    
}