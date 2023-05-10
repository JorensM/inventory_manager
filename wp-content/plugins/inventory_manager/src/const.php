<?php

    $ECHO_PAGE_ID = false; //Whether to show page id on page (for debug)

    $TEXT_DOMAIN = 'inv-mgr';

    //Product's custom field ids
    $CUSTOM_FIELD_IDS = [
        'brand_info',
        'model_info',
        'year_field',
        'handedness_field',
        'color_field',
        'country_field',
        'body_type_field',
        'string_configuration_field',
        'fretboard_material_field',
        'neck_material_field',
        'condition_field',
        'reverb_shipping_profile',
        'ebay_shipping_profile',
        'product_location',
        'product_serial_number'
    ];

    //Product's custom textarea field ids
    $CUSTOM_TEXTAREA_FIELD_IDS = [
        'notes_field'
    ];

    //Product's Custom checkbox field ids
    $CUSTOM_CB_FIELD_IDS = [
        'reverb_draft'
    ];

    $EBAY_SCOPES = 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly';

        