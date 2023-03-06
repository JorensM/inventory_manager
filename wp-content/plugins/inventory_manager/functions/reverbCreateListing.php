<?php

    

    function reverbCreateListing($data, $token){
        $curl = curl_init("https://sandbox.reverb.com/api/listings");

        error_log("Creating listing on reverb");

        $headers = [
            "Content-Type: application/hal+json",
            "Accept: application/hal+json",
            "Accept-Version: 3.0" ,
            "Authorization: Bearer $token"
        ];

        error_log(print_r($headers, true));

        $final_data = [
            "make" => $data["make"],
            "model" => $data["model"]
        ];

        error_log("final data: ");
        error_log(print_r($final_data, true));
        
        $final_data_json = json_encode($final_data);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $final_data_json);

        $res = null;

        try{
            $res = curl_exec($curl);
        }catch(Exception $e){
            error_log($e->getMessage());
        }

        error_log($res);
        

        
    }