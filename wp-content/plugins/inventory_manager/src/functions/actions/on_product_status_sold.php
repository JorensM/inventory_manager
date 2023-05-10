<?php

require_once(__DIR__ . "/../../classes/Listing_Manager_Group.php");
require_once __DIR__ . '/../product_link.php';

function on_product_status_sold($new_status, $old_status, $post){
    $product = null;

    $page_id = get_current_screen()->id;

    $is_product = !empty($post->ID) && in_array( $post->post_type, array( 'product') );
    if($is_product){
        $product = wc_get_product($post->ID);
        if ( $new_status != $old_status && $new_status != 'auto-draft' && $old_status != 'auto-draft' && $old_status != 'new') {
            log_activity("General", 'Status of product ' . product_link( $product ) . " changed from <b>$old_status</b> to <b>$new_status</b>");
        }

        if( ( $old_status == 'new' || $old_status == 'auto-draft' ) && ( $new_status != 'new' && $new_status != 'auto-draft' ) ) {
            $current_user = wp_get_current_user();
            $product_link = product_link($product);
            //$product_url = admin_url() . "post.php?post={$product_id}&action=edit";
            log_activity( $current_user->nickname, "Created product $product_link with status <b>$new_status</b>" );
        }
    }
    if($new_status == "sold"){
        //error_log("Status changed to sold, ending listing");
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

        if( $page_id == 'product' ) {
            if(isset($responses["reverb"]["message"])){
                Admin_Notice::displayInfo($responses["reverb"]["message"]);
            }
    
            //Admin_Notice::displayInfo('abc<pre>' . print_r($responses, true) . '</pre>');
        }   
        
        

        //error_log("responses: ");
        //error_log(print_r($responses, true));
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