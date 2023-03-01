<?php
/*
    * Plugin Name:       Inventory Manager Core
    * Description:       Core plugin for the inventory manager website
    * Version:           1.0.0
    * Author:            JorensM
    * Author URI:        github.com/JorensM
    * Text Domain:       inv-mgr
*/

/*

index.php
separator1
edit.php
upload.php
edit.php?post_type=page
edit-comments.php
separator2
themes.php
plugins.php
users.php
tools.php
options-general.php

*/

//Remove unneeded menu items from admin
function remove_menu_items(){

    $to_remove = [
        "separator1",
        "separator2",
        "index.php",
        "edit.php",
        "edit.php?post_type=page",
        "edit-comments.php",
        "themes.php",
        "tools.php",
        "options-general.php",
        "woocommerce",
        "woocommerce-marketing",
        "wc-admin&path=/analytics/overview"

    ];

    foreach($to_remove as $item){
        remove_menu_page($item);
    }
}
add_action( 'admin_init', "remove_menu_items" );

//Reorder admin menu items
function change_menu_order( $menu_ord ) {
    if ( !$menu_ord ) return true;

    return array(
        'users.php', // Dashboard
    );
}
add_filter( 'custom_menu_order', 'change_menu_order', 10, 1 );
add_filter( 'menu_order', 'change_menu_order', 10, 1 );

//Add custom fields to WooCommerce product editor
function woo_product_custom_fields(){
    global $woocommerce, $post;
    echo '<div class="options_group">';
        woocommerce_wp_text_input(
            array(
                'id'          => '_text_field',
                'label'       => __( 'My Text Field', 'woocommerce' ),
                'placeholder' => 'http://',
                'desc_tip'    => true,
                'description' => __( "Here's some really helpful tooltip text.", "woocommerce" )
            )
        );
    echo '</div>';
}

add_action( 'woocommerce_product_options_general_product_data', 'woo_product_custom_fields' );

//Remove unneeded Product fields
function woo_remove_fields() {
    ?>
        <style>
            #postdivrich,
            #commentsdiv,
            #postexcerpt,
            #visibility,
            #catalog-visibility,
            #post-preview,
            .misc-pub-curtime,
            .misc-pub-visibility
            {
                display: none; 
            }
        </style>
  <?php
}
add_action('admin_head', 'woo_remove_fields');