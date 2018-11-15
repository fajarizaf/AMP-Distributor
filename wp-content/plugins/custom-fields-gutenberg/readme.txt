=== Custom Fields for Gutenberg ===

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

Restores the Custom Field meta box for Gutenberg.



== Description ==

Gutenberg no longer displays Custom Fields that are attached to posts. This plugin restores pre-Gutenberg functionality, and displays the Custom Fields meta box on all Gutenberg-enabled pages.



**Features**

* Easy to use
* Clean code
* Built with the WordPress API
* Lightweight, fast and flexible
* Works great with other WordPress plugins
* Plugin options configurable via settings screen
* Focused on flexibility, performance, and security
* One-click restore plugin default options
* Translation ready


**Options**

* Specify the post types that should display custom fields
* Exclude custom fields that are protected/hidden
* Exclude custom fields with empty values
* Exclude specific custom fields by name


**Planned Features**

* Ajaxify adding of new Custom Fields
* Ajax method to Delete custom fields


**GDPR**

This plugin does not collect any data. So it does _not_ do anything to make your site _less_ compliant with GDPR. I have done my best to ensure that this plugin is 100% GDPR compliant, but I'm not a lawyer so can't guarantee anything. To determine if your site is GDPR compliant, please consult an attorney.



== Screenshots ==

1. Plugin Settings Screen (showing default options)
2. Custom Fields displayed on Gutenberg screen



== Installation ==

**Installing the plugin**

1. Upload the plugin to your blog and activate
2. Configure the plugin settings as desired
3. Enable theme switcher via settings or shortcode

[More info on installing WP plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins)


**Usage**

Works just like the original "Custom Fields" meta box, except:

* __Edit custom field__     &mdash; make any changes and then click the Post "Update" or "Publish" button
* __Add new custom field__  &mdash; add new custom field, click "Update" or "Publish", and then reload the page
* __Delete custom field__   &mdash; set the field custom field Key/Name to a blank value, click "Update" or "Publish", then reload the page


**Uninstalling**

This plugin cleans up after itself. All plugin settings will be removed from your database when the plugin is uninstalled via the Plugins screen. Custom Fields will NOT be removed.



== Upgrade Notice ==

To upgrade this plugin, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

Note: uninstalling the plugin from the WP Plugins screen results in the removal of all settings and data from the WP database. Custom Fields will NOT be removed.



== Frequently Asked Questions ==

**Got a question?**

Send any questions or feedback via my [contact form](https://perishablepress.com/contact/)



== Support development of this plugin ==

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books:

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Super fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thanks! :)



== Changelog ==

**1.3 (2018/08/14)**

* Adds `rel="noopener noreferrer"` to all [blank-target links](https://perishablepress.com/wordpress-blank-target-vulnerability/)
* Makes it possible to delete any custom field by setting its key/name to blank value
* Adds "rate plugin" link to settings page
* Updates donate link
* Updates GDPR blurb
* Regenerates default translation template
* Further tests on WP versions 4.9 and 5.0 (alpha)

**1.2 (2018/03/27)**

* Improves logic of `g7g_cfg_get_post_types()`
* Replaces `update_option` with `delete_option` in `g7g_cfg_reset_options`
* Renames `G7G_DisplayCustomFields` to `G7G_CFG_CustomFields`
* Replaces readme.txt URL with WP Plugin Directory URL
* Further tests on WordPress 5.0 (alpha)

**1.1 (2018/03/26)**

* Replaces `filemtime()` with `g7g_cfg_random_string()`
* Changes name and slug to meet WP Directory requirements
* Renames plugin from "Gutenberg Display Custom Fields" to "Custom Fields for Gutenberg"
* Renames plugin constants
* Renames all functions
* Renames options, et al
* Regenerates default translation file

**1.0 (2018/03/25)**

* Initial release
