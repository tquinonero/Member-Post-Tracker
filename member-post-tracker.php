<?php
/*
Plugin Name: Member Post Tracker
Description: Tracks read posts for logged-in users and provides a button to mark posts as read.
Version: 1.0
Author: Your Name
License: GPL v3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

// Enqueue JavaScript file
function member_post_tracker_enqueue_script() {
    wp_enqueue_script('member-post-tracker-script', plugin_dir_url(__FILE__) . 'script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'member_post_tracker_enqueue_script');

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
        }
    }
    wp_die();
}
add_action('wp_ajax_mark_post_as_read', 'mark_post_as_read');

// Display button after content
function display_mark_as_read_button($content) {
    if (is_singular(array('member-video', 'programme')) && is_user_logged_in()) {
        $post_id = get_the_ID();
        $user_id = get_current_user_id();
        $read_posts = get_user_meta($user_id, 'read_posts', true);
        $button_text = in_array($post_id, $read_posts) ? 'Read' : 'Mark as Read';
        $button_markup = '<button class="mark-as-read" data-post-id="' . $post_id . '">' . $button_text . '</button>';
        $content .= $button_markup;
    }
    return $content;
}
add_filter('the_content', 'display_mark_as_read_button');

