<?php

//Requires
require_once __DIR__ . '/../get_sanitized_page_id.php';

/**
 * Adds custom CSS to the page
 * To add custom CSS to every page, edit the custom_css_all() function
 * To add custom CSS to a specific page, create/edit a function custom_css_<PAGEID>() and echo the style there (examples below)
 * 
 * @return void
 */
function custom_css() {
    //Store page id in a variable
    $page_id = get_current_screen()->id;

    //echo $page_id;

    custom_css_all();

    $sanitized_page_id = get_sanitized_page_id();

    $fn_name = "custom_css_$sanitized_page_id";

    if ( function_exists( $fn_name ) ) {
        $fn_name();
    }

    // //Apply CSS to product page
    // if( $page_id === 'product' ) {
    //     custom_css_product();
    // }

    // //Apply CSS to user page
    // if($page_id === "user"){
    //     custom_css_user();
    // }

    // //Apply CSS to profile page
    // if($page_id === "profile"){
    //     custom_css_profile();
    // }

    // //Apply CSS to dashboard
    // if($page_id === "dashboard"){
    //     custom_css_dashboard();
    // }

    // if($page_id === "edit-product"){
    //     custom_css_edit_product();
    // }

}

//Add action for custom CSS
add_action('admin_head', 'custom_css');

/**
 * Custom CSS for all pages
 */
function custom_css_all() {
    ?>
        <style>
            /* Hide unneeded element */
            #postdivrich,
            #commentsdiv,
            #postexcerpt,
            #visibility,
            #catalog-visibility,
            #post-preview,
            #edit-slug-box,
            /* .misc-pub-curtime, */
            /* .misc-pub-visibility, */
            .product-data-wrapper,
            /* ._sale_price_field, */
            .linked_product_options,
            .attribute_options,
            .advanced_options,
            .marketplace-suggestions_options,
            .variations_options, 
            .dimensions_field,
            .shipping_class_field,
            .woocommerce-layout__header,
            #wp-admin-bar-wp-logo,
            #wp-admin-bar-site-name,
            #wp-admin-bar-comments,
            #wp-admin-bar-new-post,
            #wp-admin-bar-new-media,
            #wp-admin-bar-new-page,
            #wp-admin-bar-new-shop_order,
            #wp-admin-bar-new-shop_coupon,
            #postimagediv,
            #tagsdiv-product_tag,
            #contextual-help-link-wrap
            /* #woocommerce-activity-panel */
            {
                display: none !important; 
            }

            li:has(> a[href="edit-tags.php?taxonomy=product_tag&post_type=product"])
            /* li:has(> a[href="edit-tags.php?taxonomy=product_cat&post_type=product"]) */
            {
                display: none !important;
            }
        </style>
    <?php

    //Hide price field if user doesn't have 'price' permission
    if(!user_can(wp_get_current_user(), "price")){
        ?>  
            <style>
                ._regular_price_field{
                    display: none !important;
                }
            </style>
        <?php
    }
    //Hide shipping tab if user doesn't have 'shipping' permission
    if(!user_can(wp_get_current_user(), "shipping")){
        ?>  
            <style>
                .shipping_options{
                    display: none !important;
                }
            </style>
        <?php
    }
}

/**
 * Custom CSS for product page
 */
function custom_css_product() {
    ?>
        <style>
            /* .metabox-prefs */
            /* .editor-expand */
            fieldset.metabox-prefs,
            fieldset.editor-expand,
            ._manage_stock_field,
            ._stock_status_field,
            ._sold_individually_field,
            ._weight_field,
            .misc-pub-curtime,
            .inventory_sold_individually,
            #post_status > option[value='pending'],
            ._sale_price_field .description
            {
                display: none !important
            }

            #wpbody{
                margin-top: 43px !important;
            }
        </style>
    <?php

    //If 'publish_posts' capability is not present for user, hide publish button on product page
    if( !user_can( wp_get_current_user(), "publish_posts" ) ){
        ?>  
            <style>
                ._sale_price_field,
                #publish{
                    display: none !important;
                }
            </style>
        <?php
    }
}

/**
 * Custom CSS for user page
 */
function custom_css_user() {
    ?>
        <style>
            /* .metabox-prefs */
            /* .editor-expand */
            .form-field:has(* label[for="url"]),
            tr:has(* label[for="send_user_notification"])
            {
                display: none !important
            }
        </style>
    <?php
}

/**
 * Custom CSS for profile page
 */
function custom_css_profile() {
    ?>
        <style>
            .user-rich-editing-wrap,
            .user-syntax-highlighting-wrap,
            .user-comment-shortcuts-wrap,
            .user-admin-bar-front-wrap,
            .user-url-wrap,
            .user-description-wrap,
            #application-passwords-section,
            #fieldset-billing,
            #fieldset-shipping
            {
                display: none !important
            }
        </style>
    <?php
}

/**
 * Custom CSS for dashboard page
 */
function custom_css_dashboard() {
    ?>
        <style>
            #screen-options-link-wrap,
            #welcome-panel,
            #dashboard-widgets-wrap
            {
                display: none !important
            }
        </style>
    <?php
}

/**
 * Custom CSS for product edit page
 */
function custom_css_edit_product() {
    ?>
        <style>
            a[href="http://localhost/inventory_manager/wp-admin/edit.php?post_type=product&page=product_importer"]
            {
                display: none !important;
            }
        </style>
    <?php
}

