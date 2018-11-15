<?php
/**
 * CSS or Style file add for Admin Section. 
 * 
 * @since 1.0.0
 * @update 1.0.3
 */
function wpt_style_js_adding_admin(){
    
    wp_enqueue_style('wpt-admin-css', WOO_Product_Table::getPath('BASE_URL') . 'css/admin.css', __FILE__, '1.0.0');
    
    /**
     * Select2 CSS file including. 
     * 
     * @since 1.0.3
     */    
    wp_enqueue_style( 'select2', WOO_Product_Table::getPath('BASE_URL') . 'css/select2.min.css', __FILE__, '1.8.2' );
    
    //jQuery file including. jQuery is a already registerd to WordPress
    wp_enqueue_script('jquery');
    
    //Includeing jQuery UI Core
    wp_enqueue_script('jquery-ui-sortable');
    
    /**
     * Select2 jQuery Plugin file including. 
     * Here added min version. But also available regular version in same directory
     * 
     * @since 1.0.3
     */
    wp_enqueue_script( 'select2', WOO_Product_Table::getPath('BASE_URL') . 'js/select2.min.js', __FILE__, '4.0.5', true );
    
    wp_enqueue_script('wpt-admin-js', WOO_Product_Table::getPath('BASE_URL') . 'js/admin.js', __FILE__, '1.0.0', true);
}
add_action('admin_enqueue_scripts','wpt_style_js_adding_admin',99);