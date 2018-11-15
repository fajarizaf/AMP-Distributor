<?php
/**
 * This file is Activated now
 * Will ad in Future. 
 * Just keeping information, I have kept this file
 * 
 * @since 1.0.0
 * @updated 1.0.4 Currently Inactive this file.
 */

/**
 * Function for Live Table Update for Configure page
 * 
 * @since 1.0.0
 * @deprecated since 1.0.4 1.0.4_12_5.5.2018
 */
function wpt_live_table_setting(){
    if( isset( $_POST['action'] ) == 'wpt_table_preview' ){
        $atts = $_POST['info'];
        //var_dump($atts);
        //echo do_shortcode("[Product_Table title= 'This is Text' classes= '' table_class= '' product_cat_ids= '' product_cat_slugs= '' short= 'desc' template= 'blue' column= 'price,quantity,action,']");
        echo wpt_shortcode_generator( $atts );
    }else{
        echo '<p style="color: #d00;">Critical Error</p>';
    }
    //echo wpt_shortcode_generator();
    die();
}

//add_action('wp_ajax_wpt_table_preview','wpt_live_table_setting');
//add_action('wp_ajax_nopriv_wpt_table_preview','wpt_live_table_setting');

