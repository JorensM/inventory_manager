<?php

function add_custom_categories(){
    $categories_to_add = [
        [
            "name" => "Accessories",
            "uuid" => "62835d2e-ac92-41fc-9b8d-4aba8c1c25d5"
        ],
        [
            "name" => "Bass Cases",
            "uuid" => "bd397c15-0cf3-4c6f-8005-7b8309ced1c4"
        ],
    ];

    foreach($categories_to_add as $category){
        wp_insert_term( $category["name"], 'product_cat', array(
            'slug' => $category["uuid"] // optional
        ) );
    }

   
}
add_action("init", "add_custom_categories");