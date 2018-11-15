<?php
/**
 * CSS or Style file add for FrontEnd Section. 
 * 
 * @since 1.0.0
 */
function wpt_style_js_adding(){
    //Custom CSS Style for Woo Product Table's Table (Universal-for all table) and (template-for defien-table)
    wp_enqueue_style( 'wpt-universal', WOO_Product_Table::getPath('BASE_URL') . 'css/universal.css', __FILE__, WOO_Product_Table::getVersion() );
    wp_enqueue_style( 'wpt-template-table', WOO_Product_Table::getPath('BASE_URL') . 'css/template.css', __FILE__, WOO_Product_Table::getVersion() );
    
    /**
     * DataTable CSS file including. 
     * Not used, Because its Inactivated in this version
     * 
     * @since 1.0.0 -15
     */    
    //wp_enqueue_style( 'datatables', WOO_Product_Table::getPath('BASE_URL') . 'css/jquery.dataTables.min.css', __FILE__, '1.8.2' );
    
    //jQuery file including. jQuery is a already registerd to WordPress
    wp_enqueue_script('jquery');
    
    ///custom JavaScript for Woo Product Table pro plugin
    wp_enqueue_script( 'wpt-custom-js', WOO_Product_Table::getPath('BASE_URL') . 'js/custom.js', __FILE__, WOO_Product_Table::getVersion(), true );
    
    /**
     * DataTable javascript file including. 
     * Not used, Because its Inactivated in this version
     * 
     * @since 1.0.0 -15
     */
    //wp_enqueue_script( 'datatables', WOO_Product_Table::getPath('BASE_URL') . 'js/jquery.dataTables.js', __FILE__, '1.8.2', true );

    /**
     * rtResponsive jQuery plugin including here.
     * Currently Disable
     * @since 1.5
     */
    //wp_enqueue_style( 'rtResponsiveTables', WOO_Product_Table::getPath('BASE_URL') . 'css/jquery.rtResponsiveTables.min.css', __FILE__, WOO_Product_Table::getVersion() );
    //wp_enqueue_script( 'rtResponsiveTables', WOO_Product_Table::getPath('BASE_URL') . 'js/jquery.rtResponsiveTables.min.js', __FILE__, '1.8.2', true );

    
}
add_action('wp_enqueue_scripts','wpt_style_js_adding',99);

/**
 * Removed from admin panel at @since 1.5
 * MS and CSS file also added to Dashboard/wp-admin
 * Because, We want to show preview as like Live Preview
 * 
 * @since 1.0.0
 */
//add_action('admin_enqueue_scripts','wpt_style_js_adding',99);