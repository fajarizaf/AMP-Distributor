<?php

/**
 * Go generate as Array from 
 * 
 * @param Array $string Obviously should be an Array, Otherwise, it will generate false.
 * @return Array This function will generate comman string to Array
 */
function wpt_explode_string_to_array($string) {
    if ($string && is_string($string)) {
        $string = rtrim($string, ', ');
        $string = explode(',', $string);
    } else {
        $string = false;
    }
    return $string;
}

/**
 * Generate each row data for product table. This function will only use for once place.
 * I mean: in shortcode.php file normally.
 * But if anybody want to use any others where, you have to know about $wpt_table_column_keywords and $wpt_each_row
 * both should be Array, Although I didn't used condition for $wpt_each_row Array to this function. 
 * So used: based on your own risk.
 * 
 * @param Array $wpt_table_column_keywords
 * @param Array $wpt_each_row
 * @return String_Variable
 */
function wpt_generate_each_row_data($wpt_table_column_keywords = false, $wpt_each_row = false) {
    $final_row_data = false;
    if (is_array($wpt_table_column_keywords) && count($wpt_table_column_keywords) > 0) {
        
        foreach ($wpt_table_column_keywords as $each_keyword) {
            $final_row_data .= ( isset($wpt_each_row[$each_keyword]) ? $wpt_each_row[$each_keyword] : false );
        }
    }
    return $final_row_data;
}

/**Generaed a Array for $wpt_permitted_td 
 * We will use this array to confirm display Table body's TD inside of Table
 * 
 * @since 1.0.4
 * @date 27/04/2018
 * @param Array $wpt_table_column_keywords
 * @return Array/False
 */
function wpt_define_permitted_td_array( $wpt_table_column_keywords = false ){
    $wpt_permitted_td = false;
    if( $wpt_table_column_keywords && is_array( $wpt_table_column_keywords ) && count($wpt_table_column_keywords) > 0 ){
        foreach($wpt_table_column_keywords as $each_keyword){
            $wpt_permitted_td[$each_keyword] = true;
        }
    }
    return $wpt_permitted_td;
}

/**
 * Generating <options>VAriation Atribute</option> for Product Variation
 * CAn be removed later.
 * 
 * @param type $current_single_attribute
 * @return string
 */
function wpt_array_to_option_atrribute( $current_single_attribute = false ){
    $html = '<option value>None</option>';
    if( is_array( $current_single_attribute ) && count( $current_single_attribute ) ){
        foreach( $current_single_attribute as $wpt_pr_attributes ){
        $html .= "<option value='{$wpt_pr_attributes}'>" . ucwords($wpt_pr_attributes) . "</option>";
        }
    }
    return $html;
}

/**
 * For Variable product, Variation's attribute will generate to select tag
 * 
 * @param Array $attributes
 * @param Int $product_id
 * @param Int $temp_number
 * @return string HTML Select tag will generate from Attribute
 */
function wpt_variations_attribute_to_select( $attributes , $product_id = false, $temp_number = false){
    $html = false;
    
    $html .= "<div class='wpt_varition_section' data-product_id='{$product_id}'  data-temp_number='{$temp_number}'>";
    //var_dump($total_attributes);
    foreach( $attributes as $attribute_key_name=>$options ){
        
        $label = wc_attribute_label( $attribute_key_name );
        $attribute_name = wc_variation_attribute_name( $attribute_key_name );
        
        $html .= "<select data-product_id='{$product_id}' data-attribute_name='{$attribute_name}' placeholder='{$label}'>";
        $html .= "<option value='0'>". __('Choose','wpt_table'). " " . $label . "</option>";
        foreach( $options as $option ){
            $html .= "<option value='{$option}'>" . ucwords($option) . "</option>";
        }
        $html .= "</select>";
        
    }
    $html .= "<div class='wpt_message wpt_message_{$product_id}'></div>";
    $html .= '</div>';
    return $html;
}

/**
 * Actually Its very simple function. If founded Variable - A Array, 
 * Than We want to return a class, Otherwise nothing.
 * V1.0.4 currently not used. Can be used later again.
 * 
 * @deprecated since 1.0.4 1.0.4_10_5.5.2018
 * @param Array $target_array
 * @param String $return_class
 * @return String
 */
function wpt_is_array_class($target_array = false, $return_class = ''){
    if( is_array( $target_array ) && count( $target_array ) > 0 ){
        return $return_class;
    }
}

/**
 * Getting unit amount with unint sign. Suppose: Kg, inc, cm etc
 * woocommerce has default wp_options for weight,height etc's unit.
 * Example: for weight, woocommerce_weight_unit
 * 
 * @param string $target_unit Such as: weight, height, lenght, width
 * @param int $value Can be any number. It also can be floating point number. Normally decimal
 * @return string If get unit and value is gater than o, than it will generate string, otheriwse false
 */
function wpt_get_value_with_woocommerce_unit( $target_unit, $value ){
    $get_unit = get_option( 'woocommerce_' . $target_unit . '_unit' );
    //var_dump($get_unit);
    return ( is_numeric( $value ) && $value > 0 ? $value . ' ' . $get_unit : false );
}