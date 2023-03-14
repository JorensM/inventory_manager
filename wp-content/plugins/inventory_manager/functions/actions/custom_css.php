<?php
//Remove unneeded Product fields
function custom_css() {
    //Store page id in a variable
    $page_id = get_current_screen()->id;

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
            ._sale_price_field,
            .linked_product_options,
            .attribute_options,
            .advanced_options,
            .marketplace-suggestions_options,
            .variations_options, 
            .dimensions_field,
            .shipping_class_field,
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

    //Apply CSS to product page
    if($page_id === "product"){
        ?>
            <style>
                /* .metabox-prefs */
                /* .editor-expand */
                fieldset.metabox-prefs,
                fieldset.editor-expand
                {
                    display: none !important
                }

                #wpbody{
                    margin-top: 43px !important;
                }
            </style>
        <?php
    }

    //Apply CSS to user page
    if($page_id === "user"){
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

    //Apply CSS to profile page
    if($page_id === "profile"){
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

    //Apply CSS to dashboard
    if($page_id === "dashboard"){
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

    if($page_id === "edit-product"){
        ?>
            <style>
                a[href="http://localhost/inventory_manager/wp-admin/edit.php?post_type=product&page=product_importer"]
                {
                    display: none !important;
                }
            </style>
        <?php
    }

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
    echo $page_id;
}
add_action('admin_head', 'custom_css');