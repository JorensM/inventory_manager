<?php

require_once(__DIR__ . "/../../classes/Listing_Manager_Group.php");

function on_product_status_sold($new_status, $old_status, $post){
    $product = null;

    $is_product = !empty($post->ID) && in_array( $post->post_type, array( 'product') );
    if($is_product){
        $product = wc_get_product($post->ID);
    }
    if($new_status == "sold"){
        error_log("Status changed to sold, ending listing");
        global $listing_managers;
        //global $reverbManager;
        $product = wc_get_product($post->ID);
        if($product->get_meta("sold")){
            $product->update_meta_data("sold", true);
        }else{
            $product->add_meta_data("sold", true);
        }
        

        //$res = $reverbManager->endListing($product);
        $responses = $listing_managers->end_listing($product);

        if(isset($responses["reverb"]["message"])){
            Admin_Notice::displayInfo($responses["reverb"]["message"]);
        }

        error_log("responses: ");
        error_log(print_r($responses, true));
    }
    if($new_status != "sold" && $is_product){
        if($product->get_meta("sold")){
            $product->update_meta_data("sold", false);
        }else{
            $product->add_meta_data("sold", false);
        }
    }


    if(!empty($post->ID) && in_array( $post->post_type, array( 'product') )){
        $product->save();
    }
    
}
add_action("transition_post_status", "on_product_status_sold", 10, 3);