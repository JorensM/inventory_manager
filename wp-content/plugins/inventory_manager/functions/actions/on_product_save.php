<?php

require_once(__DIR__ . "/../generate_barcode.php");

//Called when product gets created/updated
function on_product_save($product_id){
    $product = wc_get_product($product_id);

    if($product->get_sku()){
        generate_barcode($product->get_sku(), $product_id, $product->get_title());
    }
}
add_action( 'woocommerce_new_product', 'on_product_save', 10, 1 );
add_action( 'woocommerce_update_product', 'on_product_save', 10, 1 );