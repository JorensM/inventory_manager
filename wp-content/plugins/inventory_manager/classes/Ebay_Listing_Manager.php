<?php

    require_once "Listing_Manager_Interface.php";

    class Ebay_Listing_Manager implements Listing_Manager_Interface {

        private string $mode;
        private string $api_url;

        function __construct(string $mode = 'sandbox') {
            if($mode == 'live'){
                $api_url
            }
        }

        function create_listing ( WC_Product $product ) {
            
        }

        function update_listing ( WC_Product $product ) {
            
        }

        function update_or_create_Listing ( WC_Product $product ) {
            
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