<?php

    //Add views to the product list (such as 'Sold')
    //Views are list items being filtered, for example by 'Sold' products
    function add_product_table_views( $views ) {

        //'Sold' view
        //Get count of products with status 'sold'
        $sold_count = wp_count_posts("product")->sold;
        //Add view to the list of views using the $views variable
        $views['sold'] = "<a href='edit.php?post_status=sold&post_type=product'>Sold <span class='count'>($sold_count)</span></a>";

        return $views;
    }

    //Add filter
    add_filter( 'views_edit-product', 'add_product_table_views' );