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

    foreach($to_remove as $item){
        remove_menu_page($item);
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
        </style>
  <?php
}
add_action('admin_head', 'woo_remove_fields');

//Add custom Javascript
function custom_js() {
    $page_id = get_current_screen()->id;

    //Custom style for users page
    if($page_id === "users"){
        ?>
            <script>
                console.log("test");
                document.getElementById("posts").innerHTML = "Products";
            </script>
        <?php
    }
    
        
  
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

    add_role( 'regular', 'Regular', array( 'read' => true, 'level_0' => true ) );

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