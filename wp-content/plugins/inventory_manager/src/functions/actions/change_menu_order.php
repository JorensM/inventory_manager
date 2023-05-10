<?php

    //Reorder admin menu items
    function change_menu_order( $menu_ord ) {
        if ( !$menu_ord ) return true;

        //The order in which the items should appear
        return array(
            "index.php", // Dashboard
            "edit.php?post_type=product", //Products
            "users.php", // Users
        );
    }

    //Add filters
    add_filter( 'custom_menu_order', 'change_menu_order', 10, 1 );
    add_filter( 'menu_order', 'change_menu_order', 10, 1 );