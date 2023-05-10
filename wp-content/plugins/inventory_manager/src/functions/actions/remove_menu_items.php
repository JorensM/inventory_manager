<?php

//Remove unneeded menu items from admin
function remove_menu_items(){

    /**
     * Top-level menus to remove
     */
    $to_remove = [
        "separator1",
        "separator2",
        //"index.php",
        "edit.php",
        "edit.php?post_type=page",
        "edit-comments.php",
        "themes.php",
        "tools.php",
        //"options-general.php",
        "woocommerce",
        "woocommerce-marketing",
        "wc-admin&path=/analytics/overview",
        "plugins.php"

    ];

    /*
        Submenus to remove. Format:
        [
            "menu1_slug" => [
                "submenu1_slug",
                "submenu2_slug
            ],
            "menu2_slug => [
                "submenu1_slug",
                "submenu2_slug"
            ]
        ]

        Some of the WooCommerce submenus don't get removed even if they're added to this list.
        The workaround is to hide these items with CSS in the customCSS() function
    */
    $submenus_to_remove = [
        "edit.php?post_type=product" => [
            "edit-tags.php?taxonomy=product_tag&post_type=product",
            "product_attributes",
            "product-reviews"
        ],
        "index.php" => [
            "update-core.php"
        ],
        "options-general.php" => [
            "options-general.php",
            "options-writing.php",
            "options-reading.php",
            "options-discussion.php",
            "options-media.php",
            "options-permalink.php",
            "options-privacy.php"

        ]
    ];

    

    foreach($to_remove as $item){
        remove_menu_page($item);
    }

    foreach($submenus_to_remove as $parent_slug => $parent){
        foreach($parent as $submenu_item){
            remove_submenu_page($parent_slug, $submenu_item);
        }
    }

}
add_action( 'admin_init', "remove_menu_items" );