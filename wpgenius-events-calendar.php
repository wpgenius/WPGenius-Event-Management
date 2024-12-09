<?php
/**
 * Plugin Name: WPGenius Event Management
 * Description: Manage events with custom post types, taxonomies, Elementor widgets, and shortcodes.
 * Plugin URI: https://wpgenius.in
 * Version: 1.0.0
 * Author: Team WPGenius (Makarand Mane)
 * Author URI: https://makarandmane.com
 * Text Domain: wpgenius-event-management
*/
/*
* Copyright 2024  Team WPGenius  (email : makarand@wpgenius.in)
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WPG_EVENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPG_EVENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files.
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-init.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-database.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-actions.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-admin.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-ajax.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-settings.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/class-wpgenius-shortcodes.php';
require_once WPG_EVENT_PLUGIN_DIR . 'includes/elementor/elementor-widgets.php';

// Add text domain
add_action('plugins_loaded','wpgenius_events_translations');
function wpgenius_events_translations(){
    $locale = apply_filters("plugin_locale", get_locale(), 'wpgenius-event-management');
    $lang_dir = dirname( __FILE__ ) . '/languages/';
    $mofile        = sprintf( '%1$s-%2$s.mo', 'wpgenius-event-management', $locale );
    $mofile_local  = $lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

    if ( file_exists( $mofile_global ) ) {
        load_textdomain( 'wpgenius-event-management', $mofile_global );
    } else {
        load_textdomain( 'wpgenius-event-management', $mofile_local );
    }  
}

if(class_exists('WPGenius_Events_Actions'))
 	WPGenius_Events_Actions::init();

if(class_exists('WPGenius_Events_Admin') && is_admin())
 	WPGenius_Events_Admin::init();

if(class_exists('WPGenius_Events_Ajax'))
 	WPGenius_Events_Ajax::init();

if(class_exists('WPGenius_Events_Settings'))
 	WPGenius_Events_Settings::init();

if(class_exists('WPGenius_Shortcodes'))
    WPGenius_Shortcodes::init();

register_activation_hook( 	__FILE__, array( $wbcdb, 'activate_events' 	) );
register_deactivation_hook( __FILE__, array( $wbcdb, 'deactivate_events' ) );
register_activation_hook( __FILE__, function(){ register_uninstall_hook( __FILE__, array( 'WPGenius_Events_DB', 'uninstall_events' ) ); });