<?php

require_once __DIR__ . '/../endpoints/ebay_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_confirm_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_decline_endpoint.php';
require_once __DIR__ . '/../endpoints/ebay_regenerate_token_endpoint.php';

function register_endpoints() {

    register_rest_route("inv-mgr", "/ebay", array (
        'methods' => 'GET',
        'callback' => 'ebay_endpoint'
    ));

    register_rest_route("inv-mgr", "/ebay_confirm", array (
        'methods' => 'GET',
        'callback' => 'ebay_confirm_endpoint'
    ));

    register_rest_route("inv-mgr", "/ebay_decline", array (
        'methods' => 'GET',
        'callback' => 'ebay_decline_endpoint'
    ));

    register_rest_route("inv-mgr", "/ebay_regenerate", array (
        'methods' => 'GET',
        'callback' => 'ebay_regenerate_token_endpoint'
    ));

}

add_action('rest_api_init', 'register_endpoints');