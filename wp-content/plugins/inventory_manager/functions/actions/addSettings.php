<?php

//Add settings
function add_settings(){

    register_setting("settings_page_settings-general", "reverb_token");

    add_settings_section("custom", "Settings", "render_settings_section", "settings_page_settings-general");
    add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    //add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    

}
add_action("admin_init", "add_settings");