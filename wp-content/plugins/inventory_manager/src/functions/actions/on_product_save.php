<?php

require_once(__DIR__ . "/../generate_barcode.php");
require_once __DIR__ . '/../log_activity.php';
require_once __DIR__ . '/../product_link.php';

//Called when product gets created/updated
function on_product_save($product_id){
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

function on_product_new( $post_id, $post, $update ) {
    if($update) {
        return;
    }
    $product = wc_get_product( $post_id );
    $current_user = wp_get_current_user();
    $product_link = product_link($product);
    //$product_url = admin_url() . "post.php?post={$product_id}&action=edit";
    log_activity( $current_user->nickname, "Created product $product_link" );
}

function on_product_update( $product_id ) {
    $product = wc_get_product( $product_id );
    $current_user = wp_get_current_user();
    $product_url = admin_url() . "post.php?post={$product_id}&action=edit";
    if($current_user->nickname){
        //log_activity( $current_user->nickname, "Updated product <a href='{$product_url}'>{$product->get_title()}</a>" );
    }
}

//add_action("woocommerce_process_product_meta", "on_product_save", 1000, 1);
//add_action( 'woocommerce_new_product', 'on_product_new', 10, 1 );
//add_action( 'woocommerce_new_product', 'on_product_save', 10, 1 );
add_action( 'woocommerce_update_product', 'on_product_save', 10, 1 );
//add_action( 'save_post_product', 'on_product_new', 10, 3);
//add_action( 'woocommerce_update_product', 'on_product_update', 10, 1 );