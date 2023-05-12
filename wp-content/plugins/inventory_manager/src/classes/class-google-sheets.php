<?php

    require_once __DIR__ . '/../functions/curl_request.php';
    require_once __DIR__ . '/../functions/generate_jwt.php';

    class Google_Sheets {

        private string $api_url;

        private string $api_key;

        private string $spreadsheet_id;

        private string $sheet_name;

        private string $access_token;

        function __construct() {
            $this->api_url = 'https://sheets.googleapis.com/v4/spreadsheets/';
        }

        function set_spreadsheet( $spreadsheet_id ) {
            $this->sheet_name = 'Sheet1';
            $this->spreadsheet_id = $spreadsheet_id;
            
        }

        function set_sheet( $sheet_name ) {
            $this->sheet_name = $sheet_name;
        }

        function get_values( $range ) {

        }

        function get_sheet_url() {
            return $this->api_url . $this->spreadsheet_id . '/' . $this->sheet_name;
        }
        
        function get_values_url() {
            return $this->api_url . $this->spreadsheet_id . '/values/' . $this->sheet_name;
        }

        function generate_access_token() {

            $grant_type = urlencode( 'urn:ietf:params:oauth:grant-type:jwt-bearer' );

            $jwt_header = array(
                'alg' => 'RS256',
                'typ' => 'JWT'
            );

            $jwt_payload = array(
                "iss" => "test-541@inventory-manager-386314.iam.gserviceaccount.com",
                "scope" => "https://www.googleapis.com/auth/spreadsheets",
                "aud" => "https://oauth2.googleapis.com/token",
                "exp" => time() + 60,
                "iat" => time()
            );

            $jwt = generate_jwt($jwt_header, $jwt_payload, __DIR__ . '/../../google-credentials.json');

            $url = "https://oauth2.googleapis.com/token?grant_type=$grant_type&assertion=$jwt";

            $res = curl_request($url, 'POST');

            if( isset( $res['access_token'] ) ) {
                $this->access_token = $res['access_token'];
                return $this->access_token;
            }
            
            return null;
        }

        function append( $values ) {

            $this->generate_access_token();

            $url = $this->get_values_url() . '!A1:C3:append?valueInputOption=RAW';

            //echo $url;

            $data = array(
                'range' => 'Sheet1!A1:C3',
                'majorDimension' => 'ROWS',
                'values' => $values
                // 'resource' => array(
                //     'values' => $values
                // )
            );

            //echo "<pre>";
            //print_r( $values );
            //echo "</pre>";

            $headers = array(
                "Authorization: Bearer $this->access_token",
                'Content-Type: application/json'
            );

            $res = curl_request($url, 'POST', $data, $headers);

            return $res;
        }
    }