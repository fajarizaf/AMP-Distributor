<?php // Reset Settings

if (!defined('ABSPATH')) exit;

function g7g_cfg_admin_notice() {
	
	$screen = get_current_screen();
	
	if (!property_exists($screen, 'id')) return;
	
	if ($screen->id === 'settings_page_g7g-cfg') {
		
		if (isset($_GET['reset-options'])) {
			
			if ($_GET['reset-options'] === 'true') : ?>
			
			<div class="notice notice-success is-dismissible">
				<p><strong><?php esc_html_e('Default options restored.', 'custom-fields-gutenberg'); ?></strong></p>
			</div>
			
			<?php else : ?>
			
			<div class="notice notice-info is-dismissible">
				<p><strong><?php esc_html_e('No changes made to options.', 'custom-fields-gutenberg'); ?></strong></p>
			</div>
			
			<?php endif;
			
		}
		
	}
	
}

function g7g_cfg_reset_options() {
	
	if (isset($_GET['reset-options-verify']) && wp_verify_nonce($_GET['reset-options-verify'], 'g7g_cfg_reset_options')) {
		
		if (!current_user_can('manage_options')) exit;
		
		$options_delete = delete_option('g7g_cfg_options');
		
		$result = 'false';
		
		if ($options_delete) $result = 'true';
		
		$location = admin_url('options-general.php?page=g7g-cfg&reset-options='. $result);
		
		wp_redirect($location);
		
		exit;
		
	}
	
}
