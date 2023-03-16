<?php

function render_ebay_token_field(){
    ?>  
        <input type="text" name="ebay_token" id="ebay_token" value=" <?php echo get_option( 'ebay_token' ) ?> ">
    <?php
}

function render_reverb_token_field(){
    ?>  
        <input type="text" name="reverb_token" id="reverb_token" value="<?php echo get_option( 'reverb_token' ) ?>">
    <?php
}

function render_settings_section(){
    //$ebay_oauth_url = "https://auth.sandbox.ebay.com/oauth2/authorize?client_id=JorensMe-Test-SBX-7ae27b979-8fb9e8f6&response_type=code&redirect_uri=Jorens_Merenjan-JorensMe-Test-S-vkhob&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/buy.order.readonly https://api.ebay.com/oauth/api_scope/buy.guest.order https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.marketplace.insights.readonly https://api.ebay.com/oauth/api_scope/commerce.catalog.readonly https://api.ebay.com/oauth/api_scope/buy.shopping.cart https://api.ebay.com/oauth/api_scope/buy.offer.auction https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.email.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.phone.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.address.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.name.readonly https://api.ebay.com/oauth/api_scope/commerce.identity.status.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.item.draft https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/sell.item https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly";

    //echo "abc";
}

//Add settings
function add_settings(){

    register_setting("settings_page_settings-general", "reverb_token");
    register_setting( 'settings_page_settings-general', 'ebay_token' );

    add_settings_section("custom", "Settings", "render_settings_section", "settings_page_settings-general");
    add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    add_settings_field("ebay_token", "eBay token", "render_ebay_token_field", "settings_page_settings-general", "custom");
    //add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    

}
add_action("admin_init", "add_settings");