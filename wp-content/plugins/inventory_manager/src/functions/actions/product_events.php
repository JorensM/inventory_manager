<?php

    /**
     * This file holds all code related to product events, such as creating/updating products, etc.
     */

    //--Requires--//

    //Classes
    require_once __DIR__ . '/../../classes/Admin_Notice.php';
    require_once __DIR__ . '/../../classes/Listing_Manager_Group.php';
    require_once __DIR__ . '/../../classes/class-google-sheets-products.php';

    //Functions
    require_once __DIR__ . '/../generate_barcode.php';
    require_once __DIR__ . '/../log_activity.php';
    require_once __DIR__ . '/../product_link.php';



    /**
     * Called when product gets created/updated
     */
    function on_product_save( $product_id ) {
        global $google_sheets;

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

        $res = $google_sheets->add_product( $product );

        log_activity('sheets', '<pre>' . print_r($res, true) . '</pre>');
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


    /**
     * Called when product status changes
     */
    function on_product_status_change($new_status, $old_status, $post){
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
    add_action("transition_post_status", "on_product_status_change", 10, 3);