<?php

    
    interface Listing_Manager_Interface {

        /**
            * Interface for listing managers, such as ReverbListingManager, EbayListingManager etc.
        */

        /**
         * Initialize object
         * 
         * @param array $auth_data data for authorization, such as API keys, tokens, etc.
         * @param string $mode mode, such as live, sandbox, etc.
         * @param array $field_mappings mappings of product fields to request params
         */
        function __construct(array $auth_data, string $mode);

        /**
         * Retrieves listing
         * 
         * @param WC_Product $product product associated with listing
         * @return any response of API request or null on error
         */
        function get_listing(WC_Product $product);

        /**
         * Creates a new listing
         * 
         * @param WC_Product $product product to create listing from.
         * @return string response of API request
         */
        function create_listing(WC_Product $product);

        /**
         * Updates existing listing
         * 
         * @param WC_Product $product product associated with listing
         * @return string response of API request
         */
        function update_listing(WC_Product $product);

        /**
         * Update existing listing, or creates one if it hasn't been created yet
         * 
         * @param WC_Product $product product associated with listing, or product to create listing from
         * @return string response of API request
         */
        function update_or_create_Listing(WC_Product $product);

        /**
         * Deletes existing listing
         * 
         * @param WC_Product $product product associated with listing
         * @return string response of API request
         */
        function delete_listing(WC_Product $product);

        /**
         * Ends a listing
         * 
         * @param WC_Product $product product associated with the listing
         * 
         * @return any response of request or null on error
         */
        function end_listing(WC_Product $product);

        /**
         * Delete listing if possible, end it otherwise
         * 
         * @param WC_Product $product product associated with the listing
         * 
         */
        function end_or_delete_listing(WC_Product $product);

        /**
         * Retrieve the details for the linked account
         * 
         * @return any account details
         */
        function get_user();

        /**
         * Retrieve the shipping profiles of linked account
         * 
         * @return any shipping profiles
         */
        function get_shipping_profiles();

    }