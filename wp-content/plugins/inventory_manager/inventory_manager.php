<?php
/*
    * Plugin Name:       Inventory Manager Core
    * Description:       Core plugin for the inventory manager website
    * Version:           1.0.0
    * Author:            JorensM
    * Author URI:        github.com/JorensM
    * Text Domain:       inv-mgr
*/

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//error_log("hello123");
//--Requires--//
//Constants
require_once("const.php");

//Classes
require_once("classes/ReverbListingManager.php");
require_once("classes/AdminNotice.php");

//Functions
require_once("functions/generateBarcode.php");
require_once("functions/reverbCreateListing.php");
require_once("functions/reverbUpdateListing.php");
require_once("functions/checkListingsAndUpdateProducts.php");



$text_domain = "inv-mgr";

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

//echo "<div style='height:200px'> </div>";
//echo "hello";

//$barcode = generateBarcode();

//generateBarcode("1234", "123");

//echo '<img src="data:image/png;base64,' . base64_encode($barcode) . '">';


//Remove unneeded menu items from admin
function remove_menu_items(){

    global $submenu;

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
        List of submenus to remove.
        Some of the WooCommerce submenus don't get removed even if they're added to this list.
        The workaround was to hide these items with CSS in the custom_css() function
    */
    // $submenus_to_remove = [
    //     ["edit.php?post_type=product", "edit-tags.php?taxonomy=product_tag&post_type=product"],
    //     ["edit.php?post_type=product", "product_attributes"],
    //     ["edit.php?post_type=product", "product-reviews"],
    //     ["index.php", "update-core.php"],
    //     ["options-general.php", "options-general.php"],

    // ];


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
        The workaround is to hide these items with CSS in the custom_css() function
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

    // echo "<pre>";
    //     print_r($submenu["edit.php?post_type=product"]);
    // echo "</pre>";

    

    foreach($to_remove as $item){
        remove_menu_page($item);
    }

    foreach($submenus_to_remove as $parent_slug => $parent){
        foreach($parent as $submenu_item){
            remove_submenu_page($parent_slug, $submenu_item);
        }
        //remove_submenu_page($item[0], $item[1]);
    }

}
add_action( 'admin_init', "remove_menu_items" );

function render_reverb_token_field(){
    ?>  
        <input type="text" name="reverb_token" id="reverb_token" value="<?php echo get_option("reverb_token") ?>">
    <?php
}

function render_settings_section(){
    //echo "abc";
}




// General settings page
function settingsGeneralPage(){
    ?>
    <form method="POST" action="options.php">
        <?php 
            settings_fields( 'settings_page_settings-general' );	//pass slug name of page, also referred to in Settings API as option group name
            do_settings_sections( 'settings_page_settings-general' ); 	//pass slug name of page
            submit_button();
        ?>
    </form>
    <?php
}

// Add menu items
function add_menu_items(){
    add_submenu_page("options-general.php", "General", "General", "manage_options", "settings-general", "settingsGeneralPage");
}
add_action("admin_menu", "add_menu_items");

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
add_filter( 'custom_menu_order', 'change_menu_order', 10, 1 );
add_filter( 'menu_order', 'change_menu_order', 10, 1 );

//Add custom fields to WooCommerce product editor
function woo_product_custom_fields(){
    global $woocommerce, $post, $text_domain;
    
    //Render fields
    echo '<div class="options_group">';
        //Brand info
        woocommerce_wp_text_input(
            array(
                'id'          => 'brand_info',
                'label'       => __( 'Brand Info', $text_domain ),
                'placeholder' => '',
                'desc_tip'    => false,
                'custom_attributes' => array( 'required' => 'required' ),
                // 'description' => __( "Enter Brand info", $text_domain )
            )
        );

        //Model info
        woocommerce_wp_text_input(
            array(
                'id'          => 'model_info',
                'label'       => __( 'Model info', $text_domain ),
                'placeholder' => '',
                'desc_tip'    => false,
                'custom_attributes' => array( 'required' => 'required' ),
                // 'description' => __( "Enter Model info", $text_domain )
            )
        );

        //Year
        // echo "<pre>";
        //     print_r(get_post_meta($post->ID));
        // echo "</pre>";
        woocommerce_wp_text_input(
            array(
                'id'                => 'year_field',
                'label'             => __( 'Approx./exact year', $text_domain ),
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
                'id'                => 'color_field',
                'label'             => __( 'Finish color', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Country
        woocommerce_wp_text_input(
            array(
                'id'                => 'country_field',
                'label'             => __( 'Country of manufacture', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Handedness
        woocommerce_wp_select(
            array(
                'id'      => 'handedness_field',
                'label'   => __( 'Handedness', $text_domain ),
                'options' => array(
                    'right'   => __( 'Right', $text_domain ),
                    'left'   => __( 'Left', $text_domain ),
                )
            )
        );

        //Body type
        woocommerce_wp_text_input(
            array(
                'id'                => 'body_type_field',
                'label'             => __( 'Body type', $text_domain ),
                'placeholder'       => '',
            )
        );

        //String configuration
        woocommerce_wp_text_input(
            array(
                'id'                => 'string_configuration_field',
                'label'             => __( 'Number of strings/string configuration', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Fretboard material
        woocommerce_wp_text_input(
            array(
                'id'                => 'fretboard_material_field',
                'label'             => __( 'Fretboard material', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Neck material
        woocommerce_wp_text_input(
            array(
                'id'                => 'neck_material_field',
                'label'             => __( 'Neck Material', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Condition
        woocommerce_wp_select(
            array(
                'id'      => 'condition_field',
                'label'   => __( 'Condition', $text_domain ),
                'options' => array(
                    'used'   => __( 'Used', $text_domain ),
                    // 'new'   => __( 'New', $text_domain ),
                    'non-functioning'   => __( 'Non-functioning', $text_domain ),
                )
            )
        );

        //Notes
        woocommerce_wp_textarea_input(
            array(
                'id'          => 'notes_field',
                'label'       => __( 'Notes ', $text_domain ),
                'placeholder' => '',
                "style" => "height: 140px;",
                // 'rows' => 6,
                // 'cols' => 4
            )
        );

        woocommerce_wp_checkbox(
            array(
                'id'            => 'reverb_draft',
                'wrapper_class' => 'show_if_simple',
                'label'         => __('<b>Reverb:</b> save as draft', $text_domain ),
                'desc_tip'      => true,
                'description'   => __( 'If this is checked, listing will be created as draft on Reverb, instead of being published', $text_domain )
            )
        );

    echo '</div>';
}
add_action( 'woocommerce_product_options_general_product_data', 'woo_product_custom_fields' );

//Custom field ids
$custom_field_ids = [
    "brand_info",
    "model_info",
    "year_field",
    "handedness_field",
    "color_field",
    "country_field",
    "body_type_field",
    "string_configuration_field",
    "fretboard_material_field",
    "neck_material_field",
    "condition_field",
];

//Custom textarea field ids
$custom_textarea_field_ids = [
    "notes_field"
];

//Custom checkbox field ids
$custom_cb_field_ids = [
    "reverb_draft"
];

//Save custom fields
function woo_save_product_fields( $post_id ){

    global $custom_field_ids, $custom_cb_field_ids;

    $field_ids = $custom_field_ids;

    $textarea_field_ids = [
        "notes_field"
    ];

    $product = wc_get_product($post_id);

    foreach($field_ids as $field_id){
        $field_data = esc_attr($_POST[$field_id]);
        update_post_meta( $post_id, $field_id, $field_data );
        if($product->get_meta($field_id)){
            $product->update_meta_data($field_id, $field_data);
        }else{
            $product->add_meta_data($field_id, $field_data);
        }
    }

    foreach($textarea_field_ids as $field_id){
        $field_data = esc_html($_POST[$field_id]);
	    update_post_meta( $post_id, $field_id, $field_data );
        if($product->get_meta($field_id)){
            $product->update_meta_data($field_id, $field_data);
        }else{
            $product->add_meta_data($field_id, $field_data);
        }
        
    }

    foreach($custom_cb_field_ids as $field_id){
        $field_data = isset( $_POST['reverb_draft'] ) ? 'yes' : 'no';
        if($product->get_meta($field_id)){
            $product->update_meta_data($field_id, $field_data);
        }else{
            $product->add_meta_data($field_id, $field_data);
        }
    }

    //echo $_POST["reverb_draft"];

    // if(!$product->get_meta("is_sold")){
    //     $product->add_meta_data("is_sold", "no");
    // }

    $product->save();
    

}
add_action( 'woocommerce_process_product_meta', 'woo_save_product_fields' );

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

//Add custom Javascript
function custom_js() {
    $page_id = get_current_screen()->id;

    //echo $page_id;

    //Custom style for users page
    if($page_id === "users"){
        ?>
            <script>
                console.log("test");
                document.getElementById("posts").innerHTML = "Products";
            </script>
        <?php
    }
    //Begin "product" page
    else if($page_id === "product"){
        ?>

            <script>

                const status_dropdown = document.getElementById("post_status");

                <?php
                    if(wc_get_product()->get_status() == 'sold'){
                        ?>
                            document.getElementById("post-status-display").innerHTML = "<b>Sold</b>";
                        <?php
                    }
                ?>
                status_dropdown.insertAdjacentHTML("beforeend", "<option value='sold'>Sold</option>");

                //Generate title based on entered information
                function generateTitle(input_element){
                    //Add a string to an array if the string is set and not empty
                    function addStrIfNotEmpty(str, arr){
                        if(str && str !== ""){
                            arr.push(str);
                        }
                    }

                    let brand_info = document.getElementById("brand_info").value;
                    let model_info = document.getElementById("model_info").value;
                    let year = document.getElementById("year_field").value;
                    let color = document.getElementById("color_field").value;

                    // model_info = addSpaceOrEmpty(model_info);
                    // year_info = addSpaceOrEmpty(year);
                    // color_info = addSpaceOrEmpty(color);

                    //Turn info into array of strings
                    let title_parts = [];
                    addStrIfNotEmpty(brand_info, title_parts);
                    addStrIfNotEmpty(model_info, title_parts);
                    addStrIfNotEmpty(year, title_parts);
                    addStrIfNotEmpty(color, title_parts);

                    const title = title_parts.join(" ");

                    //Hide input label after generating title
                    if(title.replaceAll(" ", "") !== "" && title !== null && title !== undefined){
                        input_element.value = title;
                        document.getElementById("title-prompt-text").classList.add("screen-reader-text");
                    }
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
                            onclick='prefillData()'
                        >
                            Prefill data
                        </button>
                        <button 
                            type='button'
                            class='button button-secondary'
                            onclick='generateTitle(title_input)'
                        >
                            Generate title
                        </button>
                    </div>
                    
                `);

                function prefillData(){
                    document.getElementById("_regular_price").value = 50;
                    document.getElementById("brand_info").value = 21;
                    document.getElementById("model_info").value = 21;
                    document.getElementById("year_field").value = 2000;
                    document.getElementById("notes_field").value = "abcd";
                    document.getElementById("in-product_cat-18").checked = true;
                    document.getElementById("in-product_cat-19").checked = true;
                }

                //Prevent "are you sure you want to leave this page" popup
                window.addEventListener('beforeunload', function (event) {
                    event.stopImmediatePropagation();
                });

                //On form submit
                form.addEventListener("submit", (e) => {
                    //console.log(e);
                    //e.preventDefault();
                    //return;


                    //Check if category is specified, and cancel form submission if false
                    let has_category = false;

                    for(let i = 0; i < inputs_pop.length; i++) {
                        let item = inputs_pop[i];
                        if(item.value === "15"){
                            console.log ("Is uncategorized");
                        }
                        if(item.checked && item.value !== "15"){
                            has_category = true;
                            break;
                        }
                    }
                    
                    if(!has_category){
                        for(let i = 0; i < inputs_all.length; i++) {
                            let item = inputs_all[i];
                            if(item.checked && item.value !== "15"){
                                has_category = true;
                                break;
                            }
                        }
                    }

                    if(!has_category){
                        e.preventDefault();
                        alert("Please select a category!");
                    }

                })
            </script>

        <?php
        //Get barcode
        $file_ext = ".png";

        $product = wc_get_product();

        $barcode_url =  wp_upload_dir()["baseurl"] . "/" . $product->get_id() . $file_ext;

        $barcode_filename = wp_upload_dir()["basedir"] . "/" . $product->get_id() . $file_ext;

        $barcode_exists = false;

        $file_headers = @get_headers($barcode_url);
        if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $barcode_exists = false;
        }
        else {
            $barcode_exists = true;
        } 
        //If barcode exists, show it
        if($barcode_exists){
            ?>
                <script>
                    const minor_publishing_div = document.getElementById("minor-publishing");

                    const barcode_html = `
                        <div 
                            style='
                                display: flex; 
                                align-items: center;
                                justify-content: center;
                                width: 100%;
                                padding: 16px;
                                box-sizing: border-box;
                            '
                        >
                            <img 
                                src='<?php echo $barcode_url; ?>'
                                style='
                                    width: 100%;
                                '
                            >
                        </div>  
                    `

                    minor_publishing_div.insertAdjacentHTML("beforeend", barcode_html);
                </script>
            <?php
        }
    //End "product" page
    //Begin "profile" page
    }else if($page_id === "profile"){
        ?>
            <script>
                const divs = document.getElementsByTagName("h2");

                for (let x = 0; x < divs.length; x++) {
                    const div = divs[x];
                    const content = div.textContent.trim();
                
                    if (content == 'Customer billing address' || content == 'Customer shipping address') {
                        div.style.display = 'none';
                    }
                }
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

function addCustomCategories(){
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
add_action("init", "addCustomCategories");

//generateBarcode("1234", "hello");
/* Render barcode below SKU field. deprecated */
// function render_product_barcode(){

//     $file_ext = ".png";

//     $product = wc_get_product();

//     $barcode_url =  wp_upload_dir()["baseurl"] . "/" . $product->get_id() . $file_ext;

//     $barcode_filename = wp_upload_dir()["basedir"] . "/" . $product->get_id() . $file_ext;

//     $barcode_exists = false;

//     $file_headers = @get_headers($barcode_url);
//     if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
//         $barcode_exists = false;
//     }
//     else {
//         $barcode_exists = true;
//     } 

//     if($barcode_exists){
//         echo "
//             <div style='display: flex; align-items: center;justify-content: center'>
//                 <img src='$barcode_url'>
//             </div>  
//         ";
//     }

    
// }
//add_action( 'woocommerce_product_options_sku', 'render_product_barcode' );

//Called when product gets created/updated
function on_product_save($product_id){
    $product = wc_get_product($product_id);

    if($product->get_sku()){
        generateBarcode($product->get_sku(), $product_id, $product->get_title());
    }
}
add_action( 'woocommerce_new_product', 'on_product_save', 10, 1 );
add_action( 'woocommerce_update_product', 'on_product_save', 10, 1 );

//$REVERB_TOKEN = "0f603718557c595e4f814f1a6325e505e58f33d2499cbb66040ee6fec55a836d";

// echo "<pre>";
//     print_r(get_post_meta(212));
// echo "</pre>";

$REVERB_TOKEN = get_option("reverb_token");

$reverbManager = new ReverbListingManager(["token" => $REVERB_TOKEN], "sandbox");

//new DisplayNotice("Test", "success");

function publishProductToPlatforms($post_id, $post){
    global $reverbManager;

    //$reverbManager = new ReverbListingManager(["token" => $REVERB_TOKEN], "sandbox");

    if(get_post_status($post_id) == "publish" && !empty($post->ID) && in_array( $post->post_type, array( 'product') )) {
        $product = wc_get_product($post->ID);

        $reverb_response = null;

        error_log("Is updating");
        if($product->get_meta("sold") != true){
            $reverb_response = $reverbManager->updateOrCreateListing($product);
        }
        

        //new DisplayNotice("Test", "warning");


        if(isset($reverb_response["message"])){
            AdminNotice::displayInfo("<b>Reverb:</b> " . $reverb_response["message"]);
        }
    }
}
add_action("woocommerce_process_product_meta", "publishProductToPlatforms", 1000, 2);
add_action('admin_notices', [new AdminNotice(), 'displayAdminNotice']);


//--WP cron--//
//Function to run
function run_cron(){
    global $reverbManager;
    //error_log("running cron");
    checkListingsAndUpdateProducts();
    //$reverbManager->checkListingAndMarkSold()

}
//Register function as action
add_action("run_cron", "run_cron");

//Create a custom cron interval 'minute' that runs every minute
add_filter( 'cron_schedules', 'example_add_cron_interval' );
function example_add_cron_interval( $schedules ) { 
    $schedules['minute'] = array(
        'interval' => 5,
        'display'  => esc_html__( 'Every Minute' ), );
    return $schedules;
}

//Check if cron task is already scheduled, and schedule it if false
if ( ! wp_next_scheduled( 'run_cron' ) ) {
    wp_schedule_event( time(), 'minute', 'run_cron' );
}


function beforeProductDelete($post_id, $post){
    //Check if post type is product
    if(in_array( $post->post_type, array( 'product'))){
        //Get listing managers
        global $reverbManager;

        //Get the product
        $product = wc_get_product($post_id);

        //Delete product on other platforms
        $reverbManager->endOrDeleteListing($product);
    }
}
add_action("before_delete_post", "beforeProductDelete", 10, 2);



function customProductListColumns( $columns ){

    global $text_domain;
    
    //add column
    //$column = array( 'status' => __( 'Status', $text_domain ) ) ;

    //array_push($columns, $column);

    //array_splice( $columns, 6, 0, $column ) ;

    //array_splice($columns, sizeof($columns), 0, $column);

    $columns["status"] = "Status";

    return $columns;
}

add_filter( 'manage_product_posts_columns', 'customProductListColumns', 15 ) ;

function customProductListColumnsContent($column_id, $post_id){
    if($column_id == "status"){
        $status_str = "";
        $status = get_post_status($post_id);

        switch($status){
            case "publish":
                $status_str = "Published";
                break;
            case "trash":
                $status_str = "Trashed";
                break;
            case "sold":
                $status_str = "Sold";
                break;
            default:
                $status_str = "Unknown";
                break;
        }
        echo $status_str;
    }
}
add_action( 'manage_posts_custom_column','customProductListColumnsContent', 10, 2 );


function registerPostStatuses(){
    global $text_domain;
    // global $reverbManager;
    // $reverbManager->endListing(wc_get_product(261));

    register_post_status("sold",
        [
            "label" => _x("Sold", $text_domain),
            // "public" => true,
            "show_in_admin_all_list" => true,
            "show_in_admin_status_list" => true
        ]
        );
}
add_action("init", "registerPostStatuses");



function addProductViews( $views ) 
{
    // Manipulate $views

    // echo "<pre>";
    //     htmlspecialchars(print_r($views, true));
    // echo "<pre>";

    $sold_count = wp_count_posts("product")->sold;

    $views["sold"] = "<a href='edit.php?post_status=sold&post_type=product'>Sold <span class='count'>($sold_count)</span></a>";

    return $views;
}

add_filter( 'views_edit-product', 'addProductViews' );

function onProductStatusSold($new_status, $old_status, $post){
    $product = null;

    $is_product = !empty($post->ID) && in_array( $post->post_type, array( 'product') );
    if($is_product){
        $product = wc_get_product($post->ID);
    }
    if($new_status == "sold"){
        error_log("Status changed to sold, ending listing");
        global $reverbManager;
        $product = wc_get_product($post->ID);
        if($product->get_meta("sold")){
            $product->update_meta_data("sold", true);
        }else{
            $product->add_meta_data("sold", true);
        }
        

        $res = $reverbManager->endListing($product);
        error_log("response: ");
        error_log(print_r($res, true));
    }
    if($new_status != "sold" && $is_product){
        if($product->get_meta("sold")){
            $product->update_meta_data("sold", false);
        }else{
            $product->add_meta_data("sold", false);
        }
    }


    if(!empty($post->ID) && in_array( $post->post_type, array( 'product') )){
        $product->save();
    }
    
}
add_action("transition_post_status", "onProductStatusSold", 10, 3);

 