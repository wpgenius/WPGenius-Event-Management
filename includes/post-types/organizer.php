<?php
/**
 *
 * @class       WPGenius_Events_API
 * @author      Team WPGenius (Makarand Mane)
 * @category    Admin
 * @package     wpgenius-event-management/includes/post-types
 * @version     1.0
 */

 // Prevent direct access.
 if ( ! defined( 'ABSPATH' ) ) {
	 exit;
 }
 
 /**
  * Class WPGenius_Organizer
  *
  * Registers the Organizer post type and inline meta box for event selection.
  */
 class WPGenius_Organizer {
 
	 protected static $instance;
 
	 public static function init() {
		 if ( is_null( self::$instance ) ) {
			 self::$instance = new WPGenius_Organizer();
		 }
		 return self::$instance;
	 }
 
	 private function __construct() {
		 add_action( 'init', array( $this, 'register_post_type' ) );
		 add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		 add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		 add_filter( 'manage_organizer_posts_columns', array( $this, 'add_custom_columns' ) );
		 add_action( 'manage_organizer_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	 }
 
	 public function register_post_type() {
		 $visibility = get_option( 'wpgenius_cpt_visibility', array( 'organizer' => 'public' ) );
		 $public = ( isset( $visibility['organizer'] ) && $visibility['organizer'] === 'backend' ) ? false : true;
 
		 $labels = array(
			 'name'               => _x( 'Organizers', 'post type general name', 'wpgenius-event-plugin' ),
			 'singular_name'      => _x( 'Organizer', 'post type singular name', 'wpgenius-event-plugin' ),
			 'menu_name'          => _x( 'Organizers', 'admin menu', 'wpgenius-event-plugin' ),
			 'add_new'            => __( 'Add New', 'wpgenius-event-plugin' ),
			 'add_new_item'       => __( 'Add New Organizer', 'wpgenius-event-plugin' ),
			 'new_item'           => __( 'New Organizer', 'wpgenius-event-plugin' ),
			 'edit_item'          => __( 'Edit Organizer', 'wpgenius-event-plugin' ),
			 'view_item'          => __( 'View Organizer', 'wpgenius-event-plugin' ),
			 'all_items'          => __( 'All Organizers', 'wpgenius-event-plugin' ),
			 'search_items'       => __( 'Search Organizers', 'wpgenius-event-plugin' ),
			 'not_found'          => __( 'No organizers found.', 'wpgenius-event-plugin' ),
			 'not_found_in_trash' => __( 'No organizers found in Trash.', 'wpgenius-event-plugin' ),
		 );
 
		 $args = array(
			 'labels'             => $labels,
			 'description'        => __( 'Organizer post type.', 'wpgenius-event-plugin' ),
			 'public'             => $public,
			 'exclude_from_search'=> ! $public,
			 'publicly_queryable' => $public,
			 'show_ui'            => true,
			 'show_in_menu'       => true,
			 'menu_icon'          => 'dashicons-groups',
			 'supports'           => array( 'title', 'editor', 'thumbnail' ),
			 'taxonomies'         => array( 'event_taxonomy' ),
			 'has_archive'        => $public,
			 'rewrite'            => array( 'slug' => 'organizers' ),
			 'capability_type'    => 'post',
			 'map_meta_cap'       => true,
		 );
 
		 register_post_type( 'organizer', $args );
	 }
 
	 public function add_meta_boxes() {
		 add_meta_box( 'organizer_event', __( 'Organizer Event', 'wpgenius-event-plugin' ), array( $this, 'render_meta_box' ), 'organizer', 'normal', 'high' );
	 }
 
	 public function render_meta_box( $post ) {
		 wp_nonce_field( 'wpgenius_save_organizer_details', 'wpgenius_organizer_nonce' );
 
		 $event_terms    = get_terms( array( 'taxonomy' => 'event_taxonomy', 'hide_empty' => false ) );
		 $selected_event = wp_get_post_terms( $post->ID, 'event_taxonomy', array( 'fields' => 'ids' ) );
 
		 echo '<p><label for="organizer_event">' . __( 'Event:', 'wpgenius-event-plugin' ) . '</label>';
		 echo '<select name="organizer_event" id="organizer_event">';
		 foreach ( $event_terms as $event ) {
			 $selected = ( in_array( $event->term_id, $selected_event ) ) ? 'selected' : '';
			 echo '<option value="' . esc_attr( $event->term_id ) . '" ' . $selected . '>' . esc_html( $event->name ) . '</option>';
		 }
		 echo '</select></p>';
		 echo '<p class="description">' . __( 'Select the event this organizer is associated with.', 'wpgenius-event-plugin' ) . '</p>';
	 }
 
	 public function save_meta_boxes( $post_id ) {
		 if ( ! isset( $_POST['wpgenius_organizer_nonce'] ) || ! wp_verify_nonce( $_POST['wpgenius_organizer_nonce'], 'wpgenius_save_organizer_details' ) ) {
			 return;
		 }
		 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			 return;
		 }
		 if ( ! current_user_can( 'edit_post', $post_id ) ) {
			 return;
		 }
 
		 if ( isset( $_POST['organizer_event'] ) ) {
			 wp_set_post_terms( $post_id, intval( $_POST['organizer_event'] ), 'event_taxonomy' );
		 }
	 }
 
	 public function add_custom_columns( $columns ) {
		 $new_columns = array();
		 foreach ( $columns as $key => $title ) {
			 if ( $key == 'title' ) {
				 $new_columns['featured_image']   = __( 'Image', 'wpgenius-event-plugin' );
				 $new_columns['organizer_event']  = __( 'Event', 'wpgenius-event-plugin' );
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
 
			 case 'organizer_event':
				 $events = wp_get_post_terms( $post_id, 'event_taxonomy', array( 'fields' => 'names' ) );
				 echo esc_html( implode( ', ', $events ) );
				 break;
		 }
	 }
 }
 WPGenius_Organizer::init();
 