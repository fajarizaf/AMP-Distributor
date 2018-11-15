<?php // plugin core

if (!defined('ABSPATH')) exit;

function g7g_cfg_add_meta_box() {
	
	global $post;
	
	$wp_version = get_bloginfo('version');
	
	$is_version_wp = version_compare($wp_version, '5.0', '>=');
	
	$is_gutenberg_plugin = is_plugin_active('gutenberg/gutenberg.php');
	
	if (!$is_version_wp && !$is_gutenberg_plugin) return;
	
	if (!has_meta($post->ID)) return;
	
	foreach (g7g_cfg_get_enabled_post_types() as $post_type) {
		
		add_meta_box('g7g_cfg_custom_fields', __('Custom Fields', 'custom-fields-gutenberg'), 'g7g_cfg_display_meta_box', $post_type, 'normal');
		
	}
	
}

function g7g_cfg_display_meta_box($post) {
	
	$metadata = has_meta($post->ID);
	
	if ($metadata) {
		
		echo '<div class="g7g-meta-box">';
		
		if (g7g_cfg_display_row_table($metadata)) {
			
			echo '<table>';
			echo '<thead>';
			echo '<tr>';
			echo '<th>'. __('Name',  'custom-fields-gutenberg') .'</th>';
			echo '<th>'. __('Value', 'custom-fields-gutenberg') .'</th>';
			echo '</tr>';
			echo '</thead>';
			echo '<tbody>';
			
			foreach ($metadata as $meta) {
				
				echo g7g_cfg_list_meta_row($meta);
				
			}
			
			echo '</tbody>';
			echo '</table>';
			
		}
		
		echo g7g_cfg_meta_form($post);
		
		echo '</div>';
		
	}

}

function g7g_cfg_list_meta_row($meta) {
	
	$meta_key = isset($meta['meta_key'])   ? esc_attr($meta['meta_key']) : null;
	$meta_val = isset($meta['meta_value']) ? esc_textarea($meta['meta_value']) : null;
	$meta_id  = isset($meta['meta_id'])    ? (int) $meta['meta_id'] : null;
	$post_id  = isset($meta['post_id'])    ? (int) $meta['post_id'] : null;
	
	if (empty($meta_key)) {
		
		delete_metadata_by_mid('post', $meta_id);
		
		return '';
		
	}
	
	if (!g7g_cfg_display_protected($meta_key)) return '';

	if (!g7g_cfg_display_empty($meta_val)) return '';
	
	if (!g7g_cfg_display_field($meta_key)) return '';
	
	if (is_serialized($meta_val)) {
		
		if (is_serialized_string($meta_val)) $meta_val = maybe_unserialize($meta_val);
		
		else return '';
		
	}
	
	$r  = '<tr id="meta-'. $meta_id .'">';
	
	$r .= '<td><label class="screen-reader-text" for="meta-'. $meta_id .'-key">'. __('Key', 'custom-fields-gutenberg') .'</label>';
	
	$r .= '<input name="meta['. $meta_id .'][key]" id="meta-'. $meta_id .'-key" type="text" size="20" value="'. $meta_key .'" />';
	
	$r .= wp_nonce_field('change-meta', '_ajax_nonce', false, false);
	
	$r .= '</td>';
	
	$r .= '<td><label class="screen-reader-text" for="meta-'. $meta_id .'-value">'. __('Value', 'custom-fields-gutenberg') .'</label>';
	
	$r .= '<textarea name="meta['. $meta_id .'][value]" id="meta-'. $meta_id .'-value" rows="2" cols="30">'. $meta_val .'</textarea>';
	
	$r .= '</td>';
	
	$r .= '</tr>';
	
	return $r;
	
}

function g7g_cfg_meta_form($post = null) {
	
	global $wpdb;
	
	$post = get_post($post);
	
	$keys = apply_filters('postmeta_form_keys', null, $post);
	
	if (null === $keys) {
		
		$limit = apply_filters('postmeta_form_limit', 30);
		
		$sql = "SELECT DISTINCT meta_key 
				FROM $wpdb->postmeta 
				WHERE meta_key NOT BETWEEN '_' AND '_z' 
				HAVING meta_key NOT LIKE %s 
				ORDER BY meta_key 
				LIMIT %d";
		
		$keys = $wpdb->get_col($wpdb->prepare($sql, $wpdb->esc_like('_') . '%', $limit));
		
	}

	if ($keys) {
		
		natcasesort($keys);
		
		$meta_key_input_id = 'metakeyselect';
		
	} else {
		
		$meta_key_input_id = 'metakeyinput';
		
	}
	
	?>
	
	<p><strong><?php esc_html_e('Add New Custom Field', 'custom-fields-gutenberg'); ?></strong></p>
	<table>
		<thead>
			<tr>
				<th><label for="<?php echo esc_attr($meta_key_input_id); ?>"><?php esc_html_e('Name', 'custom-fields-gutenberg'); ?></label></th>
				<th><label for="metavalue"><?php esc_html_e('Value', 'custom-fields-gutenberg'); ?></label></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					
					<?php if ($keys) : ?>
					
					<select id="metakeyselect" name="metakeyselect">
						<option value="#NONE#"><?php esc_html_e('&mdash; Select &mdash;', 'custom-fields-gutenberg'); ?></option>
						
						<?php 
						
						foreach ($keys as $key) : 
							
							if (is_protected_meta($key, 'post') || !current_user_can('add_post_meta', $post->ID, $key)) continue;
								
							echo '<option value="'. esc_attr($key) .'">'. esc_html($key) .'</option>';
							
						endforeach;
						
						?>
						
					</select>
					
					<input class="hide-if-js" type="text" id="metakeyinput" name="metakeyinput" value="" />
					
					<a class="g7g-enter-new hide-if-no-js" href="#postcustomstuff" onclick="jQuery('#metakeyinput, #metakeyselect, #enternew, #cancelnew').toggle();return false;">
						<span id="enternew"><?php esc_html_e('Enter new', 'custom-fields-gutenberg'); ?></span> 
						<span id="cancelnew" class="hidden"><?php esc_html_e('Cancel', 'custom-fields-gutenberg'); ?></span>
					</a> 
					
					<?php else : ?>
					
					<input type="text" id="metakeyinput" name="metakeyinput" value="" />
					
					<?php endif; ?>
					
				</td>
				<td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea></td>
			</tr>
		</tbody>
	</table>
	
	<?php
	
	echo '<p>';
	echo esc_html__('Custom fields can be used to add extra metadata to a post that you can', 'custom-fields-gutenberg');
	echo ' <a target="_blank" rel="noopener noreferrer" href="https://codex.wordpress.org/Using_Custom_Fields">'. esc_html__('use in your theme', 'custom-fields-gutenberg') .'</a>. ';
	echo esc_html__('Visit the plugin', 'custom-fields-gutenberg');
	echo ' <a href="'. admin_url('options-general.php?page=g7g-cfg') .'">'. esc_html__('settings', 'custom-fields-gutenberg') .'</a> ';
	echo esc_html__('for more info.', 'custom-fields-gutenberg');
	echo '</p>';
	
}

function g7g_cfg_display_row_table($metadata) {
	
	$options = g7g_cfg_get_options();
	
	$exclude_protected = isset($options['exclude_protected']) ? $options['exclude_protected'] : 1;
	$exclude_empty     = isset($options['exclude_empty'])     ? $options['exclude_empty']     : 0;
	
	$display = false;
	
	foreach ($metadata as $meta) {
		
		$meta_key = isset($meta['meta_key'])   ? $meta['meta_key']   : null;
		$meta_val = isset($meta['meta_value']) ? $meta['meta_value'] : null;
		
		if (substr($meta_key, 0, 1) !== '_') {
			
			if (!empty($meta_val)) {
				
				$display = true;
				break;
				 
			} elseif (empty($meta_val) && !$exclude_empty) {
				
				$display = true;
				break;
				
			}
			
		} else {
			
			if (!$exclude_protected) {
				
				if (!empty($meta_val)) {
					
					$display = true;
					break;
					 
				} elseif (empty($meta_val) && !$exclude_empty) {
					
					$display = true;
					break;
					
				}
				
			}
			
		}
		
	}
	
	return $display;
	
}

function g7g_cfg_get_post_types() {
	
	$post_types = get_post_types(array(), 'objects');
	
	$unset = array('attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache');
	
	$unset = apply_filters('g7g_cfg_post_types_unset', $unset);
	
	$types = array();
	
	foreach($post_types as $key => $post_type) {
		
		$types[$key]['name']  = $post_type->name;
		$types[$key]['label'] = $post_type->label;
		
		if (in_array($post_type->name, $unset) || !post_type_supports($post_type->name, 'custom-fields')) unset($types[$key]);
		
	}
	
	return apply_filters('g7g_cfg_get_post_types', $types);
	
}

function g7g_cfg_get_enabled_post_types() {
	
	global $post;
	
	$options = g7g_cfg_get_options();
	
	$array = array();
	
	foreach ($options as $key => $value) {
		
		preg_match('/^post-type_(.*)$/i', $key, $matches);
		
		if (isset($matches[1]) && !empty($matches[1])) $array[] = $matches[1];
		
	}
	
	return apply_filters('g7g_cfg_get_enabled_post_types', $array);
	
}

function g7g_cfg_display_protected($meta_key) {
	
	$options = g7g_cfg_get_options();
	
	$exclude_protected = isset($options['exclude_protected']) ? $options['exclude_protected'] : 1;
	
	$is_protected = is_protected_meta($meta_key, 'post');
	
	if ($is_protected && $exclude_protected) return false;
	
	return true;
	
}

function g7g_cfg_display_empty($meta_val) {
	
	$options = g7g_cfg_get_options();
	
	$exclude_empty = isset($options['exclude_empty']) ? $options['exclude_empty'] : 0;
	
	$is_empty = empty($meta_val) ? true : false;
	
	if ($is_empty && $exclude_empty) return false;
	
	return true;
	
}

function g7g_cfg_display_field($meta_key) {
	
	$options = g7g_cfg_get_options();
	
	$exclude_fields = isset($options['exclude_fields']) ? $options['exclude_fields'] : '';
	
	$fields = array_map('trim', explode(',', $exclude_fields));
	
	$fields = array_filter($fields);
	
	if (in_array($meta_key, $fields)) return false;
	
	return true;
	
}

function g7g_cfg_random_string($length = 10) {
	
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	
	$charactersLength = strlen($characters);
	
	$randomString = '';
	
	for ($i = 0; $i < $length; $i++) {
		
		$randomString .= $characters[rand(0, $charactersLength - 1)];
		
	}
	
	return $randomString;
	
}
