<?php

function render_reverb_token_field(){
    ?>  
        <input type="text" name="reverb_token" id="reverb_token" value="<?php echo get_option("reverb_token") ?>">
    <?php
}

function render_settings_section(){
    //echo "abc";
}

//Add settings
function add_settings(){

    register_setting("settings_page_settings-general", "reverb_token");

    add_settings_section("custom", "Settings", "render_settings_section", "settings_page_settings-general");
    add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    //add_settings_field("reverb_token", "Reverb token", "render_reverb_token_field", "settings_page_settings-general", "custom");
    

}
add_action("admin_init", "add_settings");