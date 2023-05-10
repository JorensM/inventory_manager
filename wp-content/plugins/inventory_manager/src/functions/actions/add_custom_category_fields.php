<?php

/**
 * Add custom fields to category form
 */

//--Template functions--//

/**
 * Regular text field
 * 
 * @param string $title     title of field
 * @param string $id        id of input element and meta value
 * @param string $value     value of field
 * 
 * @return void
 */
function input_field( $title, $id, $value = null ) {
    //echo 'TEST: ' . print_r($value, true);
    if( ! $value ) {
        $value = '';
    }
    ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="wh_meta_title"><?php _e($title, 'inv-mgr'); ?></label></th>
            <td>
                <input type="text" name='<?php echo $id?>' id='<?php echo $id?>' value="<?php echo $value ?>">
            </td>
        </tr>
    <?php
}

/**
 * Small text field
 * 
 * @param string $title     title of field
 * @param string $id        id of input element and meta value
 * @param string $value     value of field
 * 
 * @return void
 */
function input_field_small( $label, $id, $value = null ) {
    global $text_domain;

    if( ! $value ) {
        $value = '';
    }
    ?>
        <div class="form-field">
            <label for='<?php echo $id ?>'><?php _e($label, $text_domain); ?></label>
            <input type="text" name='<?php echo $id ?>' id='<?php echo $id; ?>'>
        </div>
    <?php
}

/**
 * Render custom fields for category
 * 
 * @param string $term_id id of category 
 * 
 * @return void
 */
function render_category_custom_fields(  $term_id = null) {
    ?>
        <input type='text' name='ebay_category_id' id='ebay_category_id' placeholder='eBay Category ID'></input>
        <div style='height: 16px'></div>
        <input type='text' name='reverb_category_id' id='reverb_category_id' placeholder='Reverb Category ID'></input>
    <?php
}

/**
 * Render fields for 'add' form
 */
function render_category_add_fields() {
    render_category_custom_fields();
}

/**
 * Render fields for 'edit' form
 */
function render_category_edit_fields($term) {
    $term_id = $term->term_id;
    input_field( "eBay category ID", 'ebay_category_id', get_term_meta( $term_id, 'ebay_category_id', true ) );
    input_field( "Reverb category ID", 'reverb_category_id', get_term_meta( $term_id, 'reverb_category_id', true ) );
}

//Add actions for rendering the fields
add_action('product_cat_add_form_fields', 'render_category_add_fields');
add_action('product_cat_edit_form_fields', 'render_category_edit_fields');

//Save custom fields's data
function save_cat_custom_fields_data($term_id) {

    $ebay_id = filter_input(INPUT_POST, 'ebay_category_id');
    $reverb_id = filter_input(INPUT_POST, 'reverb_category_id');

    update_term_meta($term_id, 'ebay_category_id', $ebay_id);
    update_term_meta($term_id, 'reverb_category_id', $reverb_id);
}

//Add actions for when data gets saved
add_action('edited_product_cat', 'save_cat_custom_fields_data', 10, 1);
add_action('create_product_cat', 'save_cat_custom_fields_data', 10, 1);