<?php

function custom_product_list_columns_content($column_id, $post_id){
    if($column_id == "status"){
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
                $status_str = "Unknown";
                break;
        }
        echo __($status_str, 'inv-mgr');
    }
}
add_action( 'manage_posts_custom_column','custom_product_list_columns_content', 10, 2 );