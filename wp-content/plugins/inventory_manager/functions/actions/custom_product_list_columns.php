<?php

function custom_product_list_columns( $columns ){

    global $text_domain;
    
    //add column
    //$column = array( 'status' => __( 'Status', $text_domain ) ) ;

    //array_push($columns, $column);

    //array_splice( $columns, 6, 0, $column ) ;

    //array_splice($columns, sizeof($columns), 0, $column);

    $columns["status"] = "Status";

    return $columns;
}
add_filter( 'manage_product_posts_columns', 'custom_product_list_columns', 15 ) ;