<?php

require_once(__DIR__ . "/../../classes/Admin_Notice.php");
require_once(__DIR__ . "/../../classes/Listing_Manager_Group.php");

function publish_product_to_platforms($post_id, $post){
    global $listing_managers;
    //global $reverbManager;

    //$reverbManager = new ReverbListingManager(["token" => $REVERB_TOKEN], "sandbox");

    if(get_post_status($post_id) == "publish" && !empty($post->ID) && in_array( $post->post_type, array( 'product') )) {
        $product = wc_get_product($post->ID);

        $reverb_response = null;

        //error_log("Is updating");
        if($product->get_meta("sold") != true){
            $responses = $listing_managers->update_or_create_listing($product);
            //$reverb_response = $reverbManager->updateOrCreateListing($product);
        }
        

        //new DisplayNotice("Test", "warning");


        if(isset($responses["reverb"]["message"])){
            Admin_Notice::displayInfo("<b>Reverb:</b> " . $responses["reverb"]["message"]);
        }
    }
}
add_action("woocommerce_process_product_meta", "publish_product_to_platforms", 1000, 2);