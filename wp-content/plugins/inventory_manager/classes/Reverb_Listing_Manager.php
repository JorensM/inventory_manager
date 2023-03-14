<?php

    require_once("Listing_Manager_Interface.php");
    require_once(__DIR__."/../functions/markProductSold.php");

    class Reverb_Listing_Manager implements Listing_Manager_Interface{

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
                "description" => "notes_field",
            ];

            $this->condition_mappings = [
                "used" => "ae4d9114-1bd7-4ec5-a4ba-6653af5ac84d",
                "non-functioning" => "fbf35668-96a0-4baa-bcde-ab18d6b1b329"
            ];
        }

        function create_listing(WC_Product $product){
            
            $data = $this->product_to_listing_request_data($product);
            $response = $this->listing_request("POST", $data);

            error_log("res: ");
            error_log(print_r($response, true));
            
            if($response){
                $product->add_meta_data("reverb_id", $response["listing"]["id"]);
                $product->save();
            }
            
            return $response;
        }

        function update_listing(WC_Product $product){
            $data = $this->product_to_listing_request_data($product);

            $listing_id = $product->get_meta("reverb_id");

            $response = $this->listing_request("PUT", $data, $listing_id);

            return $response;
        }

        function update_or_create_listing(WC_Product $product){
            //Get listing id from respective product
            $listing_id = $product->get_meta("reverb_id");

            $response = null;

            //If id is returned, that means listing already exists, so we update it
            if($listing_id){
                error_log("Updating listing");
                $response = $this->update_listing($product);
            }
            //If no id is returned, that means listing hasn't been created, so we create it
            else{
                $response = $this->create_listing($product);
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
        function listing_request(string $request_type, $data = null, $id = null, $my = false){

            if($id){
                $id = "/" . $id;
            }else{
                $id = "";
            }

            $my = $my ? "my/" : "";

            $url = $this->api_url . $my . "listings" . $id;

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
                error_log("Data: ");
                error_log(print_r($data, true));
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
         * Check if Reverb has ended, and mark it as sold on WooCommerce if true
         * 
         * @param WC_Product $product target product
         * @param bool $save whether to save product. Default true
         */
        function check_listing_and_mark_sold(WC_Product $product, bool $save = true){
            $listing_id = $this->get_listing_ID($product);

            if(!$listing_id){
                return null;
            }
            $listing = $this->get_listing($product);

            $state = $listing["state"]["slug"];
            if($state == "ended"){
                markProductSold($product, $save);
            }
        }

        /**
         * Get Reverb listing id from product. Returns null if id not set
         */
        function get_listing_ID(WC_Product $product){
            $listing_id = $product->get_meta("reverb_id");

            if($listing_id){
                return $listing_id;
            }
            return null;
        }

        /**
         * Convert product data to listing request data
         * 
         * @param WC_Product $product product to convert
         * 
         * @return array data Assoc. array of data that is valid to use for listingRequest()
         */
        function product_to_listing_request_data(WC_Product $product){
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

            //Whether to use test photos
            $test_photos = true;

            //Check if test photos are to be used
            if($test_photos){
                //Add test photos
                $data["photos"] = ["https://i.imgur.com/WJlG8F6.png"];
            }else{
                //Add actual photos from product
                $data["photos"] = $image_urls;
            }
            
            //Set categories data field
            $data["categories"] = $category_uuids;
            //Set title data field
            $data["title"] = $product->get_title();

            //Set price data fields
            $data["price"]["amount"] = $product->get_regular_price();
            $data["price"]["currency"] = "USD";

            //Set publish data field
            //$is_draft = $product->get_meta("reverb_draft");
            $data["publish"] = $product->get_meta("reverb_draft") == "yes" ? "false" : "true";

            $data["shipping"]["local"] = true;

            return $data;
        }

        

        function delete_listing(WC_Product $product){
            $listing_id = $product->get_meta("reverb_id");

            if(!$listing_id){
                return false;
            }
            $response = $this->listing_request("DELETE", null, $listing_id);
            return $response;
        }

        function end_listing(WC_Product $product){
            $listing_id = $product->get_meta("reverb_id");

            if(!$listing_id){
                return false;
            }
            error_log("Before");
            $response = $this->listing_request("PUT", ["reason" => "not_sold"], $listing_id . "/state/end", true);
            error_log("after");
            return $response;
        }

        function end_or_delete_listing(WC_Product $product){
            $listing_id = $product->get_meta("reverb_id");

            $listing = $this->get_listing($product);


            if(isset($listing["draft"])){
                $draft = $listing["draft"];
                if($draft == 1){
                    $this->delete_listing($product);
                }else{
                    $res = $this->end_listing($product);
                    error_log(print_r($res, true));
                    
                }
            }
        }

        function get_listing(WC_Product $product){

            $listing_id = $product->get_meta("reverb_id");

            if(!$listing_id){
                return false;
            }

            return $this->listing_request("GET", null, $listing_id);
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

    }