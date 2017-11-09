<?php
/*
Plugin Name: WooCommerce Theme Override
Description: Override the theme for any WooCommerce store
Author: Kenneth Studer
Author URI: 
Version: 1.0
*/

require_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/Plugin-Settings.php';

add_action( 'admin_menu', 'start_woocommerce_themeoverride_admin_menu' );
// Plugs into our custom post-type menu and adds more functionality
function start_woocommerce_themeoverride_admin_menu() {
	add_action( 'admin_init', 'register_woocommerce_theme_override_settings' );
	
	add_submenu_page( 'woocommerce', 'Theme Overrides', 'Theme Override',
		'edit_themes', 'woocommerce-theme-override-settings', 'woocommerce_theme_override_settings_page' );
	
	add_submenu_page( 'woocommerce-theme-override-settings', 'Edit Theme', 'Edit Theme',
		'edit_themes', 'woocommerce-theme-edit', 'woocommerce_theme_override_edit_page' );
}

function register_woocommerce_theme_override_settings() {
	register_setting( 'woocommerce-theme-override-settings-group', 'auth/footer.php' );
}

//This will list the plugin settings
function woocommerce_theme_override_settings_page() {
	include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/WooCommerce-Theme-Settings.php';
}

//This will show the edit theme page
function woocommerce_theme_override_edit_page() {
	include_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/WooCommerce-Theme-Edit.php';
}

/* Add our template to WooCommerce */
add_filter( 'woocommerce_locate_template', 'woocommerce_themeoverride_locate_template', 9, 3 );
function woocommerce_themeoverride_locate_template( $template, $template_name, $template_path ) {
	global $woocommerce;
	$_template = $template;
	if ( ! $template_path ) $template_path = $woocommerce->template_url;
	// Gets the absolute path to this plugin's WooCommerce directory
	$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/woocommerce/';
	
	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			$template_path . $template_name,
			$template_name
		)
	);
	
	// Modification: Get the template from this plugin, if it exists
	if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
		if ( PluginSettings::get_option( $template_name ) ) {
			$template = $plugin_path . $template_name;
		}
	}
	
	// Use default template
	if ( ! $template )
		 $template = $_template;
	
	// Return what we found
	return $template;
}

