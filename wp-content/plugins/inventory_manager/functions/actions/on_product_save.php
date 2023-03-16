<?php

require_once(__DIR__ . "/../generate_barcode.php");
require_once __DIR__ . '/../log_activity.php';

//Called when product gets created/updated
function on_product_save($product_id){
    $product = wc_get_product($product_id);

    if($product->get_sku()){
        generate_barcode($product->get_sku(), $product_id, $product->get_title());
    }
}

function on_product_new( $product_id ) {
    error_log('test1');
    $product = wc_get_product( $product_id );
    error_log('test2');
    $current_user = wp_get_current_user();
    error_log('test3');
    $product_url = admin_url() . "post.php?post={$product_id}&action=edit";
    //echo $current_user->nickname;
    error_log('test123');
    log_activity( $current_user->nickname, "Created product <a href='{$product_url}'>{$product->get_title()}</a>" );
}

function on_product_update( $product_id ) {
    error_log('test1');
    $product = wc_get_product( $product_id );
    error_log('test2');
    $current_user = wp_get_current_user();
    error_log('test3');
    $product_url = admin_url() . "post.php?post={$product_id}&action=edit";
    //echo $current_user->nickname;
    error_log('test123');
    if($current_user->nickname){
        log_activity( $current_user->nickname, "Updated product <a href='{$product_url}'>{$product->get_title()}</a>" );
    }
}

add_action( 'woocommerce_new_product', 'on_product_new', 10, 1 );
add_action( 'woocommerce_new_product', 'on_product_save', 10, 1 );
add_action( 'woocommerce_update_product', 'on_product_save', 10, 1 );
add_action( 'woocommerce_update_product', 'on_product_update', 10, 1 );