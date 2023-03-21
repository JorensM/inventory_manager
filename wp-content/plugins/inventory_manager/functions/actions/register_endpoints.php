<?php

require_once __DIR__ . '/../endpoints/ebay_endpoint.php';

function register_endpoints() {

    register_rest_route("inv-mgr", "/ebay", array (
        'methods' => 'GET',
        'callback' => 'ebay_endpoint'
    ));

}

add_action('rest_api_init', 'register_endpoints');