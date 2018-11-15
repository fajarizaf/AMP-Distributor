<?php // Register Settings

if (!defined('ABSPATH')) exit;

function g7g_cfg_get_options() {
	
	global $G7G_CFG_CustomFields;
	
	$default = $G7G_CFG_CustomFields->options();
	
	return get_option('g7g_cfg_options', $default);
	
}

function g7g_cfg_register_settings() {
	
	// register_setting( $option_group, $option_name, $sanitize_callback );
	register_setting('g7g_cfg_options', 'g7g_cfg_options', 'g7g_cfg_validate_options');
	
	// add_settings_section( $id, $title, $callback, $page );
	add_settings_section('post_types', __('Post Types', 'custom-fields-gutenberg'), 'g7g_cfg_settings_section_post_types', 'g7g_cfg_options');
	add_settings_section('settings',   __('Options',    'custom-fields-gutenberg'), 'g7g_cfg_settings_section_settings',   'g7g_cfg_options');
	
	foreach(g7g_cfg_get_post_types() as $type) {
		
		extract($type); // name label
		
		add_settings_field('post-type_'. $name, $label, 'g7g_cfg_callback_checkbox', 'g7g_cfg_options', 'post_types', array('id' => 'post-type_'. $name, 'label' => $name));
		
	}
	
	// add_settings_field( $id, $title, $callback, $page, $section, $args );
	add_settings_field('exclude_protected', __('Exclude Protected', 'custom-fields-gutenberg'), 'g7g_cfg_callback_checkbox', 'g7g_cfg_options', 'settings', array('id' => 'exclude_protected', 'label' => esc_html__('Do not display protected/hidden custom fields',            'custom-fields-gutenberg')));
	add_settings_field('exclude_empty',     __('Exclude Empty',     'custom-fields-gutenberg'), 'g7g_cfg_callback_checkbox', 'g7g_cfg_options', 'settings', array('id' => 'exclude_empty',     'label' => esc_html__('Do not display empty custom fields',                       'custom-fields-gutenberg')));
	add_settings_field('exclude_fields',    __('Exclude Fields',    'custom-fields-gutenberg'), 'g7g_cfg_callback_text',     'g7g_cfg_options', 'settings', array('id' => 'exclude_fields',    'label' => esc_html__('Do not display these custom fields (e.g., books, movies)', 'custom-fields-gutenberg')));
	add_settings_field('reset_options',     __('Reset Options',     'custom-fields-gutenberg'), 'g7g_cfg_callback_reset',    'g7g_cfg_options', 'settings', array('id' => 'reset_options',     'label' => esc_html__('Restore default plugin options',                           'custom-fields-gutenberg')));
	add_settings_field('rate_plugin',       __('Rate Plugin',       'custom-fields-gutenberg'), 'g7g_cfg_callback_rate',     'g7g_cfg_options', 'settings', array('id' => 'rate_plugin',       'label' => esc_html__('Show support with a 5-star rating&nbsp;&raquo;',           'custom-fields-gutenberg')));
	
}

function g7g_cfg_validate_options($input) {
	
	$types = g7g_cfg_get_post_types();
	
	foreach ($types as $type) {
		
		extract($type); // name label
		
		if (in_array($name, $input)) {
			
			if (!isset($input['post-type_'. $name])) $input['post-type_'. $name] = null;
			$input['post-type_'. $name] = ($input['post-type_'. $name] == 1 ? 1 : 0);
			
		}
		
	}
	
	if (!isset($input['exclude_protected'])) $input['exclude_protected'] = null;
	$input['exclude_protected'] = ($input['exclude_protected'] == 1 ? 1 : 0);
	
	if (!isset($input['exclude_empty'])) $input['exclude_empty'] = null;
	$input['exclude_empty'] = ($input['exclude_empty'] == 1 ? 1 : 0);
	
	if (isset($input['exclude_fields'])) $input['exclude_fields'] = sanitize_text_field($input['exclude_fields']);
	
	return $input;
	
}

function g7g_cfg_settings_section_post_types() {
	
	echo '<p>'. esc_html__('Where do you want to display the Custom Fields meta box?', 'custom-fields-gutenberg') .'</p>';
	
}

function g7g_cfg_settings_section_settings() {
	
	echo '<p>'. esc_html__('Note: the default settings give you the same functionality as the original Custom Fields meta box.', 'custom-fields-gutenberg') .'</p>';
	
}

function g7g_cfg_callback_text($args) {
	
	$options = g7g_cfg_get_options();
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'g7g_cfg_options['. $id .']';
	
	echo '<input id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" type="text" size="40" class="regular-text" value="'. esc_attr($value) .'">';
	echo '<label for="'. esc_attr($name) .'">'. esc_html($label) .'</label>';
	
}

function g7g_cfg_callback_textarea($args) {
	
	$options = g7g_cfg_get_options();
	
	$allowed_tags = wp_kses_allowed_html('post');
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'g7g_cfg_options['. $id .']';
	
	echo '<textarea id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" rows="3" cols="50" class="large-text code">'. wp_kses(stripslashes_deep($value), $allowed_tags) .'</textarea>';
	echo '<label for="'. esc_attr($name) .'">'. esc_html($label) .'</label>';
	
}

function g7g_cfg_callback_checkbox($args) {
	
	$options = g7g_cfg_get_options();
	
	$id    = isset($args['id'])    ? $args['id']    : '';
	$label = isset($args['label']) ? $args['label'] : '';
	$value = isset($options[$id])  ? $options[$id]  : '';
	
	$name = 'g7g_cfg_options['. $id .']';
	
	echo '<input id="'. esc_attr($name) .'" name="'. esc_attr($name) .'" type="checkbox" '. checked($value, 1, false) .' value="1"> ';
	echo '<label for="'. esc_attr($name) .'" class="inline-block">'. esc_html($label) .'</label>';
	
}

function g7g_cfg_callback_reset($args) {
	
	$nonce = wp_create_nonce('g7g_cfg_reset_options');
	
	$href  = add_query_arg(array('reset-options-verify' => $nonce), admin_url('options-general.php?page=g7g-cfg'));
	
	$label = isset($args['label']) ? $args['label'] : esc_html__('Restore default plugin options', 'custom-fields-gutenberg');
	
	echo '<a class="g7g-cfg-reset-options" href="'. esc_url($href) .'">'. esc_html($label) .'</a>';
	
}

function g7g_cfg_callback_rate($args) {
	
	$href  = 'https://wordpress.org/support/plugin/'. G7G_CFG_SLUG .'/reviews/?rate=5#new-post';
	$title = esc_attr__('Please give this plugin a 5-star rating :) A huge THANK YOU for your support!', 'custom-fields-gutenberg');
	$text  = isset($args['label']) ? $args['label'] : esc_html__('Show support with a 5-star rating&nbsp;&raquo;', 'custom-fields-gutenberg');
	
	echo '<a target="_blank" rel="noopener noreferrer" class="g7g-cfg-rate-plugin" href="'. $href .'" title="'. $title .'">'. $text .'</a>';
	
}
