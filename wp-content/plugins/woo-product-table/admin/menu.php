<?php
/**
 * Set Menu for WPT (Woo Product Table) Plugin
 * 
 * @since 1.0
 * 
 * @package Woo Product Table
 */
function wpt_admin_menu() {
    add_menu_page('WOO Product Table', 'WPT Table', 'edit_theme_options', 'woo-product-table', 'wpt_configure_page', 'dashicons-list-view');
    //add_submenu_page('woo-product-table', 'WOO Product FAQ', 'FAQ & Tutorial', 'edit_theme_options', 'woo-product-table-faq', 'wpt_faq_page');

    /************** Old Medu backup *************
    add_menu_page('WOO Product Table', 'Product Table', 'edit_theme_options', 'woo-product-table', 'wpt_faq_page', 'dashicons-list-view');
    add_submenu_page('woo-product-table', 'WOO Product Configuration', 'Configure', 'edit_theme_options', 'woo-product-table-setting', 'wpt_configure_page');
    //************* Old Menu Backup End *************/
    
}
add_action('admin_menu', 'wpt_admin_menu');