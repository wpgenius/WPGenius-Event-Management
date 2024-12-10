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
 * Class WPGenius_Speaker
 *
 * Registers the Speaker post type and its meta boxes.
 */
class WPGenius_Speaker {

	protected static $instance;

	public static function init() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new WPGenius_Speaker();
		}
		return self::$instance;
	}

    /**
     * Initialize hooks.
     */
    private function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
		add_filter( 'manage_speaker_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_speaker_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
	}

    /**
     * Register the Speaker post type.
     */
    public function register_post_type() {
        // Get visibility setting.
        $visibility = get_option( 'wpgenius_cpt_visibility', array( 'speaker' => 'public' ) );
        $public = ( isset( $visibility['speaker'] ) && $visibility['speaker'] === 'backend' ) ? false : true;

        $labels = array(
            'name'               => _x( 'Speakers', 'post type general name', 'wpgenius-event-plugin' ),
            'singular_name'      => _x( 'Speaker', 'post type singular name', 'wpgenius-event-plugin' ),
            'menu_name'          => _x( 'Speakers', 'admin menu', 'wpgenius-event-plugin' ),
            'name_admin_bar'     => _x( 'Speaker', 'add new on admin bar', 'wpgenius-event-plugin' ),
            'add_new'            => _x( 'Add New', 'speaker', 'wpgenius-event-plugin' ),
            'add_new_item'       => __( 'Add New Speaker', 'wpgenius-event-plugin' ),
            'new_item'           => __( 'New Speaker', 'wpgenius-event-plugin' ),
            'edit_item'          => __( 'Edit Speaker', 'wpgenius-event-plugin' ),
            'view_item'          => __( 'View Speaker', 'wpgenius-event-plugin' ),
            'all_items'          => __( 'All Speakers', 'wpgenius-event-plugin' ),
            'search_items'       => __( 'Search Speakers', 'wpgenius-event-plugin' ),
            'parent_item_colon'  => __( 'Parent Speakers:', 'wpgenius-event-plugin' ),
            'not_found'          => __( 'No speakers found.', 'wpgenius-event-plugin' ),
            'not_found_in_trash' => __( 'No speakers found in Trash.', 'wpgenius-event-plugin' ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Speaker post type.', 'wpgenius-event-plugin' ),
            'public'             => $public,
            'exclude_from_search'=> ! $public,
            'publicly_queryable' => $public,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-microphone',
            'supports'           => array( 'title', 'editor', 'thumbnail' ),
            'taxonomies'         => array( 'event_taxonomy', 'speaker_group' ),
            'has_archive'        => $public,
            'rewrite'            => array( 'slug' => 'speakers' ),
            'capability_type'    => 'post',
            'map_meta_cap'       => true,
        );

        register_post_type( 'speaker', $args );

        // Register the Group taxonomy.
        $group_labels = array(
            'name'              => _x( 'Groups', 'taxonomy general name', 'wpgenius-event-plugin' ),
            'singular_name'     => _x( 'Group', 'taxonomy singular name', 'wpgenius-event-plugin' ),
            'search_items'      => __( 'Search Groups', 'wpgenius-event-plugin' ),
            'all_items'         => __( 'All Groups', 'wpgenius-event-plugin' ),
            'parent_item'       => __( 'Parent Group', 'wpgenius-event-plugin' ),
            'parent_item_colon' => __( 'Parent Group:', 'wpgenius-event-plugin' ),
            'edit_item'         => __( 'Edit Group', 'wpgenius-event-plugin' ),
            'update_item'       => __( 'Update Group', 'wpgenius-event-plugin' ),
            'add_new_item'      => __( 'Add New Group', 'wpgenius-event-plugin' ),
            'new_item_name'     => __( 'New Group Name', 'wpgenius-event-plugin' ),
            'menu_name'         => __( 'Groups', 'wpgenius-event-plugin' ),
        );

        $group_args = array(
            'hierarchical'      => true,
            'labels'            => $group_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'group' ),
        );

        register_taxonomy( 'speaker_group', array( 'speaker' ), $group_args );
    }

    /**
     * Add meta boxes for the Speaker post type.
     */
    public function add_meta_boxes() {
        add_meta_box( 'speaker_details', __( 'Speaker Details', 'wpgenius-event-plugin' ), array( $this, 'render_meta_box' ), 'speaker', 'normal', 'high' );
    }

    /**
     * Render the Speaker Details meta box.
     *
     * @param WP_Post $post The current post object.
     */
    public function render_meta_box( $post ) {
		// Add a nonce field for security.
		wp_nonce_field( 'wpgenius_save_speaker_details', 'wpgenius_speaker_nonce' );
	
		// Retrieve existing meta data.
		$designation      = get_post_meta( $post->ID, '_designation', true );
		$company_name     = get_post_meta( $post->ID, '_company_name', true );
		$company_logo     = get_post_meta( $post->ID, '_company_logo', true );
		$email_address    = get_post_meta( $post->ID, '_email_address', true );
		$featured_speaker = get_post_meta( $post->ID, '_featured_speaker', true );
	
		// Designation Field
		echo '<p><label for="designation">' . __( 'Designation:', 'wpgenius-event-plugin' ) . '</label>';
		echo '<input type="text" name="designation" id="designation" value="' . esc_attr( $designation ) . '"></p>';
		echo '<p class="description">' . __( 'Enter the designation/title of the speaker (e.g., Senior Developer).', 'wpgenius-event-plugin' ) . '</p>';
	
		// Company Name Field
		echo '<p><label for="company_name">' . __( 'Company Name:', 'wpgenius-event-plugin' ) . '</label>';
		echo '<input type="text" name="company_name" id="company_name" value="' . esc_attr( $company_name ) . '"></p>';
		echo '<p class="description">' . __( 'Enter the speaker’s company name.', 'wpgenius-event-plugin' ) . '</p>';
	
		// Company Logo Field
		$image = '';
		if ( $company_logo ) {
			$image = wp_get_attachment_image( $company_logo, 'medium' );
		}
	
		echo '<p><label for="company_logo">' . __( 'Company Logo:', 'wpgenius-event-plugin' ) . '</label><br>';
		echo '<input type="hidden" name="company_logo" id="company_logo" value="' . esc_attr( $company_logo ) . '">';
		echo '<button type="button" class="button upload_company_logo_button">' . __( 'Upload Logo', 'wpgenius-event-plugin' ) . '</button>';
		echo '<div class="company_logo_preview" style="margin-top:10px;">' . $image . '</div></p>';
		echo '<p class="description">' . __( 'Upload or select the company’s logo from the media library.', 'wpgenius-event-plugin' ) . '</p>';
	
		// Email Address Field
		echo '<p><label for="email_address">' . __( 'Email Address:', 'wpgenius-event-plugin' ) . '</label>';
		echo '<input type="email" name="email_address" id="email_address" value="' . esc_attr( $email_address ) . '"></p>';
		echo '<p class="description">' . __( 'Enter the speaker’s contact email address.', 'wpgenius-event-plugin' ) . '</p>';
	
		// Featured Speaker Field
		$checked = ( $featured_speaker ) ? 'checked' : '';
		echo '<p><label for="featured_speaker">';
		echo '<input type="checkbox" name="featured_speaker" id="featured_speaker" ' . $checked . '> ';
		echo __( 'Featured Speaker', 'wpgenius-event-plugin' ) . '</label></p>';
		echo '<p class="description">' . __( 'Check this box if this is a featured speaker.', 'wpgenius-event-plugin' ) . '</p>';
	}
	

    /**
     * Save the Speaker meta box data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function save_meta_boxes( $post_id ) {
        // Verify nonce.
        if ( ! isset( $_POST['wpgenius_speaker_nonce'] ) || ! wp_verify_nonce( $_POST['wpgenius_speaker_nonce'], 'wpgenius_save_speaker_details' ) ) {
            return;
        }

        // Check autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check user permissions.
        if ( ! current_user_can( 'edit_speaker', $post_id ) ) {
            return;
        }

        // Save other meta data.
        update_post_meta( $post_id, '_designation', sanitize_text_field( $_POST['designation'] ) );
        update_post_meta( $post_id, '_company_name', sanitize_text_field( $_POST['company_name'] ) );
        update_post_meta( $post_id, '_company_logo', intval( $_POST['company_logo'] ) );
        update_post_meta( $post_id, '_email_address', sanitize_email( $_POST['email_address'] ) );
        update_post_meta( $post_id, '_featured_speaker', isset( $_POST['featured_speaker'] ) ? '1' : '0' );
    }

    /**
     * Add custom columns to the Speaker post list.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public function add_custom_columns( $columns ) {
        $new_columns = array();
        foreach ( $columns as $key => $title ) {
            $new_columns[ $key ] = $title;
            if ( $key == 'title' ) {
                $new_columns['featured_image']    = __( 'Image', 'wpgenius-event-plugin' );
                $new_columns['company']           = __( 'Company', 'wpgenius-event-plugin' );
            }
        }
        return $new_columns;
    }

    /**
     * Render custom column content.
     *
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public function render_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'featured_image':
                if ( has_post_thumbnail( $post_id ) ) {
                    echo get_the_post_thumbnail( $post_id, array( 50, 50 ) );
                }
                break;

            case 'company':
                $company = get_post_meta( $post_id, '_company_name', true );
                echo esc_html( $company );
                $designation = get_post_meta( $post_id, '_designation', true );
                echo esc_html( $designation );
                break;

        }
    }
}
WPGenius_Speaker::init();
