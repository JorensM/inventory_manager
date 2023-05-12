<?php

    // function base64url_encode($str) {
    //     return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    // }

    // // function base64url_encode(string $input) : string
    // // {
    // //     return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    // // }

    // // function base64url_encode(string $input) {
    // //     return urlencode(base64_encode(($input)));
    // // }

    // function generate_jwt($headers, $payload, $secret = 'secret') {

    //     $headers_encoded = base64url_encode(json_encode($headers));
        
    //     $payload_encoded = base64url_encode(json_encode($payload));
        
    //     $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $secret, true);
    //     $signature_encoded = base64url_encode($signature);
        
    //     $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        
    //     return $jwt;
    // }

    //helper function
    function base64url_encode($data) { 
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
    }

    function generate_jwt($header, $claim, $credentials_path) {
        // Read the JSON credential file my-private-key.json download from Google
        $private_key_file = $credentials_path;
        $json_file = file_get_contents($private_key_file);
        
        $info = json_decode($json_file);
        $private_key = $info->{'private_key'};
        
        //{Base64url encoded JSON header}
        $jwtHeader = base64url_encode(json_encode($header));
        
        //{Base64url encoded JSON claim set}
        $now = time();
        $jwtClaim = base64url_encode(json_encode($claim));
        
        $data = $jwtHeader.".".$jwtClaim;
        
        // Signature
        $Sig = '';
        openssl_sign($data,$Sig,$private_key,'SHA256');
        $jwtSign = base64url_encode( $Sig  );
        
        
        //{Base64url encoded JSON header}.{Base64url encoded JSON claim set}.{Base64url encoded signature}
        
        $jwtAssertion = $data.".".$jwtSign;

        return $jwtAssertion;
    }
    
    