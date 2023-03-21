<?php

    require_once "Listing_Manager_Interface.php";

    function make_utf8 ( string $string ) {
        // Test it and see if it is UTF-8 or not
        $utf8 = \mb_detect_encoding($string, ["UTF-8"], true);

        if ($utf8 !== false) {
            //error_log("Was not UTF-8");
            return $string;
        }

        // From now on, it is a safe assumption that $string is NOT UTF-8-encoded

        // The detection strictness (i.e. third parameter) is up to you
        // You may set it to false to return the closest matching encoding
        $encoding = \mb_detect_encoding($string, mb_detect_order(), true);

        if ($encoding === false) {
            throw new \RuntimeException("String encoding cannot be detected");
        }

        return \mb_convert_encoding($string, "UTF-8", $encoding);
    }

    class Ebay_Listing_Manager implements Listing_Manager_Interface {

        private string $mode;
        private string $api_url;
        private string $token;

        function __construct(array $auth_data, string $mode = 'sandbox') {
            $this->token = $auth_data["token"];
            //$this->token = "123";
            $this->mode = $mode;
            if ( $mode == 'live' ) {
                $this->api_url = "https://api.ebay.com/ws/api.dll";
            }
            else{
                $this->api_url = "https://api.sandbox.ebay.com/ws/api.dll";
            }
        }

        function api_request ( string $req_name, $xml_data_str = null ) {
            $curl = curl_init($this->api_url);

            $headers = [
                "Content-Type:text/xml",
                'X-EBAY-API-SITEID:0',
                'X-EBAY-API-COMPATIBILITY-LEVEL:967',
                "X-EBAY-API-CALL-NAME:$req_name",
                "X-EBAY-API-IAF-TOKEN:$this->token"
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $data_str = $xml_data_str;

            //error_log('sending XML: ');
            //error_log( $data_str );

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_str );

            $res = null;

            try{
                $res = curl_exec($curl);
            }catch(Exception $e){
                //error_log("Error making listing request: ");
                //error_log($e->getMessage());

                return null;
            }

            //error_log("response XML: ");
            //error_log($res);
            $res_xml = simplexml_load_string($res);
            
            return $res_xml;

        }



        function xml_str ( string $req_name, string $xml_str ) {
            return "<?xml version='1.0' encoding='utf-8'?><{$req_name}Request xmlns='urn:ebay:apis:eBLBaseComponents'>    
                $xml_str
                <ErrorLanguage>en_US</ErrorLanguage>
                <WarningLevel>High</WarningLevel>
            </{$req_name}Request>";
        }

        function create_listing ( WC_Product $product ) {
            $xml = $this->product_to_listing_request_data( $product );
            $data = $this->xml_str( 'AddItem', $xml );

            $response = $this->api_request( 'AddItem', $data );

            //error_log('Ebay response: ');
            //error_log( print_r( $response, true ) );

            return $response;

        }

        function check_listing_and_mark_sold ( WC_Product $product) {

        }

        function product_to_listing_request_data ( WC_Product $product ) {

            $description = $product->get_meta( 'notes_field' );

            $xml = "
                <Item>
                    <Title>{$product->get_title()}</Title>
                    <Description>{$description}</Description>
                    <StartPrice>1.0</StartPrice>
                    <Currency>USD</Currency>
                    <Country>US</Country>
                    <ListingDuration>Days_7</ListingDuration>
                    <PrimaryCategory>
                        <CategoryID>29223</CategoryID>
                    </PrimaryCategory>
                    <PostalCode>95125</PostalCode>
                    <ShipToLocations>None</ShipToLocations>
                    <PictureDetails>
                        <PictureURL>https://i.imgur.com/WJlG8F6.png</PictureURL>
                    </PictureDetails>
                    <ReturnPolicy>
                        <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
                        <RefundOption>MoneyBack</RefundOption>
                        <ReturnsWithinOption>Days_30</ReturnsWithinOption>
                        <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
                    </ReturnPolicy>
                </Item>
            ";// TODO: Replace PictureURL with actual image urls
            return $xml;
        }

        function update_listing ( WC_Product $product ) {
            
        }

        function update_or_create_Listing ( WC_Product $product ) {
            //error_log("EBAY TEST");
            $response = null;
            $response = $this->create_listing($product);
            return $response;
        }

        function delete_listing ( WC_Product $product ) {
            
        }

        function get_listing ( WC_Product $product ) {
            
        }

        function end_listing ( WC_Product $product ) {
            
        }

        function end_or_delete_listing ( WC_Product $product ) {
            
        }
    }