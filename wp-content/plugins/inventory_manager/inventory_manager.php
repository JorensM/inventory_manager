<?php
/*
    * Plugin Name:       Inventory Manager Core
    * Description:       Core plugin for the inventory manager website
    * Version:           1.0.0
    * Author:            JorensM
    * Author URI:        github.com/JorensM
    * Text Domain:       inv-mgr
*/

ini_set( 'display_errors' , 1 );
ini_set( 'display_startup_errors' , 1 );
error_reporting(E_ALL);

//--Requires--//

//Classes
require_once 'src/classes/class-google-sheets-products.php';

//Functions
require_once 'src/functions/curl_request.php';

//Constants
require_once 'src/const.php' ;



function check_dependencies() {
    if(!is_plugin_active("woocommerce/woocommerce.php")) {
        Admin_Notice::displayError('Inventory Manager requires WooCommerce to work');
        deactivate_plugins('inventory_manager/inventory_manager.php');
    }
}

function on_admin_init() {
    check_dependencies();
}

// Redirect to admin if on a customer facing page like site homepage
function maybe_redirect() {
    if(!is_admin()) {
        wp_redirect( admin_url() );
        exit();
    }
}

add_action( 'admin_init', 'on_admin_init' );
add_action( 'init', 'maybe_redirect');




//Actions
//Require all php files in the functions/actions folder
$actions_files = glob( __DIR__ . '/src/functions/actions/*.php' );
foreach ( $actions_files as $action_file ) {
    require_once( $action_file );   
}


// $google_api_key = 'AIzaSyDE6vhvjcgwNYZUnN8kSVJ_DJwWm8qRDb4';
// $spreadsheet_id = '10j-z9e95OxpKApmGaJON_V7WrUEag02UuKwL3K1G15o';
// $sheet_name = 'Sheet1';


// $google_sheets = new Google_Sheets_Products();

// $google_sheets->set_spreadsheet( $spreadsheet_id );
// $google_sheets->set_sheet( $sheet_name );
//$res = $google_sheets->add_product( new WC_Product(132) );//$google_sheets->generate_access_token();//$google_sheets->add_product();

// $res = $google_sheets->get_row_number_by_product_id( 132 );

// echo 'row number: ';
// echo '<br>';
// echo '<pre>';
// print_r($res);
// echo '</pre>';
// echo 'row count: ';
// echo '<br>';
// echo '<pre>';
// print_r($res);
// echo '</pre>';

