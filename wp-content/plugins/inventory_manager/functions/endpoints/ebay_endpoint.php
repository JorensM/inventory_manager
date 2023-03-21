<?php


    function ebay_endpoint( $data ) {
        //echo "test";

        $challenge_code = $data['challenge_code'];
        $verification_token = '1234567890123456789012345678901234567890';
        $endpoint = 'https://wordpress-963862-3367102.cloudwaysapps.com/wp-json/inv-mgr/ebay';

        $hash = hash_init('sha256');

        hash_update($hash, $challenge_code);
        hash_update($hash, $verification_token);
        hash_update($hash, $endpoint);

        $response_hash = hash_final($hash);

        $response = array(
            'challengeResponse' => $response_hash
        );

        echo json_encode($response);
        //echo $responseHash;
    }