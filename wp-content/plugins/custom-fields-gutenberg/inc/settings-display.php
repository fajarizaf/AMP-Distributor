<?php // Display Settings

if (!defined('ABSPATH')) exit;

function g7g_cfg_menu_pages() {
	
	// add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function )
	
	add_options_page(
		__('Custom Fields for Gutenberg', 'custom-fields-gutenberg'), 
		__('Custom Fields Gutenberg', 'custom-fields-gutenberg'), 
		'manage_options', 'g7g-cfg', 'g7g_cfg_display_settings'
	);
	
}

function g7g_cfg_display_settings() { 
	
	?>
	
	<div class="wrap">
		<h1>
			<span class="fa fa-pad fa-gear"></span> <?php echo G7G_CFG_NAME; ?> 
			<span class="g7g-cfg-version"><?php echo G7G_CFG_VERSION; ?></span>
		</h1>
		<p>
			<?php esc_html_e('Thanks for using this free plugin. For complete documentation,', 'custom-fields-gutenberg'); ?> 
			<a target="_blank" rel="noopener noreferrer" href="https://wordpress.org/plugins/custom-fields-gutenberg/#installation">
				<?php esc_html_e('visit the plugin homepage', 'custom-fields-gutenberg'); ?>&nbsp;&raquo;
			</a>
		</p>
		<form method="post" action="options.php">
			
			<?php 
				settings_fields('g7g_cfg_options');
				do_settings_sections('g7g_cfg_options');
				submit_button(); 
			?>
			
		</form>
	</div>
	
	<?php 
	
}
