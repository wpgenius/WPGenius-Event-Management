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
 * Class WPGenius_Session
 *
 * Registers the Session post type and its meta boxes.
 * Taxonomies: event_taxonomy (shared), session_track, event_category
 * Meta Fields: Event (via event_taxonomy), Date, Time, Session Length, Session Type, Links (Slides/Video), Speakers
 */
class WPGenius_Session {

    protected static $instance;

    public static function init() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new WPGenius_Session();
        }
        return self::$instance;
    }

    /**
     * Private constructor for singleton pattern.
     */
    private function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
        add_filter( 'manage_session_posts_columns', array( $this, 'add_custom_columns' ) );
        add_action( 'manage_session_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
    }

    /**
     * Register the Session post type and its custom taxonomies.
     */
    public function register_post_type() {
        // Check visibility setting.
        $visibility = get_option( 'wpgenius_cpt_visibility', array( 'session' => 'public' ) );
        $public = ( isset( $visibility['session'] ) && $visibility['session'] === 'backend' ) ? false : true;

        $labels = array(
            'name'               => _x( 'Sessions', 'post type general name', 'wpgenius-event-plugin' ),
            'singular_name'      => _x( 'Session', 'post type singular name', 'wpgenius-event-plugin' ),
            'menu_name'          => _x( 'Sessions', 'admin menu', 'wpgenius-event-plugin' ),
            'name_admin_bar'     => _x( 'Session', 'add new on admin bar', 'wpgenius-event-plugin' ),
            'add_new'            => _x( 'Add New', 'session', 'wpgenius-event-plugin' ),
            'add_new_item'       => __( 'Add New Session', 'wpgenius-event-plugin' ),
            'new_item'           => __( 'New Session', 'wpgenius-event-plugin' ),
            'edit_item'          => __( 'Edit Session', 'wpgenius-event-plugin' ),
            'view_item'          => __( 'View Session', 'wpgenius-event-plugin' ),
            'all_items'          => __( 'All Sessions', 'wpgenius-event-plugin' ),
            'search_items'       => __( 'Search Sessions', 'wpgenius-event-plugin' ),
            'not_found'          => __( 'No sessions found.', 'wpgenius-event-plugin' ),
            'not_found_in_trash' => __( 'No sessions found in Trash.', 'wpgenius-event-plugin' ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Session post type.', 'wpgenius-event-plugin' ),
            'public'             => $public,
            'exclude_from_search'=> ! $public,
            'publicly_queryable' => $public,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-schedule',
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'         => array( 'event_taxonomy' ), 
            'has_archive'        => $public,
            'rewrite'            => array( 'slug' => 'sessions' ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
        );

        register_post_type( 'session', $args );

        // Register Tracks Taxonomy
        $track_labels = array(
            'name'              => _x( 'Tracks', 'taxonomy general name', 'wpgenius-event-plugin' ),
            'singular_name'     => _x( 'Track', 'taxonomy singular name', 'wpgenius-event-plugin' ),
            'search_items'      => __( 'Search Tracks', 'wpgenius-event-plugin' ),
            'all_items'         => __( 'All Tracks', 'wpgenius-event-plugin' ),
            'parent_item'       => __( 'Parent Track', 'wpgenius-event-plugin' ),
            'parent_item_colon' => __( 'Parent Track:', 'wpgenius-event-plugin' ),
            'edit_item'         => __( 'Edit Track', 'wpgenius-event-plugin' ),
            'update_item'       => __( 'Update Track', 'wpgenius-event-plugin' ),
            'add_new_item'      => __( 'Add New Track', 'wpgenius-event-plugin' ),
            'new_item_name'     => __( 'New Track Name', 'wpgenius-event-plugin' ),
            'menu_name'         => __( 'Tracks', 'wpgenius-event-plugin' ),
        );

        $track_args = array(
            'hierarchical'      => true,
            'labels'            => $track_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'track' ),
        );

        register_taxonomy( 'session_track', array( 'session' ), $track_args );

        // Register Event Category Taxonomy
        $event_cat_labels = array(
            'name'              => _x( 'Event Categories', 'taxonomy general name', 'wpgenius-event-plugin' ),
            'singular_name'     => _x( 'Event Category', 'taxonomy singular name', 'wpgenius-event-plugin' ),
            'search_items'      => __( 'Search Event Categories', 'wpgenius-event-plugin' ),
            'all_items'         => __( 'All Event Categories', 'wpgenius-event-plugin' ),
            'parent_item'       => __( 'Parent Event Category', 'wpgenius-event-plugin' ),
            'parent_item_colon' => __( 'Parent Event Category:', 'wpgenius-event-plugin' ),
            'edit_item'         => __( 'Edit Event Category', 'wpgenius-event-plugin' ),
            'update_item'       => __( 'Update Event Category', 'wpgenius-event-plugin' ),
            'add_new_item'      => __( 'Add New Event Category', 'wpgenius-event-plugin' ),
            'new_item_name'     => __( 'New Event Category Name', 'wpgenius-event-plugin' ),
            'menu_name'         => __( 'Event Categories', 'wpgenius-event-plugin' ),
        );

        $event_cat_args = array(
            'hierarchical'      => true,
            'labels'            => $event_cat_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'event-category' ),
        );

        register_taxonomy( 'event_category', array( 'session' ), $event_cat_args );
    }

    /**
     * Add meta boxes for Session.
     */
    public function add_meta_boxes() {
        add_meta_box( 'session_details', __( 'Session Details', 'wpgenius-event-plugin' ), array( $this, 'render_meta_box' ), 'session', 'normal', 'high' );
    }

    /**
     * Render the Session Details meta box inline (no separate template).
     *
     * Fields: Event, Date, Time, Session Length, Session Type, Link to Slides, Link to Video, Speakers
     */
    public function render_meta_box( $post ) {
        wp_nonce_field( 'wpgenius_save_session_details', 'wpgenius_session_nonce' );

        $event_terms      = get_terms( array( 'taxonomy' => 'event_taxonomy', 'hide_empty' => false ) );
        $selected_event   = wp_get_post_terms( $post->ID, 'event_taxonomy', array( 'fields' => 'ids' ) );
        $session_date     = get_post_meta( $post->ID, '_session_date', true );
        $session_time     = get_post_meta( $post->ID, '_session_time', true );
        $session_length   = get_post_meta( $post->ID, '_session_length', true );
        $session_type     = get_post_meta( $post->ID, '_session_type', true );
        $link_slides      = get_post_meta( $post->ID, '_link_slides', true );
        $link_video       = get_post_meta( $post->ID, '_link_video', true );
        $session_speakers = get_post_meta( $post->ID, '_session_speakers', true );

        // Get all speakers
        $speakers = get_posts( array(
            'post_type'      => 'speaker',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        // Event field
        echo '<p><label for="session_event">' . __( 'Event:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<select name="session_event" id="session_event">';
        foreach ( $event_terms as $event ) {
            $selected = ( in_array( $event->term_id, $selected_event ) ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $event->term_id ) . '" ' . $selected . '>' . esc_html( $event->name ) . '</option>';
        }
        echo '</select></p>';
        echo '<p class="description">' . __( 'Select the event this session belongs to.', 'wpgenius-event-plugin' ) . '</p>';

        // Date
        echo '<p><label for="session_date">' . __( 'Date:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<input type="date" name="session_date" id="session_date" value="' . esc_attr( $session_date ) . '"></p>';

        // Time
        echo '<p><label for="session_time">' . __( 'Time:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<input type="time" name="session_time" id="session_time" value="' . esc_attr( $session_time ) . '"></p>';

        // Session Length
        echo '<p><label for="session_length">' . __( 'Session Length (minutes):', 'wpgenius-event-plugin' ) . '</label>';
        echo '<input type="number" name="session_length" id="session_length" value="' . esc_attr( $session_length ) . '"></p>';

        // Session Type
        $session_types = array( 'Regular Session', 'Break Session' );
        echo '<p><label for="session_type">' . __( 'Session Type:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<select name="session_type" id="session_type">';
        foreach ( $session_types as $type ) {
            $selected = ( $session_type == $type ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $type ) . '" ' . $selected . '>' . esc_html( $type ) . '</option>';
        }
        echo '</select></p>';

        // Link to Slides
        echo '<p><label for="link_slides">' . __( 'Link to Slides:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<input type="url" name="link_slides" id="link_slides" value="' . esc_attr( $link_slides ) . '"></p>';

        // Link to Video
        echo '<p><label for="link_video">' . __( 'Link to Video:', 'wpgenius-event-plugin' ) . '</label>';
        echo '<input type="url" name="link_video" id="link_video" value="' . esc_attr( $link_video ) . '"></p>';

        // Speakers
        echo '<p><label for="session_speakers">' . __( 'Speakers:', 'wpgenius-event-plugin' ) . '</label><br>';
        echo '<select name="session_speakers[]" id="session_speakers" multiple style="min-width:200px;">';
        foreach ( $speakers as $speaker ) {
            $selected = ( is_array( $session_speakers ) && in_array( $speaker->ID, $session_speakers ) ) ? 'selected' : '';
            echo '<option value="' . esc_attr( $speaker->ID ) . '" ' . $selected . '>' . esc_html( $speaker->post_title ) . '</option>';
        }
        echo '</select></p>';
        echo '<p class="description">' . __( 'Select one or more speakers for this session.', 'wpgenius-event-plugin' ) . '</p>';
    }

    /**
     * Save the Session meta box data.
     */
    public function save_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['wpgenius_session_nonce'] ) || ! wp_verify_nonce( $_POST['wpgenius_session_nonce'], 'wpgenius_save_session_details' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save Event
        if ( isset( $_POST['session_event'] ) ) {
            wp_set_post_terms( $post_id, intval( $_POST['session_event'] ), 'event_taxonomy' );
        }

        // Save Date/Time/Length/Type/Links
        if ( isset( $_POST['session_date'] ) ) {
            update_post_meta( $post_id, '_session_date', sanitize_text_field( $_POST['session_date'] ) );
        }

        if ( isset( $_POST['session_time'] ) ) {
            update_post_meta( $post_id, '_session_time', sanitize_text_field( $_POST['session_time'] ) );
        }

        if ( isset( $_POST['session_length'] ) ) {
            update_post_meta( $post_id, '_session_length', intval( $_POST['session_length'] ) );
        }

        if ( isset( $_POST['session_type'] ) ) {
            update_post_meta( $post_id, '_session_type', sanitize_text_field( $_POST['session_type'] ) );
        }

        if ( isset( $_POST['link_slides'] ) ) {
            update_post_meta( $post_id, '_link_slides', esc_url_raw( $_POST['link_slides'] ) );
        }

        if ( isset( $_POST['link_video'] ) ) {
            update_post_meta( $post_id, '_link_video', esc_url_raw( $_POST['link_video'] ) );
        }

        // Save Speakers
        if ( isset( $_POST['session_speakers'] ) ) {
            update_post_meta( $post_id, '_session_speakers', array_map( 'intval', $_POST['session_speakers'] ) );
        } else {
            delete_post_meta( $post_id, '_session_speakers' );
        }
    }

    /**
     * Add custom columns to the Session post list.
     */
    public function add_custom_columns( $columns ) {
        $new_columns = array();
        foreach ( $columns as $key => $title ) {
            if ( $key == 'title' ) {
                $new_columns['session_event']         = __( 'Event', 'wpgenius-event-plugin' );
                $new_columns['session_date_time']     = __( 'Date & Time', 'wpgenius-event-plugin' );
                $new_columns['session_track']         = __( 'Track', 'wpgenius-event-plugin' );
                $new_columns['session_event_category']= __( 'Event Category', 'wpgenius-event-plugin' );
                $new_columns['session_speakers']      = __( 'Speakers', 'wpgenius-event-plugin' );
            }
            $new_columns[ $key ] = $title;
        }
        return $new_columns;
    }

    /**
     * Render custom column content.
     */
    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'session_event':
                $events = wp_get_post_terms( $post_id, 'event_taxonomy', array( 'fields' => 'names' ) );
                echo esc_html( implode( ', ', $events ) );
                break;

            case 'session_date_time':
                $date = get_post_meta( $post_id, '_session_date', true );
                $time = get_post_meta( $post_id, '_session_time', true );
                echo esc_html( $date . ' ' . $time );
                break;

            case 'session_track':
                $tracks = wp_get_post_terms( $post_id, 'session_track', array( 'fields' => 'names' ) );
                echo esc_html( implode( ', ', $tracks ) );
                break;

            case 'session_event_category':
                $categories = wp_get_post_terms( $post_id, 'event_category', array( 'fields' => 'names' ) );
                echo esc_html( implode( ', ', $categories ) );
                break;

            case 'session_speakers':
                $speakers = get_post_meta( $post_id, '_session_speakers', true );
                if ( ! empty( $speakers ) && is_array( $speakers ) ) {
                    $speaker_names = array_map( 'get_the_title', $speakers );
                    echo esc_html( implode( ', ', $speaker_names ) );
                }
                break;
        }
    }
}

// Initialize the singleton class
WPGenius_Session::init();
