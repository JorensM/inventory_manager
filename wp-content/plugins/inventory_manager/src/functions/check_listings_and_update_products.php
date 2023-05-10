<?php

    //--Requires--//
    require_once plugin_dir_path( __FILE__ ) . '../const.php';
    require_once plugin_dir_path( __FILE__ ) . '../classes/Listing_Manager_Group.php';

    /**
     * Checks all platforms for if any of the listings have been deleted, and delete them if true
     * Should be run as a cron job
     * 
     * @return void
     */
    function check_listings_and_update_products(){
        global $listing_managers;

        //$reverbManager = new Reverb_Listing_Manager(["token" => $REVERB_TOKEN], "sandbox");

        $all_products = wc_get_products([]);

        foreach($all_products as $product){
            //$reverbManager->checkListingAndDeleteProduct($product);
            //$reverbManager->checkListingAndMarkSold($product, false);
            $listing_managers->check_listing_and_mark_sold($product, false);
            $product->save();
        }
    }