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
         * @param WC_Product product associated with listing
         * @return string response of API request
         */
        function delete_listing(WC_Product $product);

        /**
         * Retrieves listing
         * 
         * @param WC_Product product associated with listing
         * @return string response of API request
         */
        function get_listing(WC_Product $product);

        function end_listing(WC_Product $product);

        function end_or_delete_listing(WC_Product $product);

    }