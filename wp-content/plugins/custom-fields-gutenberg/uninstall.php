<?php // uninstall plugin

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

global $wpdb;

function g7g_cfg_uninstall() {
	
	delete_option('g7g_cfg_options');
	
}

if (function_exists('is_multisite') && is_multisite()) {
	
	if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
		
		$sites = get_sites();
		
		foreach ($sites as $site) {
			
			switch_to_blog($site->blog_id);
			g7g_cfg_uninstall();
			restore_current_blog();
			
		}
		
	} else {

		$sites = wp_get_sites(array('limit' => 0));
		
		foreach ($sites as $site) {
			
			switch_to_blog($site['blog_id']);
			g7g_cfg_uninstall();
			restore_current_blog();
			
		}
		
	}
	
} else {
	
	g7g_cfg_uninstall();
	
}

wp_cache_flush();