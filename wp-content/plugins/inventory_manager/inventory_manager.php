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

$actions_dir = "functions/actions/";
//Actions
require_once("functions/actions/addSettings.php");
require_once($actions_dir . "");



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

 