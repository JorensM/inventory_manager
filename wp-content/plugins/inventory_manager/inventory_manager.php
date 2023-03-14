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

//Functions
// require_once("functions/generateBarcode.php");
// require_once("functions/reverbCreateListing.php");
// require_once("functions/reverbUpdateListing.php");
// require_once("functions/checkListingsAndUpdateProducts.php");

//Actions
//Require all php files in the functions/actions folder
$actions_files = glob(__DIR__.'/functions/actions/*.php');
//print_r($actions_files);
foreach ($actions_files as $action_file) {
    //echo "Requiring";
    require_once($action_file);   
}



$text_domain = "inv-mgr";


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

//$REVERB_TOKEN = get_option("reverb_token");

//$reverbManager = new ReverbListingManager(["token" => $REVERB_TOKEN], "sandbox");


 