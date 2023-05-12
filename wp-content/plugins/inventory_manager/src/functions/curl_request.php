<?php

    function curl_request( $url, $method = 'GET', $data = null , $headers = null) {
        $curl = curl_init( $url );


        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
        if( $method == 'POST' || $method == 'PUT' && $data ) {

            // echo 'data: ';
            // echo '<br>';
            // echo "<pre>";
            // echo json_encode( $data, JSON_PRETTY_PRINT );
            // //print_r( $da);
            // echo "</pre>"; 

            curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
        }

        if( $headers ) {
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers ); 
        }

         

        $response = null;

        try {
            $response = curl_exec( $curl );
        } catch ( Exception $e ) {
            return null;
        }

        return json_decode( $response, true );

    }