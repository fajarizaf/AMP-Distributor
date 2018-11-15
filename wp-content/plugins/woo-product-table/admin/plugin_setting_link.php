<?php

add_filter('plugin_action_links_' . WOO_Product_Table::getPath('PLUGIN_BASE_FILE'), 'wpt_add_action_links');
//add_filter('plugin_action_links_woo-product-table-pro/woo-product-table-pro.php', 'wpt_add_action_links');

function wpt_add_action_links($links) {
    $wpt_links[] = '<a href="' . admin_url('admin.php?page=woo-product-table') . '" title="Go to Setting Page">Configure</a>';
    //$wpt_links[] = '<a title="See FAQ - How to use." href="' . admin_url('admin.php?page=woo-product-table-faq') . '">FAQ - Shortcode</a>';
    //$links[] = '<a href="' . admin_url( 'options-general.php?page=myplugin' ) . '">Settings</a>';
    return array_merge($wpt_links, $links);
}
