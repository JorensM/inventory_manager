<?php
/*
    * Plugin Name:       Inventory Manager Core
    * Description:       Core plugin for the inventory manager website
    * Version:           1.0.0
    * Author:            JorensM
    * Author URI:        github.com/JorensM
    * Text Domain:       inv-mgr
*/

$domain = "inv-mgr";

/*

index.php
separator1
edit.php
upload.php
edit.php?post_type=page
edit-comments.php
separator2
themes.php
plugins.php
users.php
tools.php
options-general.php

*/

//Remove unneeded menu items from admin
function remove_menu_items(){

    $to_remove = [
        "separator1",
        "separator2",
        "index.php",
        "edit.php",
        "edit.php?post_type=page",
        "edit-comments.php",
        "themes.php",
        "tools.php",
        "options-general.php",
        "woocommerce",
        "woocommerce-marketing",
        "wc-admin&path=/analytics/overview"

    ];

    $submenus_to_remove = [
        ['edit.php?post_type=product', 'edit-tags.php?taxonomy=product_tag&post_type=product'],
        ['edit.php?post_type=product', 'product_attributes'],
        ['edit.php?post_type=product', 'product-reviews']
    ];

    //echo "test";

    // echo "<pre>";
    //     print_r(wp_get_nav_menu_items("edit.php?post_type=product"));
    // echo "</pre>";

    

    foreach($to_remove as $item){
        remove_menu_page($item);
    }

    foreach($submenus_to_remove as $item){
        remove_submenu_page($item[0], $item[1]);
    }

}
add_action( 'admin_init', "remove_menu_items" );

//Reorder admin menu items
function change_menu_order( $menu_ord ) {
    if ( !$menu_ord ) return true;

    return array(
        'users.php', // Dashboard
    );
}
add_filter( 'custom_menu_order', 'change_menu_order', 10, 1 );
add_filter( 'menu_order', 'change_menu_order', 10, 1 );

//Add custom fields to WooCommerce product editor
function woo_product_custom_fields(){
    global $woocommerce, $post, $domain;
    
    //Render fields
    echo '<div class="options_group">';
        //Brand info
        woocommerce_wp_text_input(
            array(
                'id'          => '_brand_info',
                'label'       => __( 'Brand Info', $domain ),
                'placeholder' => '',
                'desc_tip'    => false,
                'custom_attributes' => array( 'required' => 'required' ),
                // 'description' => __( "Enter Brand info", $domain )
            )
        );

        //Model info
        woocommerce_wp_text_input(
            array(
                'id'          => '_model_info',
                'label'       => __( 'Model info', $domain ),
                'placeholder' => '',
                'desc_tip'    => false,
                'custom_attributes' => array( 'required' => 'required' ),
                // 'description' => __( "Enter Model info", $domain )
            )
        );

        //Year
        // echo "<pre>";
        //     print_r(get_post_meta($post->ID));
        // echo "</pre>";
        woocommerce_wp_text_input(
            array(
                'id'                => '_year_field',
                'label'             => __( 'Approx./exact year', $domain ),
                'placeholder'       => '',
                // 'value' => "2023",
                //'desc_tip'    		=> false,
                //'description'       => __( "Here's some really helpful text that appears next to the field.", 'woocommerce' ),
                'type'              => 'number',
                'custom_attributes' => array(
                        'step' 	=> '1',
                        'min'	=> '0'
                    )
            )
        );

        //Color
        woocommerce_wp_text_input(
            array(
                'id'                => '_color_field',
                'label'             => __( 'Finish color', $domain ),
                'placeholder'       => '',
            )
        );

        //Country
        woocommerce_wp_text_input(
            array(
                'id'                => '_country_field',
                'label'             => __( 'Country of manufacture', $domain ),
                'placeholder'       => '',
            )
        );

        //Handedness
        woocommerce_wp_select(
            array(
                'id'      => '_handedness_field',
                'label'   => __( 'Handedness', $domain ),
                'options' => array(
                    'right'   => __( 'Right', $domain ),
                    'left'   => __( 'Left', $domain ),
                )
            )
        );

        //Body type
        woocommerce_wp_text_input(
            array(
                'id'                => '_body_type_field',
                'label'             => __( 'Body type', $domain ),
                'placeholder'       => '',
            )
        );

        //String configuration
        woocommerce_wp_text_input(
            array(
                'id'                => '_string_configuration_field',
                'label'             => __( 'Number of strings/string configuration', $domain ),
                'placeholder'       => '',
            )
        );

        //Fretboard material
        woocommerce_wp_text_input(
            array(
                'id'                => '_fretboard_material_field',
                'label'             => __( 'Fretboard material', $domain ),
                'placeholder'       => '',
            )
        );

        //Neck material
        woocommerce_wp_text_input(
            array(
                'id'                => '_neck_material_field',
                'label'             => __( 'Neck Material', $domain ),
                'placeholder'       => '',
            )
        );

        //Condition
        woocommerce_wp_select(
            array(
                'id'      => '_condition_field',
                'label'   => __( 'Condition', $domain ),
                'options' => array(
                    'used'   => __( 'Used', $domain ),
                    'new'   => __( 'New', $domain ),
                    'for-parts'   => __( 'For parts', $domain ),
                )
            )
        );

        //Notes
        woocommerce_wp_textarea_input(
            array(
                'id'          => '_notes_field',
                'label'       => __( 'Notes ', 'woocommerce' ),
                'placeholder' => '',
                "style" => "height: 140px;",
                // 'rows' => 6,
                // 'cols' => 4
            )
        );

    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'woo_product_custom_fields' );

//Save custom fields
function woo_save_product_fields( $post_id ){

    $field_ids = [
        "_brand_info",
        "_year_field",
        "_handedness_field"
    ];

    $textarea_field_ids = [
        "_notes_field"
    ];

    foreach($field_ids as $field_id){
        $field_data = $_POST[$field_id];
        update_post_meta( $post_id, $field_id, esc_attr( $field_data ) );
    }

    foreach($textarea_field_ids as $field_id){
        $field_data = $_POST[$field_id];
	    update_post_meta( $post_id, $field_id, esc_html( $field_data ) );
    }

    

}
add_action( 'woocommerce_process_product_meta', 'woo_save_product_fields' );

//Remove unneeded Product fields
function woo_remove_fields() {
    ?>
        <style>
            #postdivrich,
            #commentsdiv,
            #postexcerpt,
            #visibility,
            #catalog-visibility,
            #post-preview,
            #edit-slug-box,
            .misc-pub-curtime,
            .misc-pub-visibility,
            .product-data-wrapper,
            ._sale_price_field
            {
                display: none !important; 
            }

            li:has(> a[href="edit-tags.php?taxonomy=product_tag&post_type=product"]){
                display: none !important;
            }
        </style>
  <?php
    if(!user_can(wp_get_current_user(), "price")){
        ?>  
            <style>
                ._regular_price_field{
                    display: none !important;
                }
            </style>
        <?php
    }
}
add_action('admin_head', 'woo_remove_fields');

//Add custom Javascript
function custom_js() {
    $page_id = get_current_screen()->id;

    echo $page_id;

    //Custom style for users page
    if($page_id === "users"){
        ?>
            <script>
                console.log("test");
                document.getElementById("posts").innerHTML = "Products";
            </script>
        <?php
    }
    else if($page_id === "product"){
        ?>

            <script>

                //Generate title based on entered information
                function generateTitle(input_element){
                    const brand_info = document.getElementById("_brand_info").value;
                    const model_info = document.getElementById("_model_info").value;
                    const year = document.getElementById("_year_field").value;
                    const color = document.getElementById("_color_field").value;

                    const title = `${brand_info} ${model_info} ${year} ${color}`;

                    input_element.value = title;
                }

                const form = document.getElementById("post");

                const categories_pop = document.getElementById("product_cat-pop");
                const categories_all = document.getElementById("product_cat-all");

                const inputs_pop = categories_pop.querySelectorAll("input[type='checkbox']");
                const inputs_all = categories_all.querySelectorAll("input[type='checkbox']");

                const title_input = document.getElementById("title");
                const title_div = document.getElementById("titlediv");
                

                //Make title field required
                title_input.required = true;

                //Add "generate title" button
                title_div.insertAdjacentHTML("afterend", `
                    <div
                        style='
                            display: flex;
                            justify-content: flex-end
                        '
                    >
                        <button 
                            type='button'
                            class='button button-secondary'
                            onclick='generateTitle(title_input)'
                        >
                            Generate title
                        </button>
                    </div>
                    
                `);



                //Prevent "are you sure you want to leave this page" popup
                window.addEventListener('beforeunload', function (event) {
                    event.stopImmediatePropagation();
                });

                //On form submit
                form.addEventListener("submit", (e) => {
                    e.preventDefault();


                    //Check if category is specified, and cancel form submission if false
                    let has_category = false;

                    for(let i = 0; i < inputs_pop.length; i++) {
                        let item = inputs_pop[i];
                        if(item.checked){
                            has_category = true;
                            break;
                        }
                    }
                    
                    if(!has_category){
                        for(let i = 0; i < inputs_all.length; i++) {
                            let item = inputs_all[i];
                            if(item.checked){
                                has_category = true;
                                break;
                            }
                        }
                    }

                    if(has_category){
                        form.submit();
                    }else{
                        alert("Please select category!");
                    }

                    
                    
                })
            </script>

        <?php
    }

    ?>
        <script>
            //document.querySelector("")
        </script>
    <?php
    
        
  
}
add_action('admin_footer', 'custom_js');



//Remove type options from woocommerce product editor
function woo_remove_type_options($options){
    // remove "Virtual" checkbox
	if( isset( $options[ 'virtual' ] ) ) {
		unset( $options[ 'virtual' ] );
	}
	// remove "Downloadable" checkbox
	if( isset( $options[ 'downloadable' ] ) ) {
		unset( $options[ 'downloadable' ] );
	}
	return $options;
}
add_filter( 'product_type_options', "woo_remove_type_options");

//Update roles
function update_custom_roles() {

    add_role( 'regular', 'Regular', array( 
        'read' => true, 
        "view_admin_dashboard" => true,
        "price" => false,
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
        "publish_posts",
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

    foreach($regular_caps as $cap){
        $regular_role->add_cap($cap, true);
    }

    $admin_role = get_role("administrator");

    $admin_role->add_cap("price", true);

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