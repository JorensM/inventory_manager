<?php

require_once(__DIR__ . "/../../classes/Listing_Manager_Group.php");

function before_product_delete($post_id, $post){
    //Check if post type is product
    if(in_array( $post->post_type, array( 'product'))){
        //Get listing managers
        global $listing_managers;

        //Get the product
        $product = wc_get_product($post_id);

        //Delete product on other platforms
        $listing_managers->end_or_delete_listing($product);
        //$reverbManager->endOrDeleteListing($product);
    }
}
add_action("before_delete_post", "before_product_delete", 10, 2);