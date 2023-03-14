<?php

// Add menu items
function add_menu_items(){
    add_submenu_page("options-general.php", "General", "General", "manage_options", "settings-general", "settingsGeneralPage");
}
add_action("admin_menu", "add_menu_items");