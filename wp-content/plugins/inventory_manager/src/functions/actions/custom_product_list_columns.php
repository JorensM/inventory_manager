<?php

require_once __DIR__ . '/../../const.php';

/**
 * Add custom columns to products table
 */
function custom_product_list_columns( $columns ){

    //Add 'Status' column
    $columns["status"] = "Status";

    //Remove 'In Stock' column
    unset($columns['is_in_stock']);

    //Return modified columns
    return $columns;
}
//Add filter for custom product table columns
add_filter( 'manage_product_posts_columns', 'custom_product_list_columns', 15 ) ;

/**
 * Content for custom columns
 */
function custom_product_list_columns_content($column_id, $post_id){
    if($column_id == "status"){
        //'Status' column content
        $status_str = "";
        $status = get_post_status($post_id);

        switch($status){
            case "publish":
                $status_str = "Published";
                break;
            case "trash":
                $status_str = "Trashed";
                break;
            case "sold":
                $status_str = "Sold";
                break;
            case "draft":
                $status_str = "Draft";
                break;
            default:
                $status_str = $status;
                break;
        }
        echo __($status_str, 'inv-mgr');
    }
}
add_action( 'manage_posts_custom_column','custom_product_list_columns_content', 10, 2 );