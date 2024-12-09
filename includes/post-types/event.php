<?php
/**
 *
 * @class       WPGenius_Events_API
 * @author      Team WPGenius (Makarand Mane)
 * @category    Admin
 * @package     wpgenius-event-management/includes/post-types
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WPGenius_Event_Taxonomy
 *
 * Registers the shared 'event_taxonomy' for associating content with events.
 */
class WPGenius_Event_Taxonomy {

    /**
     * Holds the single instance of this class.
     *
     * @var WPGenius_Event_Taxonomy
     */
    protected static $instance;

    /**
     * Initialize the singleton instance.
     *
     * @return WPGenius_Event_Taxonomy
     */
    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor to ensure singleton pattern.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'register_taxonomy' ) );
    }

    /**
     * Register the event_taxonomy taxonomy.
     */
    public function register_taxonomy() {
        $labels = array(
            'name'              => _x( 'Events', 'taxonomy general name', 'wpgenius-event-plugin' ),
            'singular_name'     => _x( 'Event', 'taxonomy singular name', 'wpgenius-event-plugin' ),
            'search_items'      => __( 'Search Events', 'wpgenius-event-plugin' ),
            'all_items'         => __( 'All Events', 'wpgenius-event-plugin' ),
            'parent_item'       => __( 'Parent Event', 'wpgenius-event-plugin' ),
            'parent_item_colon' => __( 'Parent Event:', 'wpgenius-event-plugin' ),
            'edit_item'         => __( 'Edit Event', 'wpgenius-event-plugin' ),
            'update_item'       => __( 'Update Event', 'wpgenius-event-plugin' ),
            'add_new_item'      => __( 'Add New Event', 'wpgenius-event-plugin' ),
            'new_item_name'     => __( 'New Event Name', 'wpgenius-event-plugin' ),
            'menu_name'         => __( 'Events', 'wpgenius-event-plugin' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'event' ),
        );

        register_taxonomy( 'event_taxonomy', array( 'session', 'speaker', 'organizer', 'sponsor' ), $args );
    }
}

// Initialize the taxonomy.
WPGenius_Event_Taxonomy::init();
