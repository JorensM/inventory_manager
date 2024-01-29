<?php

    require_once 'Listing_Manager_Interface.php';
    require_once __DIR__ . '/../functions/mark_product_sold.php';
    require_once __DIR__ . '/../functions/product_link.php';

    class Reverb_Listing_Manager implements Listing_Manager_Interface{

        private string  $mode; //Mode (live/sandbox)
        private string  $token; //Access token
        private string  $api_url; //API base url
        private array   $field_mappings; //Product field to API request field mappings
        private array   $condition_mappings; //Product "condition" field to Reverb's condition uuid mappings

        function __construct( array $auth_data, string $mode ) {
            //Assign params to member variables
            $this->token = $auth_data['token'];
            $this->mode = $mode;
            

            //Determine which API url to use based on mode
            if ( $mode === 'live' ) {
                $this->api_url = 'https://api.reverb.com/api/';
            }else{
                $this->api_url = 'https://sandbox.reverb.com/api/';
            }

            //Define field mappings
            $this->field_mappings = array(
                'make' => 'brand_info',
                'model' => 'model_info',
                'year' => 'year_field',
                'description' => 'notes_field',
            );

            //Define condition mappings
            $this->condition_mappings = array(
                'used' => 'ae4d9114-1bd7-4ec5-a4ba-6653af5ac84d',
                'non-functioning' => 'fbf35668-96a0-4baa-bcde-ab18d6b1b329'
            );
        }

        /**
         * The following 2 methods are low level functions used to make API calls.
         * Use listing_request() to make requests to the 'listings' endpoint, and use
         * api_request() to make requests to any other endpoint
         */

        /**
         * Make generic API request
         * 
         * @param string    $endpoint       endpoint to make request to. For example 'shipping/providers'
         * @param string    $request_type   request method, such as GET or POST. Default 'GET'
         * @param any[]     $data           assoc. array of data to send. Will get converted to JSON
         * @param bool      $my             whether to prefix the endpoint with 'my/'. Default false
         */
        function api_request( $endpoint, $request_method = "GET", $data = null, $my = false ) {

            //Check whether use the 'my/' prefix, and store it in a variable if true, otherwise store an empty string
            $my = $my ? 'my/' : '';

            //Create endpoint URL using $this->api_url, $my, and $endpoint
            $url = $this->api_url . $my . $endpoint;

            //Init curl with the previously created URL
            $curl = curl_init( $url );
            
            //Set request headers
            $headers = [
                'Content-Type: application/hal+json',
                'Accept: application/hal+json',
                'Accept-Version: 3.0' ,
                "Authorization: Bearer {$this->token}"
            ];
            // print_r($headers);
            //Set curl options
            curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $request_method );
            curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

            //Check if request method allows sending data
            if ( $request_method == 'POST' || $request_method == 'PUT' ) {
                //If yes, convert $data to JSON
                $data_json = json_encode( $data );
                //And add it to the request
                curl_setopt( $curl, CURLOPT_POSTFIELDS, $data_json );
            }

            //Response null by default
            $res = null;

            //Make request and return null on error
            try {
                $res = curl_exec( $curl );
            } catch( Exception $e ) {
                return null;
            }
            
            //Convert response json string to assoc. array
            $res_arr = json_decode( $res, true );

            //Return converted response
            return $res_arr;
        }

        /**
         * Make request to the listing endpoint. Basically a wrapper for api_request, but with a custom endpoint
         * 
         * @param string        $request_method method of request (PUT, POST, GET)
         * @param array|null    $data           data to pass, if any
         * @param any           $id             id of listing (use when getting/updating/deleting listing)
         * @param bool          $my             whether to prefix the endpoint with 'my/'
         * 
         * @return string request response
         */
        function listing_request( string $request_method, $data = null, $id = null, $my = false ) {

            //Check if $id is specified, and convert $id to a string with the ID if true
            if ( $id ){
                $id = '/' . $id;
            } else {
                //Otherwise make $id an empty string
                $id = '';
            }
            
            //Store endpoint string with the id(if specified) to a variable
            $endpoint = 'listings' . $id;

            //Make API request using the api_request method and return response
            return $this->api_request( $endpoint, $request_method, $data, $my );
        }

        /**
         * The following methods are CRUD methods for listings
         */

        /**
         * Retrieve listing associated with provided WC_Product
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return any[]|null API response with listing data, or null on error
         */
        function get_listing( WC_Product $product ) {

            //Get listing ID from WC_Product's meta data
            $listing_id = $product->get_meta( 'reverb_id' );

            //If listing ID is not found, return null
            if( ! $listing_id ){
                return null;
            }

            //Otherwise, make a GET listing request and return response
            return $this->listing_request( 'GET', null, $listing_id );
        }

        /**
         * Create a new listing based on the product provided
         * 
         * @param WC_Product $product product to create a listing for
         * 
         * @return any[]|null API response, or null on error
         */
        function create_listing(WC_Product $product){
            
            //Convert WC_Product to a data array accepted by listing_request() and store it in a variable;
            $data = $this->product_to_listing_request_data( $product );
            //Call listing endpoint with POST and store response in a variable
            $response = $this->listing_request( 'POST', $data );
            
            if ( $response ) {
                //If there is a response

                //Add the newly created listing's ID to the WC_Product's meta data
                $product->add_meta_data( 'reverb_id', $response['listing']['id'] );
                //Add the newly created listing's public link to the WC_Product's meta data
                $product->add_meta_data( 'reverb_link', $response['listing']['_links']['web']['href'] );
                //Save product
                $product->save();

                //Get formatted link to listing
                $reverb_link = reverb_product_link( $product, 'listing' );
                //Get formatted link to WC product
                $product_link = product_link( $product );

                //Log 'created listing' in the activity log
                log_activity( 'Reverb', 'Created $reverb_link for product $product_link' );
            }
            
            //Return response of request
            return $response;
        }

        /**
         * Updates existing listing based on provided product
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return any[]|null API response, or null on error
         */
        function update_listing( WC_Product $product ) {

            //Convert WC_Product to array readable by listing_request() and store it in a variable
            $data = $this->product_to_listing_request_data( $product );

            //Get Reverb listing ID associated with the product
            $listing_id = $product->get_meta( 'reverb_id' );

            //Make PUT listing request and store response in variable
            $response = $this->listing_request( 'PUT' , $data, $listing_id);

            if ( $response ) {
                //If there is a response

                //Update WC_Product's meta data for the Reverb link
                $link = $product->get_meta( 'reverb_link' );
                $new_link = $response['listing']['_links']['web'];
                if( $link ) {
                    $product->update_meta_data( 'reverb_link', $response['listing']['_links']['web']['href'] );
                }else{
                    $product->add_meta_data( 'reverb_link', $new_link );
                }
                //Save product
                $product->save();

                //Get formatted listing link
                $reverb_link = reverb_product_link( $product );
                //Get formatted WC product link
                $product_link = product_link( $product );
                //Log 'updated listing' in the activity log
                log_activity( 'Reverb', "Updated $reverb_link for product $product_link" );
            }

            //Return API response
            return $response;
        }

        /**
         * Update existing listing based on WC_Product, or create one if not created yet
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return any[]|null API response or null on error
         */
        function update_or_create_listing( WC_Product $product ) {
            //Get listing id from respective product
            $listing_id = $product->get_meta( 'reverb_id' );

            $response = null;

            
            if ( $listing_id ) {
                //If id is returned, that means listing already exists, so we update it
                $response = $this->update_listing($product);
            }
            else {
                //If no id is returned, that means listing hasn't been created, so we create it
                $response = $this->create_listing($product);
            }

            //Return API response
            return $response;
        }

        /**
         * Deletes a listing associated with the provided product
         * 
         * @param WC_Product $product product associated with the listing
         * 
         * @return any 
         */
        function delete_listing( WC_Product $product ) {

            //Get listing ID from WC_Product's meta data
            $listing_id = $product->get_meta( 'reverb_id' );

            //If ID not found, return null
            if ( ! $listing_id ) {
                return null;
            }

            //Otherwise, make DELETE listing request and return response
            $response = $this->listing_request( 'DELETE', null, $listing_id );
            return $response;
        }

        /**
         * Ends listing associated with the provided product
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return any[]}null API response, or null on error
         */
        function end_listing( WC_Product $product ){

            //Get listing ID associated with WC_Product from product's meta data
            $listing_id = $product->get_meta( 'reverb_id' );

            //If ID not found, return null
            if ( !$listing_id ) {
                return false;
            }

            //Otherwise, make a PUT listing request and store response in a variable
            $data = array(
                'reason' => 'not_sold'
            );
            $response = $this->listing_request( 'PUT', $data, $listing_id . '/state/end', true );

            //Get formatted listing link
            $reverb_link = reverb_product_link( $product );
            //Get formatted WC product link
            $product_link = product_link( $product );
            //Log 'ended listing' into activity log
            log_activity('Reverb', "Ended $reverb_link for product $product_link");

            //Return API response
            return $response;
        }

        /**
         * End listing associcated with product if it is published, or delete it if it's a draft
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return void
         */
        function end_or_delete_listing( WC_Product $product ) {

            //Get listing associated with product and store it in a variable
            $listing = $this->get_listing($product);


            //If listing is draft, delete it, otherwise end it
            if ( isset( $listing['draft'] ) ) {
                $draft = $listing['draft'];
                if ( $draft == 1 ) {
                    $this->delete_listing( $product );
                }else{
                    $res = $this->end_listing( $product );
                    //error_log(print_r($res, true));
                    
                }
            }
        }

        /**
         * Check if listing has been deleted on Reverb, and delete it on this app if true
         * 
         * @param WC_Product $product product to check
         * 
         * @return bool true if deleted, false if not
         */
        function check_listing_and_delete_product(WC_Product $product){
            $listing_id = $product->get_meta("reverb_id");

            if(!$listing_id){
                return false;
            }

            $listing = $this->get_listing($product);
            //error_log("aaa: ");

            if(!isset($listing["id"])){
                //error_log("listing not found, deleting");
                $product->delete();
            }else{
                //error_log("listing found, not deleting");
            }
            //error_log(print_r($listing, true));
        }

        /**
         * Check if Reverb listing has ended, and mark it as sold on WooCommerce if true
         * 
         * @param WC_Product $product target product
         * @param bool $save whether to save product. Default true
         * 
         * @return void|null null on error
         */
        function check_listing_and_mark_sold( WC_Product $product, bool $save = true ){

            //Get listing ID associated with product
            $listing_id = $this->get_listing_ID($product);

            //If ID not found, return null
            if ( ! $listing_id ) {
                return null;
            }
            //Otherwise, get listing associated with product
            $listing = $this->get_listing( $product );

            //Store listing state (ended/active) in a variable
            $state = $listing['state']['slug'];
            if ( $state == 'ended' ) {
                //If listing has been ended, mark product as sold on WC
                mark_product_sold( $product, $save );
            }
        }

        /**
         * Get Reverb listing id from product. Returns null if id not set
         * 
         * @param WC_Product $product product associated with listing
         * 
         * @return string|null listing ID, or null on error
         */
        function get_listing_ID( WC_Product $product ) {

            //Get listing ID from product's meta data
            $listing_id = $product->get_meta("reverb_id");

            //If ID is set, return it
            if ( $listing_id ) {
                return $listing_id;
            }
            //Otherwise return null
            return null;
        }

        /**
         * Convert product data to listing request data that the Reverb API understands
         * 
         * @param WC_Product $product product to convert
         * 
         * @return array data Assoc. array of data that is valid to use for listing_request()
         */
        function product_to_listing_request_data( WC_Product $product ){
            $data = array();

            //Get image urls
            $image_ids = $product->get_gallery_image_ids();
            $image_urls = array();
            foreach($image_ids as $image_id){
                array_push( $image_urls, wp_get_attachment_url( $image_id ) );
            }

            //Get Reverb category UUIDs respective to which categories the product has
            $category_ids = $product->get_category_ids();
            $category_uuids = array();
            foreach ( $category_ids as $category_id ) {

                $uuid = get_term_meta( $category_id, 'reverb_category_id', true );
                if( $uuid ) {
                    array_push( $category_uuids, array( 'uuid' => $uuid ) );
                }
                
            }

            //log_activity("reverb categories ", '<div>' . json_encode( $category_uuids ) . '</div>');

            //Set data fields from meta data fields
            foreach( $this->field_mappings as $reverb_field => $woo_field ){
                $data[ $reverb_field ] = $product->get_meta( $woo_field );
            }

            //Set condition data field
            $data['condition']['uuid'] = $this->condition_mappings[ $product->get_meta( 'condition_field' ) ];
            //Set photos data field

            //Whether to use test photos
            $test_photos = false;

            //Check if test photos are to be used
            if ( $test_photos ) {
                //Add test photos
                $data['photos'] = ['https://i.imgur.com/WJlG8F6.png'];
            }else{
                //Add actual photos from product
                $data['photos'] = $image_urls;
            }
            
            //Set categories data field
            $data['categories'] = $category_uuids;
            //Set title data field
            $data['title'] = $product->get_title();

            //Set price data fields
            $data['price']['amount'] = $product->get_regular_price();
            $data['price']['currency'] = 'USD';

            //Set publish data field. This field determines whether the listing should be saved as draft or immediatelly published
            $data['publish'] = $product->get_meta( 'reverb_draft' ) == 'yes' ? 'false' : 'true';

            //Get shipping profile ID if there is one, and add it to the data to send.
            //Otherwise set shipping to 'local pickup'
            $shipping_profile_id = $product->get_meta( 'reverb_shipping_profile' );
            if( $shipping_profile_id ) {
                $data['shipping_profile_id'] = $shipping_profile_id;
            }else{
                $data['shipping']['local'] = true;
            }
            
            //Return data
            return $data;
        }

        /**
         * Get shipping profiles for the linked Reverb account
         * 
         * @return any[]|null API response, or null on error
         */
        function get_shipping_profiles() {
            //Make API request to the 'shop' endpoint and store response in variable
            $response = $this->api_request("shop");

            //Extract shipping profiles from response and return them
            return $response['shipping_profiles'];
        }

        /**
         * Retrieve user details for the linked Reverb account
         * 
         * @return any[]|null API response, or null on error
         */
        function get_user() {
            //Make GET request to the 'account' endpoint and return response
            $response = $this->api_request('account', 'GET', null, true);
            return $response;
        }

        /**
         * Get mode (live/sandbox) for current instance
         * 
         * @return string current mode
         */
        function get_mode() {
            return $this->mode;
        }
    }