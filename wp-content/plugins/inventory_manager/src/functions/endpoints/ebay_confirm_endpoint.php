<?php

    /**
     * When linking an eBay account with the app, the user gets directed to this
     * endpoint if they confirmed linking. Additionally, this endpoint generates
     * access and refresh tokens that the app needs in order to use the eBay API
     * 
     * @param $data data passed to the endpoint
     * 
     * @return void
     */
    function ebay_confirm_endpoint( $data ) {
        
        //Store auth token in a variable
        $auth_token = $data['code'];

        //$redirect_url = admin_url() . 'admin.php?page=settings-general';

        //Init curl with
        $curl = curl_init("https://api.ebay.com/identity/v1/oauth2/token");

        //Set method to POST
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        //Generate auth code by encoding eBay app client ID found in the eBay dev dashboard
        $auth = base64_encode('AllanHar-Inventor-PRD-bcd6d2723-74d77282:PRD-cd6d27234717-ecac-4c64-8415-728c');

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $auth"
        );

        //URL encode the auth token
        $urlencoded_token = urlencode($auth_token);
        //Data to send
        $data = "grant_type=authorization_code&redirect_uri=Allan_Harrell-AllanHar-Invent-jxrrf&code=$urlencoded_token";

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $res = curl_exec($curl);

        $res = json_decode($res, true);

        $success = false;

        //If request was successful, update WP options for access and refresh token
        //Otherwise, display error message.

        if ( isset ( $res["access_token"] ) ) {
            update_option("ebay_token", $res["access_token"]);
            $success = true;
        }
        if ( isset( $res["refresh_token"] ) ) {
            update_option("ebay_refresh_token", $res["refresh_token"]);
            $success = true;
        }

        if( $success ) {
            echo "Success. You may leave this page";
        }else{
            echo "Error. Try refreshing the page a few times" . PHP_EOL;
            pr($res);
        }
    }