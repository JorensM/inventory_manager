<?php

    function ebay_regenerate_token_endpoint( $data ) {
        $curl = curl_init("https://api.ebay.com/identity/v1/oauth2/token");

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');

        $auth = base64_encode('AllanHar-Inventor-PRD-bcd6d2723-74d77282:PRD-cd6d27234717-ecac-4c64-8415-728c');

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $auth"
        );

        $token = get_option('ebay_token');

        $data = array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $token,
            'scope' => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly'
        );

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $res = curl_exec($curl);

        print_r($res);
    }