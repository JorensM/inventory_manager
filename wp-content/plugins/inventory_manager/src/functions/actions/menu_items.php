<?php

    /**
     * This file contains code related to sidenav menu item manipulation
     */

    //--Requires--//
    require_once 'pages.php' ;

    /**
     * Add menu items to sidenav
     */
    function add_menu_items(){

        //Settings page
        add_submenu_page("options-general.php", "General", "General", "manage_options", "settings-general", "settings_general_page");

        //Guide pages
        add_menu_page('inv-mgr-guide', 'Guide', 'manage_options', 'inv-mgr-guide', 'inv_mgr_user_guide', 'dashicons-info-outline', 200);
        add_submenu_page('inv-mgr-guide', 'Developer guide', 'Developer guide', 'manage_options', 'developer-guide', 'inv_mgr_dev_guide');
    }

    //Add action
    add_action("admin_menu", "add_menu_items");

    /**
     * Remove unneeded menu items from sidenav
     */
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
            "wc-admin&path=/wc-pay-welcome-page",
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