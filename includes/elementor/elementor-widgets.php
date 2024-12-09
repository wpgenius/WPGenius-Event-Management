<?php
/**
 *
 * @class       WPGenius_Events_Elementor_Widgets
 * @author      Team WPGenius (Makarand Mane)
 * @category    Admin
 * @package     wpgenius-event-management/includes/elementor
 * @version     1.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGenius_Events_Elementor_Widgets {

    public static function init() {
        add_action( 'elementor/widgets/widgets_registered', array( __CLASS__, 'register_widgets' ) );
    }

    public static function register_widgets() {
        if ( defined( 'ELEMENTOR_PATH' ) && class_exists( '\Elementor\Widget_Base' ) ) {
            require_once WPG_EVENT_PLUGIN_DIR . 'includes/elementor/class-organizer-widget.php';
            require_once WPG_EVENT_PLUGIN_DIR . 'includes/elementor/class-speaker-widget.php';
            require_once WPG_EVENT_PLUGIN_DIR . 'includes/elementor/class-agenda-widget.php';
            require_once WPG_EVENT_PLUGIN_DIR . 'includes/elementor/class-sponsor-widget.php';

            $widgets_manager = \Elementor\Plugin::instance()->widgets_manager;

            $widgets_manager->register_widget_type( new \WPGenius_Organizer_Widget() );
            $widgets_manager->register_widget_type( new \WPGenius_Speaker_Widget() );
            $widgets_manager->register_widget_type( new \WPGenius_Agenda_Widget() );
            $widgets_manager->register_widget_type( new \WPGenius_Sponsor_Widget() );
        }
    }
}
