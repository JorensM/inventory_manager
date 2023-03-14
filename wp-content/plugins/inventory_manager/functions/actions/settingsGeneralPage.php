<?php

// General settings page
function settingsGeneralPage(){
    ?>
    <form method="POST" action="options.php">
        <?php 
            settings_fields( 'settings_page_settings-general' );	//pass slug name of page, also referred to in Settings API as option group name
            do_settings_sections( 'settings_page_settings-general' ); 	//pass slug name of page
            submit_button();
        ?>
    </form>
    <?php
}