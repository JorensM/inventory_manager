<?php

//Requires
require_once 'settings_pages.php' ;

// Add menu items to sidenav
function add_menu_items(){

    //Settings page
    add_submenu_page("options-general.php", "General", "General", "manage_options", "settings-general", "settings_general_page");

    //Guide pages
    add_menu_page('inv-mgr-guide', 'Guide', 'manage_options', 'inv-mgr-guide', 'inv_mgr_user_guide', 'dashicons-info-outline', 200);
    add_submenu_page('inv-mgr-guide', 'Developer guide', 'Developer guide', 'manage_options', 'developer-guide', 'inv_mgr_dev_guide');
}

//Add action
add_action("admin_menu", "add_menu_items");