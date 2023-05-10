<?php

require_once __DIR__ . '/../endpoints/ebay_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_confirm_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_decline_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_regenerate_token_endpoint.php';


/**
 * Registers endpoints for the WP REST api. As it currently stands, all endpoints
 * exist under the 'inv-mgr' namespace, and can be accessed from 
 * <SITE_URL>/inv-mgr/<ENDPOINT>
 * 
 * Here is the list of all endpoints:
 * 
 * clear-activity-log   (GET)
 *      Clears the activity log that is in the dashboard
 * ebay                 (GET, POST)
 *      This endpoint is used by eBay to verify account deletion.
 *      It is not to be used.
 * ebay_confirm         (GET)
 *      When linking an eBay account, the user gets directed to this page
 *      if the user confirmed linking. This endpoint is important because it also
 *      generates tokens used by the app. 
 * ebay_decline         (GET)
 *      When linking an eBay account, the user gets directed to this page
 *      if the user declined linking
 * ebay_regenerate      (GET)
 *      DEPRECATED. This endpoint regenerates the eBay user token
 * 
 * 
 * @return void
 */
function register_endpoints() {

    $routes = [
        [
            'namespace' => 'inv-mgr',
            'route' => '/clear-activity-log',
            'args' => [
                'methods' => 'GET',
                'callback' => 'clear_activity_log'
            ]
        ],
        [
            'namespace' => 'inv-mgr',
            'route' => '/ebay',
            'args' => [
                'methods' => 'GET, POST',
                'callback' => 'ebay_endpoint'
            ]
        ],
        [
            'namespace' => 'inv-mgr',
            'route' => '/ebay_confirm',
            'args' => [
                'methods' => 'GET',
                'callback' => 'ebay_confirm_endpoint'
            ]
        ],
        [
            'namespace' => 'inv-mgr',
            'route' => '/ebay_decline',
            'args' => [
                'methods' => 'GET',
                'callback' => 'ebay_decline_endpoint'
            ]
        ],
        [
            'namespace' => 'inv-mgr',
            'route' => '/ebay_regenerate',
            'args' => [
                'methods' => 'GET',
                'callback' => 'ebay_regenerate_token_endpoint'
            ]
        ],
    ];

    foreach ( $routes as $route ) {
        $namespace = $route['namespace'];
        $route_name = $route['route'];
        $args = $route['args'];
        $args['permission_callback'] = '__return_true';

        register_rest_route($namespace, $route_name, $args);
    }

}

add_action('rest_api_init', 'register_endpoints');