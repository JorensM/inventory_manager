<?php
    require_once(plugin_dir_path(__FILE__) . "../const.php");
    require_once(plugin_dir_path(__FILE__) . "../classes/ReverbListingManager.php");

    /**
     * Checks all platforms for if any of the listings have been deleted, and delete them if true
     * Should be run as a cron job
     * 
     * @return void
     */
    function checkListingsAndUpdateProducts(){
        $REVERB_TOKEN = get_option("reverb_token");

        $reverbManager = new ReverbListingManager(["token" => $REVERB_TOKEN], "sandbox");

        $all_products = wc_get_products([]);

        foreach($all_products as $product){
            $reverbManager->checkListingAndDeleteProduct($product);
            $reverbManager->checkListingAndMarkSold($product, false);
            $product->save();
        }
    }