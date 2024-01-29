<?php

    //Add custom settings
    function add_settings() {

        //Option group id
        $option_group = 'settings_page_settings-general';

        //Section ids
        $reverb_section = 'reverb_settings';
        $ebay_section = 'ebay_settings';

        //--Register settings--//

        //Reverb settings
        register_setting( 'settings_page_settings-general', 'reverb_token' );
        register_setting( $option_group, 'reverb_mode' );
        register_setting( $option_group, 'reverb_shipping_profile' );

        //Ebay settings
        register_setting( 'settings_page_settings-general', 'ebay_token' );
        register_setting( 'settings_page_settings-general', 'ebay_refresh_token' );
        register_setting( $option_group, 'ebay_mode');

        //--Add fields to the settings page--//

        //Reverb section
        add_settings_section( 'reverb_settings', 'Reverb', 'render_reverb_settings_section', 'settings_page_settings-general' );
        add_settings_field( 'reverb_token', 'Reverb token', 'render_reverb_token_field', 'settings_page_settings-general', 'reverb_settings' );
        add_settings_field( 'reverb_mode', 'Reverb mode', 'render_reverb_mode_field', $option_group, $reverb_section );
        //add_settings_field('reverb_shipping_profile', 'Default shipping profile', 'reverb_shipping_profile_field', $option_group, $reverb_section);

        //Ebay section
        add_settings_section( 'ebay_settings', 'eBay', 'render_ebay_settings_section', 'settings_page_settings-general' );
        add_settings_field( 'ebay_token', 'eBay token', 'render_ebay_token_field', 'settings_page_settings-general', 'ebay_settings' );
        add_settings_field( 'ebay_refresh_token', 'eBay refresh token', 'render_ebay_refresh_token_field', 'settings_page_settings-general', 'ebay_settings' );
        add_settings_field( 'ebay_mode', 'eBay mode', 'render_ebay_mode_field', $option_group, $ebay_section );

        //Misc section
        add_settings_section( 'misc_settings', 'Misc', 'render_misc_settings_section', 'settings_page_settings-general' );    

    }

    //Add action
    add_action( 'admin_init', 'add_settings' );

    //--Template functions--//

    /**
     * Render single HTML <option> element
     * 
     * @param string $value             value of option
     * @param string $label             label of option
     * @param string $selected_value    value that is selected in the wrapping <select>
     */
    function html_option( $value, $label, $selected_value ) {
        //Render option based on passed params
        ?>
            <option value='<?php echo $value ?>' <?php html_option_maybe_selected($value, $selected_value) ?>><?php echo $label ?></option>
        <?php
    }

    /**
     * Echo 'selected' if $value matches $selected_value. Used for html_option() function above
     * 
     * @param string $value             value of option
     * @param string $selected_value    selected value of the wrapping <select>
     */
    function html_option_maybe_selected( $value, $selected_value ) {
        //If $value matches $selected_value, echo 'selected'
        if ( $value == $selected_value ){
            echo 'selected';
        }
    }

    /**
     * Render a dropdown element based on array of options
     * 
     * @param string    $dropdown_name  name for dropdown. used to specify name and id attributes for the <select> element
     * @param any[]     $options        array of options to add to the dropdown. Example:
     *                                      array(
     *                                          array(
     *                                              'value' => 'option1',
     *                                              'label' => 'Option 1'
     *                                          ),
     *                                          array(
     *                                              'value' => 'option2',
     *                                              'label' => 'Option 2'
     *                                          ),
     *                                      )
     *  
     */
    function dropdown_field( $dropdown_name, $options ) {
        //Render <select> element
        ?>
            <select
                name='<?php echo $dropdown_name ?>'
                id='<?php echo $dropdown_name ?>'
            >
                <?php
                    //Loop through passed options and render each one
                    foreach ( $options as $option ) {
                        html_option($option['value'], $option['label'], get_option($dropdown_name));
                    }
                ?>
            </select>
        <?php
    }

    //--Renderer functions--//

    //eBay

    /**
     * eBay access token field
     */
    function render_ebay_token_field() {
        ?>  
            <input type="text" name="ebay_token" id="ebay_token" value=" <?php echo get_option( 'ebay_token' ) ?> ">
        <?php
    }

    /**
     * eBay refresh token field
     */
    function render_ebay_refresh_token_field() {
        ?>  
            <input type="text" name="ebay_refresh_token" id="ebay_refresh_token" value=" <?php echo get_option( 'ebay_refresh_token' ) ?> ">
        <?php
    }

    /**
     * eBay mode field
     */
    function render_ebay_mode_field() {
        dropdown_field('ebay_mode', array(
            [
                'value' => 'live',
                'label' => 'Live'
            ],
            [
                'value' => 'sandbox',
                'label' => 'Sandbox'
            ],
        ));
    }

    //Reverb

    /**
     * Reverb API token field
     */
    function render_reverb_token_field() {
        ?>  
            <input type="text" name="reverb_token" id="reverb_token" value="<?php echo get_option( 'reverb_token' ) ?>">
        <?php
    }

    /**
     * Reverb mode field
     */
    function render_reverb_mode_field() {

        dropdown_field( 'reverb_mode', array(
            [
                'value' => 'live',
                'label' => 'Live'
            ],
            [
                'value' => 'sandbox',
                'label' => 'Sandbox'
            ],
        ));

    }

    /**
     * Reverb default shipping profile field (WIP)
     */
    function render_reverb_shipping_profile_field() {
        global $listing_managers;

        $shipping_profiles = $listing_managers->getManager('reverb')->get_shipping_profiles();

        //pr($shipping_profiles);

        $options = [];

        foreach ( $shipping_profiles as $profile ) {
            array_push($options, array(
                'value' => $profile['id'],
                'label' => $profile['name']
            ));
        }

        //pr($options);

        dropdown_field('reverb_shipping_profile', $options);
    }

    //Link status

    /**
     * Reverb link status
     */
    function render_reverb_link_status( $username ) {
        ?>
            <b>Reverb status:</b>
            <?php
                if ( $username ) {
                    echo "Linked with $username";
                } else {
                    echo 'Not linked';
                }
            ?>
        <?php
    }

    /**
     * eBay link status
     */
    function render_ebay_link_status( $username ) {
        ?>
            <b>Ebay status: </b>
            <?php
                if ( $username ) {
                    echo "Linked with $username";
                } else {
                    echo 'Not linked';
                }
            ?>
        <?php
    }


    //Sections

    /**
     * Misc. section
     */
    function render_misc_settings_section() {

        $clear_log_endpoint = $_SERVER['SERVER_NAME'] . '/wp-json/inv-mgr/clear-activity-log';

        ?>
            <button type='button' onclick='clearActivityLog()' class='button'>Clear activity log</button>
            <br>
            <span id='clear-activity-log-msg' style='display: none'>Cleared activity log!</span>

            <script>
                function clearActivityLog() {
                    fetch('//<?php echo $clear_log_endpoint ?>');
                    const msg_element = document.getElementById('clear-activity-log-msg');

                    msg_element.style.display = 'block';

                    setTimeout(() => {
                        msg_element.style.display = 'none';
                    }, 5000)
                }
            </script>
        <?php
    }

    /**
     * Reverb section
     */
    function render_reverb_settings_section () {
        global $listing_managers;

        $reverb_manager = $listing_managers->getManager('reverb');

        $reverb_username = null;
        $reverb_user = null;

        if ( $reverb_manager ) {
            $reverb_user = $reverb_manager->get_user();
            
        }
        // echo '<pre>';
        // print_r( $reverb_user );
        // echo '</pre>';

        if( isset( $reverb_user['email'] ) ) {
            $reverb_username = $reverb_user['email'];
        }

        render_reverb_link_status( $reverb_username );
    }

    /**
     * eBay section
     */
    function render_ebay_settings_section(){

        global $listing_managers;

        $ebay_oauth_url = "https://auth.ebay.com/oauth2/authorize?client_id=AllanHar-Inventor-PRD-bcd6d2723-74d77282&response_type=code&redirect_uri=Allan_Harrell-AllanHar-Invent-jxrrf&scope=https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly";
        $ebay_manager = $listing_managers->getManager('ebay');
        $ebay_user = null;
        if ( $ebay_manager ) {
            $ebay_user = $ebay_manager->get_user();
        }
        $ebay_username = null;
        if( isset($ebay_user['username'] ) ) {
            $ebay_username = $ebay_user['username'];
        }

        

        ?>

            <!-- <span>Link status:</span> -->
            <a href='<?php echo $ebay_oauth_url ?>'>Link with ebay</a>
            <br>
            <?php render_ebay_link_status( $ebay_username ); ?>

        <?php
        
    }