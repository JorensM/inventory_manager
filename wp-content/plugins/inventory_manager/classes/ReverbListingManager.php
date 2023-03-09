<?php

    require_once("IListingManager.php");

    class ReverbListingManager implements IListingManager{

        private string $mode; //Mode (live/sandbox)
        private string $token; //Access token
        private string $api_url; //API base url
        private array $field_mappings; //Product field to API request field mappings
        private array $condition_mappings; //Product "condition" field to Reverb's condition uuid mappings

        function __construct(array $auth_data, string $mode){
            //Assign params to member variables
            $this->token = $auth_data["token"];
            $this->$mode = $mode;
            

            //Determine which API url to use based on mode
            if($mode === "live"){
                $this->api_url = "https://api.reverb.com/api/";
            }else{
                $this->api_url = "https://sandbox.reverb.com/api/";
            }

            //Define field mappings
            $this->field_mappings = [
                "make" => "brand_info",
                "model" => "model_info",
                "year" => "year_field",
                "description" => "notes_field"
            ];

            $this->condition_mappings = [
                "used" => "ae4d9114-1bd7-4ec5-a4ba-6653af5ac84d",
                "non-functioning" => "fbf35668-96a0-4baa-bcde-ab18d6b1b329"
            ];
        }

        function createListing(WC_Product $product){
            
            $data = $this->productToListingRequestData($product);
            $response = $this->listingRequest("POST", $data);

            error_log("res: ");
            error_log(print_r($response, true));
            
            if($response){
                $product->add_meta_data("reverb_id", $response["listing"]["id"]);
                $product->save();
            }
            
            return $response;
        }

        function updateListing(WC_Product $product){
            $data = $this->productToListingRequestData($product);

            $listing_id = $product->get_meta("reverb_id");

            $response = $this->listingRequest("PUT", $data, $listing_id);

            return $response;
        }

        function updateOrCreateListing(WC_Product $product){
            //Get listing id from respective product
            $listing_id = $product->get_meta("reverb_id");

            $response = null;

            //If id is returned, that means listing already exists, so we update it
            if($listing_id){
                $response = $this->updateListing($product);
            }
            //If no id is returned, that means listing hasn't been created, so we create it
            else{
                $response = $this->createListing($product);
            }

            return $response;
        }

        /**
         * Make request to the listing endpoint
         * 
         * @param string $request_type type of request (PUT, POST, GET)
         * @param array|null $data data to pass, if any
         * @param any $id id of listing (use when getting/updating/deleting listing)
         * 
         * @return string request response
         */
        function listingRequest(string $request_type, $data = null, $id = null){

            if($id){
                $id = "/" . $id;
            }else{
                $id = "";
            }

            $url = $this->api_url . "listings" . $id;

            error_log($url);

            $curl = curl_init($url);
            
            //Set request headers
            $headers = [
                "Content-Type: application/hal+json",
                "Accept: application/hal+json",
                "Accept-Version: 3.0" ,
                "Authorization: Bearer $this->token"
            ];
            error_log("headers: ");
            error_log(print_r($headers, true));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request_type);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            

            if($request_type == "POST" || $request_type == "PUT"){
                $data_json = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
            }

            $res = null;

            try{
                $res = curl_exec($curl);
            }catch(Exception $e){
                error_log("Error making listing request: ");
                error_log($e->getMessage());

                return null;
            }

            error_log("request res: ");
            error_log(print_r($res, true));

            //error_log($res);
            
            //Convert response json string to assoc. array
            $res_arr = json_decode($res, true);

            return $res_arr;

        }

        /**
         * Convert product data to listing request data
         * 
         * @param WC_Product $product product to convert
         * 
         * @return array data Assoc. array of data that is valid to use for listingRequest()
         */
        function productToListingRequestData(WC_Product $product){
            $data = [];

            //Get image urls
            $image_ids = $product->get_gallery_image_ids();
            $image_urls = [];
            foreach($image_ids as $image_id){
                array_push($image_urls, wp_get_attachment_url($image_id));
            }

            //Get Reverb category UUIDs respective to which categories the product has
            $category_ids = $product->get_category_ids();
            $category_uuids = [];
            foreach($category_ids as $category_id){
                $category = get_term_by("id", $category_id, "product_cat");//get_category($category_id);
                error_log(print_r($category, true));
                $uuid = $category->slug;

                array_push($category_uuids, ["uuid" => $uuid]);
            }

            //Set data fields from meta data fields
            foreach($this->field_mappings as $reverb_field => $woo_field){
                $data[$reverb_field] = $product->get_meta($woo_field);
            }

            //Set condition data field
            $data["condition"]["uuid"] = $this->condition_mappings[$product->get_meta("condition_field")];
            //Set photos data field
            $data["photos"] = $image_urls;
            //Set categories data field
            $data["categories"] = $category_uuids;
            //Set title data field
            $data["title"] = $product->get_title();

            $data["price"]["amount"] = $product->get_regular_price();
            $data["price"]["currency"] = "USD";

            return $data;
        }

        

        function deleteListing(WC_Product $product){
            
        }

        function getListing(WC_Product $product){
            
        }

    }