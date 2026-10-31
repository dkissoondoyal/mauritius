<?php
/**
 * Plugin Name: Civic Consultation Starter (Experimental)
 * Description: A minimal, reusable, moderated public consultation form. Pilot only; not a production security certification.
 * Version: 0.1.0
 * Requires PHP: 7.4
 * License: GPL-2.0-or-later
 */
if (!defined('ABSPATH')) { exit; }

/**
 * Place [civic_consultation] on a published WordPress page with comments enabled.
 * Submissions are ordinary WordPress comments held for manual moderation.
 */
add_shortcode('civic_consultation', function () {
    if (!is_singular() || !is_main_query()) { return ''; }
    $post_id = get_queried_object_id();
    if (!$post_id || !comments_open($post_id)) {
        return '<p>Consultation submissions are currently closed.</p>';
    }
    $out = '<section class="civic-consultation" aria-label="Public consultation">';
    if (isset($_GET['civic_status']) && sanitize_key(wp_unslash($_GET['civic_status'])) === 'pending') {
        $out .= '<p role="status">Thank you. Your proposal has been received for moderation.</p>';
    }
    $out .= '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
    $out .= '<input type="hidden" name="action" value="civic_consultation_submit">';
    $out .= '<input type="hidden" name="civic_post_id" value="' . esc_attr($post_id) . '">';
    $out .= wp_nonce_field('civic_consultation_' . $post_id, 'civic_nonce', true, false);
    $out .= '<p><label>Public display name <input required maxlength="80" name="civic_name" autocomplete="name"></label></p>';
    $out .= '<p><label>Email (private, for moderation) <input required type="email" maxlength="160" name="civic_email" autocomplete="email"></label></p>';
    $out .= '<p><label>Clause or topic <input required maxlength="160" name="civic_clause"></label></p>';
    $out .= '<p><label>Proposed amendment <textarea required maxlength="4000" rows="6" name="civic_proposal"></textarea></label></p>';
    $out .= '<p><label>Reason for the proposal <textarea required maxlength="2000" rows="4" name="civic_reason"></textarea></label></p>';
    $out .= '<p><label><input required type="checkbox" name="civic_consent" value="1"> I consent to storage of my submission for moderation. My display name and proposal may be published after approval; my email will not be displayed. See the site privacy policy.</label></p>';
    $out .= '<p style="display:none" aria-hidden="true"><label>Leave blank <input tabindex="-1" autocomplete="off" name="civic_website"></label></p>';
    $out .= '<p><button type="submit">Submit for moderation</button></p></form></section>';
    return $out;
});

function civic_consultation_submit() {
    $post_id = isset($_POST['civic_post_id']) ? absint($_POST['civic_post_id']) : 0;
    if (!$post_id || get_post_status($post_id) !== 'publish' || !comments_open($post_id)) {
        wp_die('This consultation is not accepting submissions.', '', array('response' => 403));
    }
    $nonce = isset($_POST['civic_nonce']) ? sanitize_text_field(wp_unslash($_POST['civic_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'civic_consultation_' . $post_id)) {
        wp_die('The form has expired. Reload and try again.', '', array('response' => 403));
    }
    if (!empty($_POST['civic_website']) || empty($_POST['civic_consent']) || wp_unslash($_POST['civic_consent']) !== '1') {
        wp_die('Submission rejected.', '', array('response' => 400));
    }
    $name = sanitize_text_field(wp_unslash($_POST['civic_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['civic_email'] ?? ''));
    $clause = sanitize_text_field(wp_unslash($_POST['civic_clause'] ?? ''));
    $proposal = sanitize_textarea_field(wp_unslash($_POST['civic_proposal'] ?? ''));
    $reason = sanitize_textarea_field(wp_unslash($_POST['civic_reason'] ?? ''));
    if (!$name || strlen($name) > 320 || !is_email($email) || !$clause ||
        !$proposal || !$reason || strlen($proposal) > 16000 || strlen($reason) > 8000) {
        wp_die('Please complete the required fields within the stated limits.', '', array('response' => 400));
    }
    // A lightweight abuse throttle. Not a substitute for production anti-spam controls.
    $ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
    $key = 'civic_rate_' . hash_hmac('sha256', $ip . '|' . strtolower($email), wp_salt('auth'));
    if (get_transient($key)) {
        wp_die('Please wait before submitting again.', '', array('response' => 429));
    }
    set_transient($key, 1, MINUTE_IN_SECONDS);
    $body = "Clause or topic: {$clause}\n\nProposed amendment:\n{$proposal}\n\nReason:\n{$reason}";
    $comment_id = wp_insert_comment(array(
        'comment_post_ID' => $post_id,
        'comment_author' => $name,
        'comment_author_email' => $email,
        'comment_content' => $body,
        'comment_approved' => 0,
        'comment_type' => 'comment',
        'comment_agent' => 'Civic Consultation Starter 0.1',
    ));
    if (!$comment_id) {
        wp_die('Submission could not be saved.', '', array('response' => 500));
    }
    wp_safe_redirect(add_query_arg('civic_status', 'pending', get_permalink($post_id)) . '#civic-consultation');
    exit;
}
add_action('admin_post_nopriv_civic_consultation_submit', 'civic_consultation_submit');
add_action('admin_post_civic_consultation_submit', 'civic_consultation_submit');
