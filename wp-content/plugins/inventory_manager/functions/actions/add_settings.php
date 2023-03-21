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
    
    $ebay_oauth_url = "https://auth.ebay.com/oauth2/authorize?client_id=AllanHar-Inventor-PRD-bcd6d2723-74d77282&response_type=code&redirect_uri=Allan_Harrell-AllanHar-Invent-jxrrf&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly";

    $session_id = session_id();
    $ebay_auth_url = "https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&runame=Allan_Harrell-AllanHar-Invent-jxrrf&SessID=$session_id";

    $ebay_regen_url = 'https://wordpress-963862-3367102.cloudwaysapps.com/wp-json/inv-mgr/ebay_regenerate';

    ?>

    <a href='<?php echo $ebay_oauth_url ?>'>Link with ebay</a>
    <br>
    <a href='<?php echo $ebay_regen_url ?>'>Regen eBay token</a>
    <!-- <a href='<?php echo $ebay_auth_url ?>'>Link with ebay second</a> -->

    <?php

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