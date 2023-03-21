<?php

    function ebay_confirm_endpoint( $data ) {
        //print_r($data);
        $auth_token = $data['code'];

        //echo $token;
        //echo PHP_EOL;
        //echo $data['expires_in'];
        
        //update_option('ebay_token', $token);

        $redirect_url = admin_url() . 'admin.php?page=settings-general';

        $curl = curl_init("https://api.ebay.com/identity/v1/oauth2/token");

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $auth = base64_encode('AllanHar-Inventor-PRD-bcd6d2723-74d77282:PRD-cd6d27234717-ecac-4c64-8415-728c');

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $auth"
        );

        // $data = array(
        //     'grant_type' => 'authorization_code',
        //     'redirect_uri' => 'https://wordpress-963862-3367102.cloudwaysapps.com/wp-json/inv-mgr/ebay_confirm',
        //     'code' => urlencode($auth_token)
        // );

        $urlencoded_token = urlencode($auth_token);
        $data = "grant_type=authorization_code&redirect_uri=Allan_Harrell-AllanHar-Invent-jxrrf&code=$urlencoded_token";
        echo $data;
        echo PHP_EOL;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($curl);

        //echo $res;
        print_r($res);

        //echo "<a href='$redirect_url'>Back</a>";

        //header( "Location: $redirect_url" );
        //die();
    }