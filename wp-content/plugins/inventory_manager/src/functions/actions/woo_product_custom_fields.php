<?php

    //Add custom fields to WooCommerce product editor
    function woo_product_custom_fields(){
        global $woocommerce, $post, $text_domain;
        
        //Render fields
        echo '<div class="options_group">';
            //Brand info
            woocommerce_wp_text_input(
                array(
                    'id'          => 'brand_info',
                    'label'       => __( 'Brand Info', $text_domain ),
                    'placeholder' => '',
                    'desc_tip'    => false,
                    'custom_attributes' => array( 'required' => 'required' ),
                    // 'description' => __( "Enter Brand info", $text_domain )
                )
            );

            //Model info
            woocommerce_wp_text_input(
                array(
                    'id'          => 'model_info',
                    'label'       => __( 'Model info', $text_domain ),
                    'placeholder' => '',
                    'desc_tip'    => false,
                    'custom_attributes' => array( 'required' => 'required' ),
                    // 'description' => __( "Enter Model info", $text_domain )
                )
            );

            //Year
            // echo "<pre>";
            //     print_r(get_post_meta($post->ID));
            // echo "</pre>";
            woocommerce_wp_text_input(
                array(
                    'id'                => 'year_field',
                    'label'             => __( 'Approx./exact year', $text_domain ),
                    'placeholder'       => '',
                    // 'value' => "2023",
                    //'desc_tip'    		=> false,
                    //'description'       => __( "Here's some really helpful text that appears next to the field.", 'woocommerce' ),
                    'type'              => 'number',
                    'custom_attributes' => array(
                            'step' 	=> '1',
                            'min'	=> '0'
                        )
                )
            );

            //Color
            woocommerce_wp_text_input(
                array(
                    'id'                => 'color_field',
                    'label'             => __( 'Finish color', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //Country
            woocommerce_wp_text_input(
                array(
                    'id'                => 'country_field',
                    'label'             => __( 'Country of manufacture', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //Handedness
            woocommerce_wp_select(
                array(
                    'id'      => 'handedness_field',
                    'label'   => __( 'Handedness', $text_domain ),
                    'options' => array(
                        'right'   => __( 'Right', $text_domain ),
                        'left'   => __( 'Left', $text_domain ),
                    )
                )
            );

            //Body type
            woocommerce_wp_text_input(
                array(
                    'id'                => 'body_type_field',
                    'label'             => __( 'Body type', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //String configuration
            woocommerce_wp_text_input(
                array(
                    'id'                => 'string_configuration_field',
                    'label'             => __( 'Number of strings/string configuration', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //Fretboard material
            woocommerce_wp_text_input(
                array(
                    'id'                => 'fretboard_material_field',
                    'label'             => __( 'Fretboard material', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //Neck material
            woocommerce_wp_text_input(
                array(
                    'id'                => 'neck_material_field',
                    'label'             => __( 'Neck Material', $text_domain ),
                    'placeholder'       => '',
                )
            );

            //Condition
            woocommerce_wp_select(
                array(
                    'id'      => 'condition_field',
                    'label'   => __( 'Condition', $text_domain ),
                    'options' => array(
                        'used'   => __( 'Used', $text_domain ),
                        // 'new'   => __( 'New', $text_domain ),
                        'non-functioning'   => __( 'Non-functioning', $text_domain ),
                    )
                )
            );

            //Notes
            woocommerce_wp_textarea_input(
                array(
                    'id'          => 'notes_field',
                    'label'       => __( 'Notes ', $text_domain ),
                    'placeholder' => '',
                    "style" => "height: 140px;",
                    // 'rows' => 6,
                    // 'cols' => 4
                )
            );

            woocommerce_wp_checkbox(
                array(
                    'id'            => 'reverb_draft',
                    'wrapper_class' => 'show_if_simple',
                    'label'         => __('<b>Reverb:</b> save as draft', $text_domain ),
                    'desc_tip'      => true,
                    'description'   => __( 'If this is checked, listing will be created as draft on Reverb, instead of being published', $text_domain )
                )
            );

        echo '</div>';
    }
    add_action( 'woocommerce_product_options_general_product_data', 'woo_product_custom_fields' );

    function woo_product_shipping_custom_fields () {
        global $text_domain, $listing_managers;

        
        echo '<div class="options_group">';
            //--Reverb shipping profile--//
            $reverb_shipping_profile_options = [];
            $reverb_shipping_profiles = $listing_managers->getManager('reverb')->get_shipping_profiles();
            foreach ( $reverb_shipping_profiles as $profile ) {
                $reverb_shipping_profile_options[ $profile['id'] ] = __( $profile['name'], $text_domain );
            }
            woocommerce_wp_select( array (
                'id'      => 'reverb_shipping_profile',
                'label'   => __('<b>Reverb</b> shipping profile', $text_domain),
                'options' => $reverb_shipping_profile_options
            ) );
            
            //--eBay shipping profile--//
            $ebay_shipping_profile_options = [];
            $ebay_shipping_profiles = $listing_managers->getManager('ebay')->get_shipping_profiles();

            foreach ( $ebay_shipping_profiles as $profile ) {
                $ebay_shipping_profile_options[ $profile['fulfillmentPolicyId'] ] = $profile['name'];
            }
            woocommerce_wp_select( array (
                'id'      => 'ebay_shipping_profile',
                'label'   => __('<b>eBay</b> shipping profile', $text_domain),
                'options' => $ebay_shipping_profile_options
            ) );
        echo '</div>';
    }
    add_action( 'woocommerce_product_options_shipping', 'woo_product_shipping_custom_fields');

    function woo_product_inventory_custom_fields() {
        global $text_domain;
        //Location
        woocommerce_wp_text_input(
            array(
                'id'                => 'product_location',
                'label'             => __( 'Location', $text_domain ),
                'placeholder'       => '',
            )
        );

        //Neck material
        woocommerce_wp_text_input(
            array(
                'id'                => 'product_serial_number',
                'label'             => __( 'Serial number', $text_domain ),
                'placeholder'       => '',
            )
        );
    }
    add_action( 'woocommerce_product_options_sku', 'woo_product_inventory_custom_fields' );

    //Save custom fields
    function woo_save_product_fields( $post_id ){

        global $custom_field_ids, $custom_cb_field_ids;

        $field_ids = $custom_field_ids;

        $textarea_field_ids = [
            "notes_field"
        ];

        $product = wc_get_product($post_id);

        foreach($field_ids as $field_id){
            $field_data = esc_attr($_POST[$field_id]);
            update_post_meta( $post_id, $field_id, $field_data );
            if($product->get_meta($field_id)){
                $product->update_meta_data($field_id, $field_data);
            }else{
                $product->add_meta_data($field_id, $field_data);
            }
        }

        foreach($textarea_field_ids as $field_id){
            $field_data = esc_html($_POST[$field_id]);
            update_post_meta( $post_id, $field_id, $field_data );
            if($product->get_meta($field_id)){
                $product->update_meta_data($field_id, $field_data);
            }else{
                $product->add_meta_data($field_id, $field_data);
            }
            
        }

        foreach($custom_cb_field_ids as $field_id){
            $field_data = isset( $_POST['reverb_draft'] ) ? 'yes' : 'no';
            if($product->get_meta($field_id)){
                $product->update_meta_data($field_id, $field_data);
            }else{
                $product->add_meta_data($field_id, $field_data);
            }
        }

        //echo $_POST["reverb_draft"];

        // if(!$product->get_meta("is_sold")){
        //     $product->add_meta_data("is_sold", "no");
        // }

        $product->save();
        

    }
    add_action( 'woocommerce_process_product_meta', 'woo_save_product_fields' );