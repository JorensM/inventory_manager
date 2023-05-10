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
//Constants
require_once 'src/const.php' ;

//Actions
//Require all php files in the functions/actions folder
$actions_files = glob( __DIR__ . '/src/functions/actions/*.php' );
foreach ( $actions_files as $action_file ) {
    require_once( $action_file );   
}