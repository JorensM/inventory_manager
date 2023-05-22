<?php

    require_once __DIR__ . '/../functions/curl_request.php';
    require_once __DIR__ . '/../functions/generate_jwt.php';

    class Google_Sheets {

        /**
         * Google Sheets API integration class. Works only with service accounts
         */

        /**
         * @var string $api_url Base url for the REST api
         */
        private string $api_url;

        /**
         * @var string $api_key Google's API key (currently not used as this API key is limited to specific calls)
         */
        private string $api_key;

        /**
         * @var string $spreadsheet_id id of the current spreadsheet (can be found in the URL of the spreadsheet's page)
         */
        private string $spreadsheet_id;

        /**
         * @var string $sheet_name name of the current sheet
         */
        private string $sheet_name;

        /**
         * @var string $access_token Google's access token. See generate_access_token() method
         */
        private string $access_token;

        /**
         * @var string $service_account_email email of the service account to use
         */
        private string $service_account_email;

        /**
         * Constructor. Sets constants such as $api_url
         */
        function __construct() {
            $this->api_url = 'https://sheets.googleapis.com/v4/spreadsheets/';
        }

        //--Basic setters & getters--//

        /**
         * Set the service account email to use
         * 
         * @param string $service_account_email email of the service account to use
         * 
         * @return void
         */
        function set_service_account(string $service_account_email) {
            $this->service_account_email = $service_account_email;
        }

        /**
         * Sets current spreadsheet and resets current sheet to 'Sheet1'
         * 
         * @param string $spreadsheet_id id of spreadsheet
         * 
         * @return void
         */
        function set_spreadsheet( string $spreadsheet_id ) {
            $this->sheet_name = 'Sheet1';
            $this->spreadsheet_id = $spreadsheet_id;
            
        }

        /**
         * Sets current sheet
         * 
         * @param string $sheet_name sheet name
         * 
         * @return void
         */
        function set_sheet( string $sheet_name ) {
            //Set sheet name to passed string
            $this->sheet_name = $sheet_name;
        }

        /**
         * Returns API url for the current spreadsheet, in the following format:
         * <API_URL>/spreadsheets/<CURRENT_SPREADSHEET_ID>/
         * 
         * @return string API url for the current spreadsheet
         */
        function get_spreadsheet_url() {
            //return api url + spreadsheet id
            return $this->api_url . $this->spreadsheet_id;
        }

        /**
         * Returns API url for the current sheet, in the following format:
         * <API_URL>/spreadsheets/<CURRENT_SPREADSHEET_ID>/<CURRENT_SHEET_NAME>
         * 
         * @return string API url for the current sheet
         */
        function get_sheet_url() {
            //return api url + spreadhseet id + sheet name
            return $this->get_spreadsheet_url() . '/' . $this->sheet_name;
        }
        
        /**
         * Returns API url for the '/values' endpoint of the current sheet, in the following format:
         * <API_URL>/spreadsheets/<CURRENT_SPREADSHEET_ID>/values/<CURRENT_SHEET_NAME>
         * 
         * @return string API url for the '/values' endpoint of the current sheet
         */
        function get_values_url() {
            //return api url + spreadsheet id + '/values' endpoint + sheet name
            return $this->get_spreadsheet_url() . '/values/' . $this->sheet_name;
        }

        //--Misc methods--//

        /**
         * Generates new access token and sets it to $this->access_token
         * 
         * @return string|null newly generated access token, or null on error
         */
        private function generate_access_token() {

            //Check if service account is linked, and throw error if false
            if( !$this->service_account_email ) {
                throw new Exception('Could not generate access token: service account email not set');
            }

            //grant type parameter (url encoded)
            $grant_type = urlencode( 'urn:ietf:params:oauth:grant-type:jwt-bearer' );

            //JWT token header
            $jwt_header = array(
                'alg' => 'RS256',
                'typ' => 'JWT'
            );

            //JWT token payload
            $jwt_payload = array(
                "iss" => $this->service_account_email,
                "scope" => "https://www.googleapis.com/auth/spreadsheets",
                "aud" => "https://oauth2.googleapis.com/token",
                "exp" => time() + 60,
                "iat" => time()
            );

            //Generate JWT token and store it in a variable
            $jwt = generate_jwt($jwt_header, $jwt_payload, __DIR__ . '/../../google-credentials.json');

            //Store endpoint url in variable and add to it grant type and jwt token as params
            $url = "https://oauth2.googleapis.com/token?grant_type=$grant_type&assertion=$jwt";

            //Make request and store response in a variable
            $res = curl_request($url, 'POST');

            //Check if response has access token
            if( isset( $res['access_token'] ) ) {
                //If it does, store the access token as a member variable of this class
                $this->access_token = $res['access_token'];
                //Return the access token
                return $this->access_token;
            }
            
            //If response doesn't include access token, return null
            return null;
        }

        /**
         * Wrapper for curl_request but with predefined headers
         * 
         * @param   string      $url    endpoint url
         * @param   string      $method request method such as GET, POST, PUT
         * @param   any[]       $data   data to send in form of an assoc. array that will be converted to a JSON string
         * 
         * @return  any[]|null  response of API request, or null on error
         */
        private function api_request( string $url, string $method = 'GET', $data = null) {
            //Set headers
            $headers = array(
                "Authorization: Bearer $this->access_token",
                'Content-Type: application/json'
            );

            //Make request with custom headers and return response
            return curl_request( $url, $method, $data, $headers );
        }

        /**
         * Get number of rows for a specified sheet. If no sheet is specified, current one is used
         * 
         * @param string $sheet_name sheet name. Default null (in which case current sheet will be used)
         * 
         * @return number|null number of rows for specified sheet, or null on error
         */
        function get_row_count( string $sheet_name = null ) {

            //Get the specified sheet
            $sheet = $this->get_sheet( $sheet_name );

            //Check if a valid sheet was returned
            if ( $sheet ) {
                //If true, extract row count from the sheet and return it
                return $sheet['properties']['gridProperties']['rowCount'];
            }

            //If valid sheet wasn't returned, return null
            return null;

        }

        /**
         * Get row number by value in column. 
         * 
         * @param string    $column column to check, such as 'A', 'B', 'F', etc.
         * @param any       $value  value to check for
         * 
         * @return int row number where value was first found, or -1 if not found
         */
        function get_row_number_by_column_value( string $column, $value ) {
            //Generate access token
            $this->generate_access_token();

            //Get row count of sheet
            $row_count = $this->get_row_count();

            //Set url to retrieve all sheet data
            $url = $this->get_values_url() . "!$column" . "1:$column$row_count";

            //Make API request and store response
            $res = $this->api_request( $url );

            //echo $url;

            //Check if response is valid and returned the values
            if ( ! isset( $res['values'] ) ) {
                //If not, return null
                return null;
            }

            //Get all rows from response
            $all_rows = $res['values'];

            //echo '<pre>';
            //print_r($all_rows);
            //echo '</pre>';

            //Loop through all cells until a cell with matching value was found
            foreach ( $all_rows as $row_index => $row) {
                if( isset( $row[0] ) && $row[0] == $value ) {
                    //If matching cell was found, return its index
                    return $row_index + 1;
                }
            }

            //If not match was found, return -1
            return -1;
        }

        /**
         * Convert index number to column letter. For example 1 will return A, 3 will return C, etc.
         * 
         * @param int $index number to convert
         * 
         * @return string respective column letter
         */
        function index_to_column_letter( int $index ) {
            //Store alphabeth in a variable
            $ALPHABET = 'ABCDEFGHIJKLMNOPRSTUVWXYZ';

            //Return the letter at specified index
            return substr($ALPHABET, $index - 1, 1);
        }

        //--Main methods--//

        

        /**
         * Get sheet by name. if name not specified, current sheet will be returned
         * 
         * @param string $sheet_name name of sheet to return. If null, current sheet will be returned
         * 
         * @return any API response as assoc. array, or null on error
         */
        function get_sheet( string $sheet_name = null ) {

            //If no sheet name was specified, use current sheet. Otherwise use specified sheet
            $sheet_name = $sheet_name || $this->sheet_name;

            //Generate access token
            $this->generate_access_token();

            //Get the spreadsheet URL
            $url = $this->get_spreadsheet_url();

            //headers (TODO: use $this->api_request())
            $headers = array(
                "Authorization: Bearer $this->access_token",
                'Content-Type: application/json'
            );

            //Make GET request and store response
            $res = curl_request($url, 'GET', null, $headers);

            //Check if response has any sheets
            if ( isset( $res['sheets'] ) ) {
                //If there are any sheets in the response, loop through them until
                //a sheet with a matching name is found
                foreach ( $res['sheets'] as $sheet ) {
                    if( isset( $sheet['properties']['title'] ) && $sheet['properties']['title'] == $sheet_name){
                        //If a sheet with a matching name was found, return it
                        return $sheet;
                    }
                }
            }

            //Otherwise, return null
            return null;
        }

        /**
         * Write values to specified range in current sheet
         * 
         * @param $range    range such as A1:D3
         * @param $values   array of values to write. Should be a 2d array
         * 
         * @return any[]|null API response, or null on error
         */
        function write( $range, $values ) {
            //Generate access token
            $this->generate_access_token();

            //Get the values URL and add to it the specified range
            $url = $this->get_values_url() . "!$range?valueInputOption=RAW";

            //Data to send to the endpoint
            $data = array(
                'range' => "$this->sheet_name!$range",
                'majorDimension' => 'ROWS',
                'values' => $values
            );

            //Make API request and return response
            return $this->api_request($url, 'PUT', $data);

            //$row_count = $this->get_row_count()
        }

        /**
         * Append values to the end of the current sheet
         * 
         * @param any[]     $values     values to append. Should be a 2D array
         * @param string    $row_length max row length, expressed as a letter of the last row
         * 
         * @return any[]|null API response, or null on error
         */
        function append( $values, string $row_length = 'D' ) {

            //Generate access token
            $this->generate_access_token();

            //Get row count of current sheet
            $row_count = $this->get_row_count();

            //Get the values URL and set its range to be within all cells
            $url = $this->get_values_url() . "!A1:$row_length$row_count:append?valueInputOption=RAW";

            //echo $url;

            //Data to send
            $data = array(
                'range' => "Sheet1!A1:$row_length$row_count",
                'majorDimension' => 'ROWS',
                'values' => $values
            );

            //echo "<pre>";
            //print_r( $values );
            //echo "</pre>";

            //headers
            $headers = array(
                "Authorization: Bearer $this->access_token",
                'Content-Type: application/json'
            );

            //Make API request and store response
            $res = curl_request($url, 'POST', $data, $headers);

            //Return response
            return $res;
        }
    }