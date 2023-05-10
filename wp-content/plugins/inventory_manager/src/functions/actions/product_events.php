<?php

/**
 * This file holds all code related to product events, such as creating/updating products, etc.
 */

//--Requires--//

//Classes
require_once __DIR__ . '/../../classes/Admin_Notice.php';
require_once __DIR__ . '/../../classes/Listing_Manager_Group.php';

//Functions
require_once __DIR__ . '/../generate_barcode.php';
require_once __DIR__ . '/../log_activity.php';
require_once __DIR__ . '/../product_link.php';



/**
 * Called when product gets created/updated
 */
function on_product_save( $product_id ) {
    $product = wc_get_product($product_id);

    if($product->get_sku()){
        //generate_barcode($product->get_sku(), $product_id, $product->get_title());
        generate_barcode(
            $product->get_sku(),
            $product_id,
            $product->get_title(),
            $product->get_sale_price(),
            $product->get_regular_price(),
            $product->get_meta('product_location'),
            $product->get_meta('product_serial_number')
        );
    }
}

add_action( 'woocommerce_update_product', 'on_product_save', 10, 1 );

/**
 * Published product to all connected platforms. Called when product status changes to 'publish'
 */
function publish_product_to_platforms($post_id, $post){
    global $listing_managers;

    if(get_post_status($post_id) == "publish" && !empty($post->ID) && in_array( $post->post_type, array( 'product') )) {
        $product = wc_get_product($post->ID);

        if($product->get_meta("sold") != true){
            $responses = $listing_managers->update_or_create_listing($product);
        }

        $message_str = "";

        if ( isset( $responses['reverb']['message']) ) {
            $message_str .= '<b>Reverb: </b><br>' . $responses['reverb']['message'];
        }

        //uncomment below line to see full reverb response on publish
        //$message_str .= '<pre>' . print_r($responses['reverb'], true) . '</pre>';

        if( isset($responses['ebay']->Errors ) && count( $responses['ebay']->Errors ) > 0 ) {
            $message_str .= "<br><b>eBay:</b><br>";
            foreach ( $responses['ebay']->Errors as $message ) {
                $message_str .= htmlspecialchars( $message->LongMessage ) . '<br>';
                if ( $message->ErrorCode == 21917053 ) {
                    $is_ebay_token_expired = true;
                }
            }
        }

        if( $responses['ebay']->Ack == 'Success' ){
            $message_str .= '<br><b>eBay:</b><br>Listing created';
        }
        
        if ( $message_str != '' ) {
            Admin_Notice::displayInfo($message_str);
        }else {
            Admin_Notice::displayInfo('empty');
        }
    }
}
add_action("woocommerce_process_product_meta", "publish_product_to_platforms", 1000, 2);