<?php // Enqueue Resources

if (!defined('ABSPATH')) exit;

function g7g_cfg_enqueue_resources_admin() {
	
	$screen = get_current_screen();
	
	if (!property_exists($screen, 'id')) return;
	
	if ($screen->id === 'settings_page_g7g-cfg') {
		
		$js_deps = array('jquery', 'jquery-ui-core', 'jquery-ui-dialog');
		
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		wp_enqueue_style('g7g-cfg-font-icons', G7G_CFG_URL .'css/font-icons.css', array(), null);
		
		wp_enqueue_style('g7g-cfg-settings', G7G_CFG_URL .'css/settings.css', array(), null);
		
		wp_enqueue_script('g7g-cfg-settings', G7G_CFG_URL .'js/settings.js', $js_deps);
		
	}
	
}

function g7g_cfg_load_assets_editor() {
	
	$random = g7g_cfg_random_string();
	
	wp_enqueue_style('g7g-cfg-load-assets', G7G_CFG_URL .'blocks/blocks.css', array(), $random);
	
	// wp_enqueue_script('g7g-cfg-load-assets-editor', G7G_CFG_URL .'blocks/blocks.js', array('wp-blocks', 'wp-element', 'wp-components', 'wp-i18n'), $random);
	
}

function g7g_cfg_load_assets() {
	
	// add_action('enqueue_block_assets', 'g7g_cfg_load_assets'); // just fyi
		
}

function g7g_cfg_admin_print_scripts() { ?>
	
	<script type="text/javascript">
		var 
		alert_reset_options_title   = '<?php _e('Confirm Reset',            'custom-fields-gutenberg'); ?>',
		alert_reset_options_message = '<?php _e('Restore default options?', 'custom-fields-gutenberg'); ?>',
		alert_reset_options_true    = '<?php _e('Yes, make it so.',         'custom-fields-gutenberg'); ?>',
		alert_reset_options_false   = '<?php _e('No, abort mission.',       'custom-fields-gutenberg'); ?>';
	</script>
	
<?php
	
}