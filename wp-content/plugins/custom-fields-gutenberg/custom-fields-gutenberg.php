<?php 
/*
	Plugin Name: Custom Fields for Gutenberg
	Plugin URI: https://perishablepress.com/custom-fields-gutenberg/
	Description: Restores the Custom Field meta box for Gutenberg.
	Tags: gutenberg, custom fields, meta box, field, box, restore, display
	Author: Jeff Starr
	Author URI: https://plugin-planet.com/
	Donate link: https://monzillamedia.com/donate.html
	Contributors: specialk
	Requires at least: 4.5
	Tested up to: 4.9.8
	Stable tag: 1.3
	Version: 1.3
	Requires PHP: 5.2
	Text Domain: custom-fields-gutenberg
	Domain Path: /languages
	License: GPL v2 or later
*/

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 
	2 of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	with this program. If not, visit: https://www.gnu.org/licenses/
	
	Copyright 2018 Monzilla Media. All rights reserved.
*/

if (!defined('ABSPATH')) die();

if (!class_exists('G7G_CFG_CustomFields')) {
	
	class G7G_CFG_CustomFields {
		
		function __construct() {
			
			$this->constants();
			$this->includes();
			
			add_action('admin_init',          array($this, 'check_version'));
			add_action('plugins_loaded',      array($this, 'load_i18n'));
			add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
			add_filter('plugin_row_meta',     array($this, 'plugin_links'), 10, 2);
			
			add_action('admin_enqueue_scripts', 'g7g_cfg_enqueue_resources_admin');
			add_action('admin_print_scripts',   'g7g_cfg_admin_print_scripts');
			add_action('admin_notices',         'g7g_cfg_admin_notice');
			add_action('admin_init',            'g7g_cfg_register_settings');
			add_action('admin_init',            'g7g_cfg_reset_options');
			add_action('admin_menu',            'g7g_cfg_menu_pages');
			
			add_action('enqueue_block_editor_assets', 'g7g_cfg_load_assets_editor');
			add_action('add_meta_boxes',              'g7g_cfg_add_meta_box');
			
		} 
		
		function constants() {
			
			if (!defined('G7G_CFG_VERSION')) define('G7G_CFG_VERSION', '1.3');
			if (!defined('G7G_CFG_REQUIRE')) define('G7G_CFG_REQUIRE', '4.5');
			if (!defined('G7G_CFG_AUTHOR'))  define('G7G_CFG_AUTHOR',  'Jeff Starr');
			if (!defined('G7G_CFG_NAME'))    define('G7G_CFG_NAME',    __('Custom Fields for Gutenberg', 'custom-fields-gutenberg'));
			if (!defined('G7G_CFG_HOME'))    define('G7G_CFG_HOME',    esc_url('https://perishablepress.com/custom-fields-gutenberg/'));
			if (!defined('G7G_CFG_URL'))     define('G7G_CFG_URL',     plugin_dir_url(__FILE__));
			if (!defined('G7G_CFG_DIR'))     define('G7G_CFG_DIR',     plugin_dir_path(__FILE__));
			if (!defined('G7G_CFG_FILE'))    define('G7G_CFG_FILE',    plugin_basename(__FILE__));
			if (!defined('G7G_CFG_SLUG'))    define('G7G_CFG_SLUG',    basename(dirname(__FILE__)));
			
		}
		
		function includes() {
			
			if (is_admin()) {
				
				require_once G7G_CFG_DIR .'inc/plugin-core.php';
				require_once G7G_CFG_DIR .'inc/resources-enqueue.php';
				require_once G7G_CFG_DIR .'inc/settings-display.php';
				require_once G7G_CFG_DIR .'inc/settings-register.php';
				require_once G7G_CFG_DIR .'inc/settings-reset.php';
				
			}
			
		}
		
		function options() {
			
			$options = array(
				
				'exclude_protected' => 1,
				'exclude_empty'     => 0,
				'exclude_fields'    => ''
				
			);
			
			$types = g7g_cfg_get_post_types();
			
			foreach($types as $type) {
				
				extract($type); // name label
				
				$options['post-type_'. $name] = 1;
				
			}
			
			return apply_filters('g7g_cfg_options', $options);
			
		}
		
		function action_links($links, $file) {
			
			if ($file === G7G_CFG_FILE) {
				
				$settings = '<a href="'. admin_url('options-general.php?page=g7g-cfg') .'">'. esc_html__('Settings', 'custom-fields-gutenberg') .'</a>';
				
				array_unshift($links, $settings);
				
			}
			
			return $links;
			
		}
		
		function plugin_links($links, $file) {
			
			if ($file === G7G_CFG_FILE) {
				
				$rate_href  = 'https://wordpress.org/support/plugin/'. G7G_CFG_SLUG .'/reviews/?rate=5#new-post';
				$rate_title = esc_attr__('Click here to rate and review this plugin on WordPress.org', 'custom-fields-gutenberg');
				$rate_text  = esc_html__('Rate this plugin', 'custom-fields-gutenberg') .'&nbsp;&raquo;';
				
				$links[] = '<a target="_blank" rel="noopener noreferrer" href="'. $rate_href .'" title="'. $rate_title .'">'. $rate_text .'</a>';
				
			}
			
			return $links;
			
		}
		
		function check_version() {
			
			$wp_version = get_bloginfo('version');
			
			if (isset($_GET['activate']) && $_GET['activate'] == 'true') {
				
				if (version_compare($wp_version, G7G_CFG_REQUIRE, '<')) {
					
					if (is_plugin_active(G7G_CFG_FILE)) {
						
						deactivate_plugins(G7G_CFG_FILE);
						
						$msg  = '<strong>'. G7G_CFG_NAME .'</strong> '. esc_html__('requires WordPress ', 'custom-fields-gutenberg') . G7G_CFG_REQUIRE;
						$msg .= esc_html__(' or higher, and has been deactivated! ', 'custom-fields-gutenberg');
						$msg .= esc_html__('Please return to the', 'custom-fields-gutenberg') .' <a href="'. admin_url() .'">';
						$msg .= esc_html__('WP Admin Area', 'custom-fields-gutenberg') .'</a> ';
						$msg .= esc_html__('to upgrade WordPress and try again.', 'custom-fields-gutenberg');
						
						wp_die($msg);
						
					}
					
				}
				
			}
			
		}
		
		function load_i18n() {
			
			load_plugin_textdomain('custom-fields-gutenberg', false, G7G_CFG_DIR .'languages/');
			
		}
		
		function __clone() {
			
			_doing_it_wrong(__FUNCTION__, esc_html__('Sorry, pal!', 'custom-fields-gutenberg'), G7G_CFG_VERSION);
			
		}
		
		function __wakeup() {
			
			_doing_it_wrong(__FUNCTION__, esc_html__('Sorry, pal!', 'custom-fields-gutenberg'), G7G_CFG_VERSION);
			
		}
		
	}
	
	$G7G_CFG_CustomFields = new G7G_CFG_CustomFields(); 
	
}
