<?php

require_once __DIR__ . '/../../classes/Listing_Manager_Group.php';

/**
 * Called before product deletion
 */
function before_delete_product( WC_Product $product ) {
    //Get listing managers
    global $listing_managers;

    //Delete product on all platforms
    $listing_managers->end_or_delete_listing($product);
}

/**
 * Called before post deletion
 */
function before_delete_post( $post_id, $post ) {
    if(in_array( $post->post_type, array( 'product'))){
        //If post type is 'product'

        //Get the product
        $product = wc_get_product( $post_id );
        
        //Call before_delete_product() function
        before_delete_product( $product );
    }
}
add_action("before_delete_post", "before_delete_post", 10, 2);