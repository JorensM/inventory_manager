<?php
function add_product_views( $views ) {
    // Manipulate $views

    // echo "<pre>";
    //     htmlspecialchars(print_r($views, true));
    // echo "<pre>";

    $sold_count = wp_count_posts("product")->sold;

    $views["sold"] = "<a href='edit.php?post_status=sold&post_type=product'>Sold <span class='count'>($sold_count)</span></a>";

    return $views;
}

add_filter( 'views_edit-product', 'add_product_views' );