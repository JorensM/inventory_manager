<?php

    //--Requires--//

    //Classes
    require_once "Listing_Manager_Interface.php";

    //Echo preformatted object/variable
    function pr($obj) {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    }

    class Ebay_Listing_Manager implements Listing_Manager_Interface {

        /**
         * eBay listing manager.
         */


        //--Variables--//

        /**
         * @var string $mode mode, can be either 'live' or 'sandbox'
         */
        private string $mode;

        /**
         * @var string $api_url base url of the traditional API. Changes depending on mode
         */
        private string $api_url;

        /**
         * @var string $rest_api_url url of the REST API. Changes depending on mode
         */
        private string $rest_api_url;

        /**
         * @var string $apiz_api_url url of the REST API that is prefixed with 'apiz.'. Changes depending on mode
         * More info: Some of the eBay REST API urls have the prefix 'apiz.' before the URL.
         */
        private string $apiz_api_url;

        /**
         * @var string $token User access token
         */
        private string $token;

        /**
         * @var string Woo product's condition field mapping to eBay's conditions. Defined in the constructor
         */
        private array $condition_mappings;


        //--Functions--//

        /**
         * Constructor. Initializes private variables, sets mode and token.
         * 
         * @param array $auth_data associative array with single element:
         *      ['token' => '123..321']
         * @param string $mode mode. Can be either 'sandbox' or 'live'. Default 'sandbox'
         */
        function __construct(array $auth_data, string $mode = 'sandbox') {
            //Initializes private variables
            $this->token = $auth_data["token"];
            $this->mode = $mode;
            //Set API urls depending on mode
            if ( $mode == 'live' ) {
                $this->api_url = "https://api.ebay.com/ws/api.dll";
                $this->rest_api_url = 'https://api.ebay.com/';
                $this->apiz_api_url = 'https://apiz.ebay.com/';
            }
            else{
                $this->api_url = "https://api.sandbox.ebay.com/ws/api.dll";
                $this->rest_api_url = 'https://api.sandbox.ebay.com/';
                $this->apiz_api_url = 'https://apiz.sandbox.ebay.com/';
                
            }

            //Define eBay's condition code mappings to the Woo product conditions
            $this->condition_mappings = [
                "used" => "3000",
                "non-functioning" => "7000"
            ];
        }

        /**
         * Make REST API request to the eBay API.
         * 
         * @param string $endpoint endpoint url (without the base url)
         * @param string $method request method ('GET', 'POST', etc...). Default 'GET'
         * @param array $data associative array of data to pass that will be converted to JSON. Default null
         * @param string $type type of request (either regular or 'apiz' request). if set to something other than null,
         * 'apiz' url will be used for the request. Otherwise will use regular REST API url. Default null
         * 
         * @return array|null request response as assoc. array, or NULL on error
         */
        function rest_api_request( string $endpoint, $method = "GET", $data = null, $type = null){
            $url = null;

            //Determine the base url based on $type param
            if(!$type) {
                $url = $this->rest_api_url;
            }else {
                $url = $this->apiz_api_url;
            }

            //Append $endpoint to the $url
            $url .= $endpoint;

            //Init curl with the full URL
            $curl = curl_init($url);

            //Define headers
            $headers = [
                "Content-Type:application/json",
                'Accept:application/json',
                "Authorization:Bearer $this->token"
            ];

            //Set headers
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //Set request method
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            //Make cURL return the response instead of echoing it.
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            //If method allows, pass data to request body
            if($method == 'POST' || $method == 'PUT'){
                $data_str = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_str );
            }
            
            $res = null;

            //Make call
            try{
                $res = curl_exec($curl);
            }catch(Exception $e){
                //error_log("Error making listing request: ");
                //error_log($e->getMessage());

                return null;
            }

            //Return response as assoc. array
            $res_array = json_decode($res, true);
            return $res_array;
        }

        /**
         * Make API request to the traditional API that uses XML
         * 
         * @param string $req_name request name, for example 'AddItem', 'GeteBayOfficialTime', etc.
         * @param string|null $xml_data_str stringified XML data to pass( without prolog and some other things. See xml_str() method
         * for more info. Default null
         * 
         * @return SimpleXMLElement|null XML object of response, or NULL on error
         */
        function api_request( string $req_name, $xml_data_str = null ) {
            //Init cURL
            $curl = curl_init( $this->api_url );

            //Define headers
            $headers = array(
                "Content-Type:text/xml",
                'X-EBAY-API-SITEID:0', //Site id. 0 - US
                'X-EBAY-API-COMPATIBILITY-LEVEL:967', //Compat. level
                "X-EBAY-API-CALL-NAME:$req_name", //Call name
                "X-EBAY-API-IAF-TOKEN:$this->token" //User access token
            );

            //Set headers
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
            //Set request type to 'POST'
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" );
            //Make cURL return the response instead of echoing it
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );


            //Wrap the $xml_data_str with additional tags
            $data_str = $this->xml_str( $req_name, $xml_data_str );

            //Set request body
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_str );

            //Make request
            $res = null;
            try{
                $res = curl_exec($curl);
            }catch(Exception $e){
                return null;
            }

            //Return response as XML object
            $res_xml = simplexml_load_string( $res ); 
            return $res_xml;

        }

        /**
         * Wrap provided XML string in <xxxRequest> tag and add prolog
         * 
         * @param string $req_name name of request.
         * @param string $xml_str XML string to add to.
         * 
         * @return string XML string with extra data
         */
        function xml_str ( string $req_name, string $xml_str ): string {
            return "<?xml version='1.0' encoding='utf-8'?><{$req_name}Request xmlns='urn:ebay:apis:eBLBaseComponents'>    
                $xml_str
                <ErrorLanguage>en_US</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
            </{$req_name}Request>";
        }

        /**
         * Create new listing on eBay based on a Woo product
         * 
         * @param WC_Product $product product to create listing for
         * 
         * @return array assoc. array of API response
         */
        function create_listing ( WC_Product $product ) {
            //Convert product data to XML string
            $xml = $this->product_to_listing_request_data( $product );

            //Call the 'AddItem' API endpoint
            $response = $this->api_request( 'AddItem', $xml );

            //Check response
            if ( isset( $response->ItemID ) ) {
                //If response contains ItemID, that means the listing was created

                //Add created listing's ID as meta data to the product
                $listing_id = strval($response->ItemID);
                $product->add_meta_data('ebay_id', $listing_id);
                $product->save();

                //Log 'listing created' into the activity log
                $ebay_link = ebay_product_link($product);
                $product_link = product_link($product);
                log_activity("eBay", "Created $ebay_link for product $product_link");
            }

            //Return response of API requset
            return $response;

        }

        /**
         * Check if listing has ended on eBay and mark it as sold on WooCommerce if true
         * 
         * @param WC_Product $product product which's listing to check
         * @param bool $save whether the call $product->save() after marking as sold
         * 
         * @return void|null returns nothing on success, and null on error
         */
        function check_listing_and_mark_sold( WC_Product $product, bool $save = true) {
            //Get eBay listing Id
            $listing_id = $product->get_meta('ebay_id');

            //Check if listing ID exists
            if ( !$listing_id ) {
                error_log("eBay Listing ID not found in check_listing_and_mark_sold()");
                return null;
            }

            //Get the eBay Listing
            $listing = $this->get_listing( $product )->Item;

            //Check status of listing and mark Woo product as sold if listing status is not 'Active'
            if( isset( $listing->SellingStatus->ListingStatus ) && $listing->SellingStatus->ListingStatus != 'Active') {
                mark_product_sold( $product, $save );
            }
        }

        /**
         * Generate a new access token if the current one is expired
         * 
         * @return void
         */
        function maybe_refresh_token() {
            //Check if token is expired
            $is_token_expired = $this->check_token();

            //If token is expired, generate a new one and set $this->token to the new value
            if ( $is_token_expired ) {
                $this->refresh_token();
            }
        }

        /**
         * Generate new access token
         * 
         * @return void
         */
        function refresh_token() {
            //Set target URL depending on mode
            $url = $this->mode == 'live' ? 'https://api.ebay.com/identity/v1/oauth2/token' : 'https://api.sandbox.ebay.com/identity/v1/oauth2/token';

            //init cURL
            $curl = curl_init($url);

            //set request method to POST
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

            //Auth token
            $auth = base64_encode('AllanHar-Inventor-PRD-bcd6d2723-74d77282:PRD-cd6d27234717-ecac-4c64-8415-728c');

            //Define headers
            $headers = array(
                'Content-Type: application/x-www-form-urlencoded', //Content type
                "Authorization: Basic $auth" //Auth token
            );

            //Get refresh token. This refresh token gets generated when user links with ebay from the setttings
            $refresh_token = get_option('ebay_refresh_token');

            //Data to send
            $data = "grant_type=refresh_token&refresh_token=$refresh_token&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly";
            
            //Set request headers
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //Set request body
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            //Make cURL return response instead of echoing it
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            
            //Make request and store response
            $res = curl_exec($curl);

            //Convert response to assoc. array
            $res_json = json_decode($res, true);

            //Check if access token was successfully generated
            if( isset( $res_json['access_token'] ) ) {
                //If token was generated, update it in the options and in this class
                update_option('ebay_token', $res_json['access_token']);
                $this->token = $res_json['access_token'];

                error_log('Successfully updated eBay token');
            }else{
                error_log('Failed to update eBay refresh token');
            }
        }


        /**
         * Check if access token is expired
         * 
         * @return bool true if expired, false if not
         */
        function check_token() {

            //Make generic eBay API request
            $response = $this->api_request("GeteBayOfficialTime", '');

            //Token not expired by default
            $is_token_expired = false;

            //eBay's 'token expired' error code
            $expired_error_code = 21917053;

            /*
                Check if response returned any errors, and if yes, 
                check if error with $expired_error_code is present.
                If present, then token is expired
            */
            if ( isset( $response->Errors ) ) {
                if( is_array( $response->Errors ) ){
                    foreach ( $response->Errors as $error ) {
                        if( $error->ErrorCode == $expired_error_code ) {
                            $is_token_expired = true;
                        }
                    }
                }
                else if ( $response->Errors->ErrorCode == $expired_error_code ){
                    $is_token_expired = true;
                }
                
            }

            //Return whether token is expired or not
            return $is_token_expired;
        }

        /**
         * Generate XML for a single <ItemSpecifics> item
         * 
         * @param string $name name of the specific
         * @param string $value value of the specific
         * 
         * @return string XML string of <NameValueList>
         */
        function item_specific_xml(string $name, string $value): string {
            return "<NameValueList>
                <Name>$name</Name>
                <Value>$value</Value>
            </NameValueList>";
        }

        /**
         * Convert WC_Product to eBay's XML <Item>
         * 
         * @param WC_Product $product product to convert
         * 
         * @return string <Item> XML string
         */
        function product_to_listing_request_data ( WC_Product $product ) {

            //Get description of item
            $description = $product->get_meta( 'notes_field' );
            //Get Listing ID
            $listing_id = $product->get_meta('ebay_id');
            //Get shipping profile id for the current product
            $shipping_profile_id = $product->get_meta('ebay_shipping_profile');
            //Get condition value of product
            $condition_field = $product->get_meta('condition_field');
            //Get brand info from product
            $brand_info = $product->get_meta('brand_info');
            //Get body type from product
            $body_type = $product->get_meta('body_type_field');

            //wrap listing ID in <ItemID> tags, or set $listing_id to empty string if $listing_id is undefined
            $listing_id = $listing_id ? "<ItemID>$listing_id</ItemID>" : '';

            //XML string for shipping data
            $shipping_xml = '';

            

            //set shipping profile XML if present
            if ( $shipping_profile_id ) {
                $shipping_xml = "
                    <SellerProfiles>
                        <SellerShippingProfile>
                            <ShippingProfileID>$shipping_profile_id</ShippingProfileID>
                        </SellerShippingProfile>
                    </SellerProfiles>
                ";
            }else{
                $shipping_xml = "
                    <ShipToLocations>None</ShipToLocations>
                ";
            }

            //Get eBay category IDs respective to which categories the product has
            $category_ids = $product->get_category_ids();
            //XML string for categories
            $categories_xml = '';
            //$i used to check foreach loop's first iteration
            $i = -1;
            //Loop through categories of product
            foreach($category_ids as $category_id){
                //Incerement $i
                $i++;

                //Get eBay category ID of the current category
                $ebay_cat_id = get_term_meta($category_id, 'ebay_category_id', true);

                //Check if first iteration of loop and if category has an eBay category ID stored
                if( $ebay_cat_id && $i < 1 ) {
                    //If this is the first iteration, set category to primary category
                    $categories_xml .= "<PrimaryCategory><CategoryID>$ebay_cat_id</CategoryID></PrimaryCategory>";
                }else if ( $ebay_cat_id ) {
                    //Otherwise, set category to secondary category
                    $categories_xml .= "<SecondaryCategory><CategoryID>$ebay_cat_id</CategoryID></SecondaryCategory>";
                }
            }

            //Set condition data field
            //Conditions XML
            $condition_xml = '';
            

            //Check if condition field is set and if there is a respective condition mapping
            if ( $condition_field && isset( $this->condition_mappings[ $condition_field ] ) ) {
                //If yes, then add XML string to $condition_xml
                $condition_xml = '<ConditionID>' . $this->condition_mappings[ $condition_field ] . '</ConditionID>';
            }

            //XML string for <ItemSpecifics>
            $item_specifics_xml = '';            

            //Check if brand info is defined, and add it to $item_specifics_xml if yes
            if( $brand_info ) {
                $item_specifics_xml .= $this->item_specific_xml('Brand', $brand_info);
            }

            //Do same for body type
            if( $body_type ) {
                $item_specifics_xml .= $this->item_specific_xml('Type', $body_type);
            }

            //If there are any item specifics, wrap the variable in <ItemSpecifics> tags
            if( $item_specifics_xml != '' ) {
                $item_specifics_xml = '<ItemSpecifics>' . $item_specifics_xml . '</ItemSpecifics>';
            }

            //Get image urls
            //Get image IDS of product
            $image_ids = $product->get_gallery_image_ids();
            //Array to store image URLS
            $image_urls = [];
            //Iterate through all image IDS
            foreach($image_ids as $image_id){
                //Store XML with image url in <PictureURL> tags in the $image_urls array
                array_push($image_urls, '<PictureURL>' . wp_get_attachment_url($image_id) . '</PictureURL>');
            }

            //XML string for images
            $images_xml = implode('', $image_urls);

            //Final XML
            $xml = "
                <Item>
                    $listing_id
                    <Title>{$product->get_title()}</Title>
                    <Description>{$description}</Description>
                    <StartPrice>1.0</StartPrice>
                    <Currency>USD</Currency>
                    <Country>US</Country>
                    <ListingDuration>Days_7</ListingDuration>
                    $categories_xml
                    $condition_xml
                    $item_specifics_xml
                    <PostalCode>95125</PostalCode>
                    $shipping_xml
                    <PictureDetails>
                        $images_xml
                    </PictureDetails>
                    <ReturnPolicy>
                        <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
                        <RefundOption>MoneyBack</RefundOption>
                        <ReturnsWithinOption>Days_30</ReturnsWithinOption>
                        <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
                    </ReturnPolicy>
                    <DescriptionReviseMode>Replace</DescriptionReviseMode>
                </Item>
            ";

            //Return the final XML string
            return $xml;
        }

        /**
         * Update existing listing from product
         * 
         * @param WC_Product $product product for which to update listing
         * 
         * @return array|null API response as assoc. array, or NULL on error
         */
        function update_listing ( WC_Product $product ) {
            //Get ID of listing from product
            $listing_id = $product->get_meta('ebay_id');

            //Define response as null by default
            $response = null;

            //Check if listing ID is defined
            if( $listing_id ) { 
                //Convert WC_Product to XML string
                $data = $this->product_to_listing_request_data( $product );
                //Make API request to update item
                $response = $this->api_request("ReviseItem", $data);

                //Log 'updated listing' in the activity log
                $ebay_link = ebay_product_link( $product );
                $product_link = product_link( $product );
                log_activity( 'eBay', "Updated $ebay_link for product $product_link" );
            }

            //If listing ID was defined, return the API response, otherwise return NULL
            return $response;
        }

        /**
         * Update listing if it exists, and create one if not (from WC_Product)
         * 
         * @param WC_Product $product
         * 
         * @return array|null API response as assoc. array, or NULL on error
         */
        function update_or_create_Listing ( WC_Product $product ) {
            //get listing ID from product
            $listing_id = $product->get_meta('ebay_id');

            //Set response to NULL by default
            $response = null;

            //Check if listing ID is present
            if( $listing_id ) {
                /*
                    If listing ID is set on product, that means the listing
                    already exists, so update it instead of creating a new one
                */
                $response = $this->update_listing( $product );
            }else{
                /*
                    If listing ID is not set on product, that means the listing
                    doesn't exist, so create one.
                */
                $response = $this->create_listing( $product );
            }

            //Return response
            return $response;
        }

        /**
         * Get currently linked user data
         * 
         * @return array assoc. array of user data
         */
        function get_user() {
            //Make REST API request and retrieve current user
            $response = $this->rest_api_request('commerce/identity/v1/user/', 'GET', null, 'apiz');

            //Return response
            return $response;
        }

        /**
         * Delete listing of WC_Product(TODO)
         */
        function delete_listing ( WC_Product $product ) {
            
        }

        /**
         * Get listing of WC_Product
         * 
         * @param WC_Product $product product which's listing to retrieve
         * 
         * @return SimpleXMLElement|null API response as XML object, or NULL on error
         */
        function get_listing ( WC_Product $product ) {
            //Get eBay listing ID of product
            $listing_id = $product->get_meta('ebay_id');

            //Check if listing ID is defined
            if( ! $listing_id ) {
                //If not, log error and return null
                error_log('eBay Listing ID not found in get_listing()');
                return null;
            }

            //Otherwise, make API request to retrieve listing
            //Data to send
            $data = "<ItemID>$listing_id</ItemID>";
            //Make request
            $response = $this->api_request("GetItem", $data);

            //Return response
            return $response;
        }

        /**
         * End listing of WC_Product
         * 
         * @param WC_Product $product product to end listing for
         * 
         * @return SimpleXMLElement|null XML object of API response, or NULL on error
         */
        function end_listing ( WC_Product $product ) {
            //Get listing ID from product
            $listing_id = $product->get_meta('ebay_id');

            //Set response to NULL by default
            $response = null;

            //Check if listing ID is defined
            if ( $listing_id ) {
                //If defined, make API request to end listing

                //Data to send
                $data = "
                    <ItemID>$listing_id</ItemID>
                    <EndingReason>NotAvailable</EndingReason>
                ";

                //Make request
                $response = $this->api_request('EndItem', $data);

                //Log 'ended listing' in activity log
                log_activity("eBay", 'Endend ' . ebay_product_link( $product ) . ' for product ' . product_link( $product ));
            }

            //Return response (object on success, null on error)
            return $response;
        }

        /**
         * Delete listing if possible, end if not. Currently this function only ends listing, and
         * doesn't delete it (TODO)
         * 
         * @param WC_Product $product product which's listing to end/delete
         * 
         * @return SimpleXMLElement|null XML object or NULL on error
         */
        function end_or_delete_listing ( WC_Product $product ) {
            //Get listing ID from product
            $listing_id = $product->get_meta('ebay_id');

            //Set response to null by default
            $response = null;

            //Check if listing ID is defined
            if( $listing_id ){
                //If yes, make request to end listing
                $response = $this->end_listing( $product );
            } else{
                //Otherwise log error
                error_log('Listing ID not found when ending listing!');
            }

            //Return response (object on success, null on error)
            return $response;
        }

        /**
         * Get shipping profiles of currently linked user
         * 
         * @return array|null assoc. array of shipping profiles, or NULL on error
         */
        function get_shipping_profiles() {
            //Make REST API request to retrieve shipping profiles
            $res = $this->rest_api_request('sell/account/v1/fulfillment_policy?marketplace_id=EBAY_US');

            //If response successful and there are any shipping profiles, return them
            if( $res['fulfillmentPolicies'] ) {
                return $res['fulfillmentPolicies'];
            }

            //Otherwise return null
            return null;;
        }
    }