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
 * Class WPGenius_Sponsor
 *
 * Registers the Sponsor post type and inline meta box for Event and Sponsor Level.
 */
class WPGenius_Sponsor {

    protected static $instance;

    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new WPGenius_Sponsor();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
        add_filter( 'manage_sponsor_posts_columns', array( $this, 'add_custom_columns' ) );
        add_action( 'manage_sponsor_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
    }

    public function register_post_type() {
        $visibility = get_option( 'wpgenius_cpt_visibility', array( 'sponsor' => 'public' ) );
        $public = ( isset( $visibility['sponsor'] ) && $visibility['sponsor'] === 'backend' ) ? false : true;

        $labels = array(
            'name'               => _x( 'Sponsors', 'post type general name', 'wpgenius-event-plugin' ),
            'singular_name'      => _x( 'Sponsor', 'post type singular name', 'wpgenius-event-plugin' ),
            'menu_name'          => _x( 'Sponsors', 'admin menu', 'wpgenius-event-plugin' ),
            'add_new'            => __( 'Add New', 'wpgenius-event-plugin' ),
            'add_new_item'       => __( 'Add New Sponsor', 'wpgenius-event-plugin' ),
            'new_item'           => __( 'New Sponsor', 'wpgenius-event-plugin' ),
            'edit_item'          => __( 'Edit Sponsor', 'wpgenius-event-plugin' ),
            'view_item'          => __( 'View Sponsor', 'wpgenius-event-plugin' ),
            'all_items'          => __( 'All Sponsors', 'wpgenius-event-plugin' ),
            'search_items'       => __( 'Search Sponsors', 'wpgenius-event-plugin' ),
            'not_found'          => __( 'No sponsors found.', 'wpgenius-event-plugin' ),
            'not_found_in_trash' => __( 'No sponsors found in Trash.', 'wpgenius-event-plugin' ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Sponsor post type.', 'wpgenius-event-plugin' ),
            'public'             => $public,
            'exclude_from_search'=> ! $public,
            'publicly_queryable' => $public,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-money',
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'         => array( 'event_taxonomy' ),
            'has_archive'        => $public,
            'rewrite'            => array( 'slug' => 'sponsors' ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
        );

        register_post_type( 'sponsor', $args );

        // Sponsor Level Taxonomy
        $level_labels = array(
            'name'              => _x( 'Sponsor Levels', 'taxonomy general name', 'wpgenius-event-plugin' ),
            'singular_name'     => _x( 'Sponsor Level', 'taxonomy singular name', 'wpgenius-event-plugin' ),
            'search_items'      => __( 'Search Sponsor Levels', 'wpgenius-event-plugin' ),
            'all_items'         => __( 'All Sponsor Levels', 'wpgenius-event-plugin' ),
            'parent_item'       => __( 'Parent Sponsor Level', 'wpgenius-event-plugin' ),
            'parent_item_colon' => __( 'Parent Sponsor Level:', 'wpgenius-event-plugin' ),
            'edit_item'         => __( 'Edit Sponsor Level', 'wpgenius-event-plugin' ),
            'update_item'       => __( 'Update Sponsor Level', 'wpgenius-event-plugin' ),
            'add_new_item'      => __( 'Add New Sponsor Level', 'wpgenius-event-plugin' ),
            'new_item_name'     => __( 'New Sponsor Level Name', 'wpgenius-event-plugin' ),
            'menu_name'         => __( 'Sponsor Levels', 'wpgenius-event-plugin' ),
        );

        $level_args = array(
            'hierarchical'      => true,
            'labels'            => $level_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'sponsor-level' ),
        );

        register_taxonomy( 'sponsor_level', array( 'sponsor' ), $level_args );
    }

    public function add_meta_boxes() {
        add_meta_box( 'sponsor_details', __( 'Sponsor Details', 'wpgenius-event-plugin' ), array( $this, 'render_meta_box' ), 'sponsor', 'normal', 'high' );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'wpgenius_save_sponsor_details', 'wpgenius_sponsor_nonce' );

        $event_terms    = get_terms( array( 'taxonomy' => 'event_taxonomy', 'hide_empty' => false ) );
        $selected_event = wp_get_post_terms( $post->ID, 'event_taxonomy', array( 'fields' => 'ids' ) );

        // Sponsor Level meta
        $selected_level = get_post_meta( $post->ID, '_sponsor_level', true );
        $levels = array( 'Platinum', 'Gold', 'Silver', 'Bronze' );

        // Event Field
        echo '<p><label for="sponsor_event">' . __( 'Event:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<select name="sponsor_event" id="sponsor_event">';
        foreach ( $event_terms as $event ) {
            $selected = ( in_array( $event->term_id, $selected_event ) ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $event->term_id ) . '" ' . $selected . '>' . esc_html( $event->name ) . '</option>';
        }
        echo '</select></p>';
        echo '<p class="description">' . __( 'Select the event this sponsor is associated with.', 'wpgenius-event-plugin' ) . '</p>';

        // Sponsor Level Field
        echo '<p><label for="sponsor_level">' . __( 'Sponsor Level:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<select name="sponsor_level" id="sponsor_level">';
        foreach ( $levels as $level ) {
            $selected = ( $selected_level == $level ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $level ) . '" ' . $selected . '>' . esc_html( $level ) . '</option>';
        }
        echo '</select></p>';
        echo '<p class="description">' . __( 'Select the sponsor level for this sponsor.', 'wpgenius-event-plugin' ) . '</p>';
    }

    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['wpgenius_sponsor_nonce'] ) || ! wp_verify_nonce( $_POST['wpgenius_sponsor_nonce'], 'wpgenius_save_sponsor_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        if ( isset( $_POST['sponsor_event'] ) ) {
            wp_set_post_terms( $post_id, intval( $_POST['sponsor_event'] ), 'event_taxonomy' );
        }

        if ( isset( $_POST['sponsor_level'] ) ) {
            update_post_meta( $post_id, '_sponsor_level', sanitize_text_field( $_POST['sponsor_level'] ) );
        } else {
            delete_post_meta( $post_id, '_sponsor_level' );
        }
    }

    public function add_custom_columns( $columns ) {
        $new_columns = array();
        foreach ( $columns as $key => $title ) {
            if ( $key == 'title' ) {
                $new_columns['featured_image'] = __( 'Image', 'wpgenius-event-plugin' );
                $new_columns['sponsor_event']  = __( 'Event', 'wpgenius-event-plugin' );
            }
            $new_columns[ $key ] = $title;
        }
        return $new_columns;
    }

    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'featured_image':
                if ( has_post_thumbnail( $post_id ) ) {
                    echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
                }
                break;

            case 'sponsor_event':
                $events = wp_get_post_terms( $post_id, 'event_taxonomy', array( 'fields' => 'names' ) );
                echo esc_html( implode( ', ', $events ) );
                break;
        }
    }
}
WPGenius_Sponsor::init();
