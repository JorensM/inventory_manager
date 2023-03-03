<?php

    function ebayRequest($url, $xml, $callname){
        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "X-EBAY-API-COMPATIBILITY-LEVEL" => "1227",
                "X-EBAY-API-CALL-NAME" => $callname,
                "X-EBAY-API-SITEID" => "0",
                "Content-Type" => "text/xml"
                //"X-EBAY-API-IAF-TOKEN" => "JorensMe-Aaa-SBX-475429346-5085b483"
            ],
            CURLOPT_POSTFIELDS => $xml
        ));

        $res = curl_exec($curl);

        curl_close($curl);

        return $res;
    }

?>