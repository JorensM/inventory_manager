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

        //error_log("API RESPONSES: ");
        //error_log( print_r( $responses["ebay"], true ) );

        $message_str = "";

        if ( isset( $responses['reverb']['message']) ) {
            $message_str .= '<b>Reverb: </b><br>' . $responses['reverb']['message'];
            //Admin_Notice::displayInfo("<b>Reverb: </b>" . $responses["reverb"]["message"]);
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

        //uncomment below line to see full ebay response on publish
        //$message_str .= '<pre>' . print_r($responses['ebay'], true) . '</pre>';

        if ( $message_str != '' ) {
            Admin_Notice::displayInfo($message_str);
        }else {
            Admin_Notice::displayInfo('empty');
        }
    }
}
add_action("woocommerce_process_product_meta", "publish_product_to_platforms", 1000, 2);