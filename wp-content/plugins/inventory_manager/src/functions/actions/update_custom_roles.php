<?php

    //Update roles
    function update_custom_roles() {

        add_role( 'regular', 'Regular', array( 
            'read' => true, 
            "view_admin_dashboard" => true,
            "price" => false,
            'publish_posts' => false
        ));

        $regular_caps = [
            "edit_themes",
            "activate_plugins",
            "edit_plugins",
            "edit_users",
            "edit_files",
            "manage_options",
            "moderate_comments",
            "manage_categories",
            "manage_links",
            "upload_files",
            "import",
            "unfiltered_html",
            "edit_posts",
            "edit_others_posts",
            "edit_published_posts",
            "edit_pages",
            "read",
            "level_10",
            "level_9",
            "level_8",
            "level_7",
            "level_6",
            "level_5",
            "level_4",
            "level_3",
            "level_2",
            "level_1",
            "level_0",
            "edit_others_pages",
            "edit_published_pages",
            "publish_pages",
            "delete_pages",
            "delete_others_pages",
            "delete_published_pages",
            "delete_posts",
            "delete_others_posts",
            "delete_published_posts",
            "delete_private_posts",
            "edit_private_posts",
            "read_private_posts",
            "delete_private_pages",
            "edit_private_pages",
            "read_private_pages",
            "delete_users",
            "create_users",
            "unfiltered_upload",
            "edit_dashboard",
            "update_plugins",
            "delete_plugins",
            "install_plugins",
            "update_themes",
            "install_themes",
            "update_core",
            "list_users",
            "remove_users",
            "promote_users",
            "edit_theme_options",
            "delete_themes",
            "export",
            "manage_woocommerce",
            "view_woocommerce_reports",
            "edit_product",
            "read_product",
            "delete_product",
            "edit_products",
            "edit_others_products",
            "publish_products",
            "read_private_products",
            "delete_products",
            "delete_private_products",
            "delete_published_products",
            "delete_others_products",
            "edit_private_products",
            "edit_published_products",
            "manage_product_terms",
            "edit_product_terms",
            "delete_product_terms",
            "assign_product_terms",
            "edit_shop_order",
            "read_shop_order",
            "delete_shop_order",
            "edit_shop_orders",
            "edit_others_shop_orders",
            "publish_shop_orders",
            "read_private_shop_orders",
            "delete_shop_orders",
            "delete_private_shop_orders",
            "delete_published_shop_orders",
            "delete_others_shop_orders",
            "edit_private_shop_orders",
            "edit_published_shop_orders",
            "manage_shop_order_terms",
            "edit_shop_order_terms",
            "delete_shop_order_terms",
            "assign_shop_order_terms",
            "edit_shop_coupon",
            "read_shop_coupon",
            "delete_shop_coupon",
            "edit_shop_coupons",
            "edit_others_shop_coupons",
            "publish_shop_coupons",
            "read_private_shop_coupons",
            "delete_shop_coupons",
            "delete_private_shop_coupons",
            "delete_published_shop_coupons",
            "delete_others_shop_coupons",
            "edit_private_shop_coupons",
            "edit_published_shop_coupons",
            "manage_shop_coupon_terms",
            "edit_shop_coupon_terms",
            "delete_shop_coupon_terms",
            "assign_shop_coupon_terms"
        ];

        $regular_role = get_role("regular");
        $regular_role->add_cap("view_admin_dashboard", true);
        $regular_role->add_cap("edit_posts", true);
        $regular_role->add_cap("price", false);
        $regular_role->add_cap("manage_woocommerce", true);
        $regular_role->add_cap("level_10", true);
        $regular_role->add_cap("read_product", true);
        $regular_role->add_cap("view_woocommerce_reports", true);
        $regular_role->add_cap('publish_posts', false);

        foreach($regular_caps as $cap){
            $regular_role->add_cap($cap, true);
        }

        $admin_role = get_role("administrator");

        $admin_role->add_cap("price", true);
        $admin_role->add_cap("shipping", true);

        //Roles to remove
        $to_remove = [
            "subscriber",
            "editor",
            "author",
            "contributor",
            "customer",
            "shop_manager"
        ];

        foreach($to_remove as $single){
            remove_role($single);
        }
    }
    add_action( 'init', 'update_custom_roles' );