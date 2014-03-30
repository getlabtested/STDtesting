<?php
/*
Plugin Name: Post Template
Plugin URI: http://post-templates.vincentprat.info
Description: A plugin that allows you to create post templates in order to save time writing posts having the same structure. This plugin makes use of the excellent other plugin "<a href="http://scompt.com/projects/manage-pages-custom-columns-in-wordpress" target="_blank">Manage Pages Custom Columns</a>" by Edward Dale.
Version: 3.3.0
Author: Vincent Prat
Author URI: http://www.vincentprat.info
*/

/*  Copyright 2006 Vincent Prat  (email : vpratfr@yahoo.fr)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

//############################################################################
// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('You are not allowed to call this page directly.'); 
}
//############################################################################

//############################################################################
// Debug mode
define('__DEBUG_POST_TEMPLATES__', false);

// plugin directory
define('POST_TEMPLATES_DIR', dirname (__FILE__));	

// i18n plugin domain 
define('POST_TEMPLATES_I18N_DOMAIN', 'post-template');

// The options of the plugin
define('POST_TEMPLATES_PLUGIN_OPTIONS', 'post_templates_plugin_options');	
define('POST_TEMPLATES_WIDGET_OPTIONS', 'post_templates_widget_options');	

// Capabilities required by the plugin
define('POST_TEMPLATES_CREATE_TEMPLATES_CAP', 'Create templates');
define('POST_TEMPLATES_DELETE_TEMPLATES_CAP', 'Delete templates');
define('POST_TEMPLATES_EDIT_TEMPLATES_CAP', 'Edit templates');
define('POST_TEMPLATES_VIEW_TEMPLATES_CAP', 'View templates');
define('POST_TEMPLATES_USE_TEMPLATES_CAP', 'Use templates');
//############################################################################

//############################################################################
// Include the plugin files
require_once(POST_TEMPLATES_DIR . '/includes/plugin-class.php');
require_once(POST_TEMPLATES_DIR . '/includes/dashboard-widget-class.php');
require_once(POST_TEMPLATES_DIR . '/includes/post-templates-dao-class.php');
require_once(POST_TEMPLATES_DIR . '/includes/post-templates-shortcodes-class.php');
require_once(POST_TEMPLATES_DIR . '/includes/managepages/managepages.php');
//############################################################################

//############################################################################
// Init the plugin classes
global $post_templates_plugin, $post_templates_dao, $post_templates_widget, $post_templates_shortcodes;

$post_templates_dao	 	= new PostTemplatesDAO();
$post_templates_plugin 	= new PostTemplatesPlugin($post_templates_dao);
$post_templates_widget 	= new PostTemplatesDashboardWidget();
$post_templates_shortcodes = new PostTemplatesShortcodes();
//############################################################################

//############################################################################
// Load the plugin text domain for internationalisation
if (!function_exists('post_templates_init_i18n')) {
	function post_templates_init_i18n() {
		load_plugin_textdomain(POST_TEMPLATES_I18N_DOMAIN, 'wp-content/plugins/post-template');
	} // function post_templates_init_i18n()

	post_templates_init_i18n();
} // if (!function_exists('post_templates_init_i18n'))
//############################################################################

//############################################################################
// Add filters and actions
if (is_admin()) {
	add_action(
		'activate_post-template/post-template.php',
		array(&$post_templates_plugin, 'activate'));

	add_action( 
		'admin_menu', 
		array(&$post_templates_plugin, 'add_admin_menus'));
		
	add_action(
		'admin_menu', 
		array(&$post_templates_plugin, 'add_admin_javascript'));

	add_action(
		'post_relatedlinks_list', 
		array(&$post_templates_plugin, 'add_post_relatedlink'));
		
	add_action(
		'page_relatedlinks_list', 
		array(&$post_templates_plugin, 'add_page_relatedlink'));
	
	add_filter(
		'manage_posts_columns', 
		array(&$post_templates_plugin, 'add_promote_to_template_column'));
		
	add_filter(
		'manage_pages_columns', 
		array(&$post_templates_plugin, 'add_promote_to_template_column'));
		
	add_action(
		'manage_posts_custom_column', 
		array(&$post_templates_plugin, 'add_promote_to_template_link'), 10, 2);
		
	add_action(
		'manage_pages_custom_column', 
		array(&$post_templates_plugin, 'add_promote_to_template_link'), 10, 2);
		
	// Add dashboard widget
	//--
	add_action(
		'wp_dashboard_setup', 
		array(&$post_templates_widget, 'register_widget'));
	
	add_filter(
		'wp_dashboard_widgets', 
		array(&$post_templates_widget, 'add_dashboard_widget'));	
} else {
}

// For debug purpose
if (__DEBUG_POST_TEMPLATES__) {
	register_deactivation_hook( __FILE__, 'post_templates_plugin_deactivation' );
	function post_templates_plugin_deactivation() {
		update_option('post_templates_db_version', '');
		update_option('post_templates_version', '2.1.0');
	}
}
//############################################################################

?>