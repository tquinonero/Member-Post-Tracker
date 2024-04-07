<?php
/*
Plugin Name: Member Post Tracker
Description: Tracks read posts for logged-in users and provides a button to mark posts as read.
Version: 1.1
Author: Toni Quiñonero
License: GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Enqueue JavaScript file and WordPress AJAX script
function member_post_tracker_enqueue_script() {
    // Enqueue your custom JavaScript file
    wp_enqueue_script('member-post-tracker-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
    //Enqueue styles for the button
    wp_enqueue_style('member-post-tracker-style', plugin_dir_url(__FILE__) . 'style.css');
    // Enqueue WordPress AJAX script
    wp_enqueue_script('wp-ajax-js');
    wp_localize_script('member-post-tracker-script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'member_post_tracker_enqueue_script');

function member_post_tracker_enqueue_admin_script($hook) {
    if ($hook != 'settings_page_member-post-tracker-settings') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script('member-post-tracker-admin-script', plugin_dir_url(__FILE__) . 'admin-script.js', array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'member_post_tracker_enqueue_admin_script');


function member_post_tracker_settings_page() {
    add_options_page(
        'Member Post Tracker Settings',
        'Member Post Tracker Settings',
        'manage_options',
        'member-post-tracker-settings',
        'member_post_tracker_settings_page_markup'
    );
}
add_action('admin_menu', 'member_post_tracker_settings_page');

function member_post_tracker_settings_page_markup() {
    ?>
    <div class="wrap">
        <h2>Member Post Tracker Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('member_post_tracker_settings'); ?>
            <?php do_settings_sections('member-post-tracker-settings'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function member_post_tracker_settings_init() {
    register_setting('member_post_tracker_settings', 'member_post_tracker_post_type');
    register_setting('member_post_tracker_settings', 'member_post_tracker_button_image'); // New setting for button image
    add_settings_section(
        'member_post_tracker_settings_section',
        'Post Type Settings',
        'member_post_tracker_settings_section_markup',
        'member-post-tracker-settings'
    );
    add_settings_field(
        'member_post_tracker_post_type_field',
        'Enter Post Type',
        'member_post_tracker_post_type_field_markup',
        'member-post-tracker-settings',
        'member_post_tracker_settings_section'
    );
    add_settings_field(
        'member_post_tracker_button_image_field',
        'Upload Button Image',
        'member_post_tracker_button_image_field_markup',
        'member-post-tracker-settings',
        'member_post_tracker_settings_section'
    );
}
add_action('admin_init', 'member_post_tracker_settings_init');

function member_post_tracker_settings_section_markup() {
    echo '<p>Enter the custom post type for the Member Post Tracker functionality.</p>';
}

function member_post_tracker_post_type_field_markup() {
    $post_type = get_option('member_post_tracker_post_type');
    echo '<input type="text" name="member_post_tracker_post_type" value="' . esc_attr($post_type) . '" />';
}

function member_post_tracker_button_image_field_markup() {
    $button_image = get_option('member_post_tracker_button_image');
    echo '<input type="text" id="member_post_tracker_button_image" name="member_post_tracker_button_image" value="' . esc_attr($button_image) . '" />';
    echo '<input type="button" id="member_post_tracker_upload_button" class="button" value="Upload Image" />';
}

// AJAX handler to mark posts as read
function mark_post_as_read() {
    if (isset($_POST['post_id'])) {
        $post_id = intval($_POST['post_id']);
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $read_posts = get_user_meta($user_id, 'read_posts', true);
            if (!$read_posts) {
                $read_posts = array();
            }
            if (!in_array($post_id, $read_posts)) {
                $read_posts[] = $post_id;
                update_user_meta($user_id, 'read_posts', $read_posts);
            }
            echo 'success';
        }
    }
    wp_die();
}
add_action('wp_ajax_mark_post_as_read', 'mark_post_as_read');
add_action('wp_ajax_nopriv_mark_post_as_read', 'mark_post_as_read');


function display_mark_as_read_button($content) {
    $post_type = get_option('member_post_tracker_post_type');
    $button_image = get_option('member_post_tracker_button_image'); // Get button image URL
    if (!empty($post_type) && is_singular($post_type) && is_user_logged_in()) {
        global $post;
        $post_id = $post->ID;
        // Add your button markup here with data-post-id attribute and custom image
        $button_markup = '<button class="mark-as-read-button" data-post-id="' . $post_id . '"><img src="' . $button_image . '" alt="Mark as Read" />&nbsp;<span>Mark as Read</span></button>';
        $content .= $button_markup;
    }
    return $content;
}

add_filter('the_content', 'display_mark_as_read_button');
