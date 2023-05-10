<?php

//Requires
require_once __DIR__ . "/../check_listings_and_update_products.php";

//--WP cron--//
//Function to run
function run_cron(){
    check_listings_and_update_products();

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