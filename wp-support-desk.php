<?php
/**
 * Plugin Name: WP Support Desk
 * Description: Simple support ticket system plugin .
 * Version: 1.0
 * Author: Yash Shende
 */

if (!defined('ABSPATH')) exit;

register_activation_hook(__FILE__, 'wpsd_create_table');

function wpsd_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . "wpsd_tickets";
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        status VARCHAR(20) DEFAULT 'open',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

add_shortcode('wpsd_ticket_form', 'wpsd_ticket_form_shortcode');

function wpsd_ticket_form_shortcode() {
    ob_start();

    // Success message after redirect
    if (isset($_GET['wpsd']) && $_GET['wpsd'] === 'success') {
        echo "<p style='color:green;font-weight:bold;'>✅ Ticket submitted successfully!</p>";
    }
    ?>
    <h2>Submit Support Ticket</h2>

    <form method="post">
        <?php wp_nonce_field('wpsd_submit_ticket', 'wpsd_nonce'); ?>

        <p>
            <input type="text" name="name" placeholder="Your Name" required style="width:100%; padding:10px;">
        </p>

        <p>
            <input type="email" name="email" placeholder="Your Email" required style="width:100%; padding:10px;">
        </p>

        <p>
            <input type="text" name="subject" placeholder="Subject" required style="width:100%; padding:10px;">
        </p>

        <p>
            <textarea name="message" placeholder="Describe your issue..." required style="width:100%; padding:10px;" rows="5"></textarea>
        </p>

        <p>
            <button type="submit" name="wpsd_submit_ticket" style="padding:10px 20px; cursor:pointer;">
                Submit Ticket
            </button>
        </p>
    </form>
    <?php

    return ob_get_clean();
}

add_action('init', 'wpsd_handle_ticket_submission');

function wpsd_handle_ticket_submission() {
    if (!isset($_POST['wpsd_submit_ticket'])) return;

    if (!isset($_POST['wpsd_nonce']) || !wp_verify_nonce($_POST['wpsd_nonce'], 'wpsd_submit_ticket')) {
        wp_die("Security check failed!");
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "wpsd_tickets";

    $name    = sanitize_text_field($_POST['name']);
    $email   = sanitize_email($_POST['email']);
    $subject = sanitize_text_field($_POST['subject']);
    $message = sanitize_textarea_field($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        return;
    }

    $wpdb->insert($table_name, [
        'name'    => $name,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
        'status'  => 'open'
    ]);

    $redirect_url = add_query_arg('wpsd', 'success', wp_get_referer());
    wp_safe_redirect($redirect_url);
    exit;
}


// 4) Admin Menu Page: Support Tickets
add_action('admin_menu', 'wpsd_add_admin_menu');

function wpsd_add_admin_menu() {
    add_menu_page(
        'Support Tickets',
        'Support Tickets',
        'manage_options',
        'wpsd-tickets',
        'wpsd_admin_tickets_page',
        'dashicons-sos',
        25
    );
}

// 5) Admin Page Content (Tickets Table)
function wpsd_admin_tickets_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }

    global $wpdb;
    $table_name = $wpdb->prefix . "wpsd_tickets";

    // ✅ Pagination setup
$per_page = 10;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

// ✅ Base query
$where = "WHERE 1=1";
$params = [];

// ✅ Search filter
if (!empty($search)) {
    $where .= " AND (name LIKE %s OR email LIKE %s OR subject LIKE %s)";
    $like = '%' . $wpdb->esc_like($search) . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

// ✅ Status filter
if (!empty($status_filter)) {
    $where .= " AND status = %s";
    $params[] = $status_filter;
}

// ✅ Total count query
$count_sql = "SELECT COUNT(*) FROM $table_name $where";
$total_items = (int) $wpdb->get_var($wpdb->prepare($count_sql, $params));
$total_pages = ceil($total_items / $per_page);

// ✅ Tickets query with LIMIT + OFFSET
$data_sql = "SELECT * FROM $table_name $where ORDER BY created_at DESC LIMIT %d OFFSET %d";
$params_for_data = array_merge($params, [$per_page, $offset]);

$tickets = $wpdb->get_results($wpdb->prepare($data_sql, $params_for_data));

    echo '<div class="wrap">';
    echo '<h1>Support Tickets</h1>';
    // ✅ Search + Filter values
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

// ✅ Filter Form
echo '<form method="get" style="margin: 15px 0; display:flex; gap:10px; align-items:center;">';

echo '<input type="hidden" name="page" value="wpsd-tickets">';

echo '<input type="text" name="s" placeholder="Search name/email/subject..." value="' . esc_attr($search) . '" style="width:280px;">';

echo '<select name="status">
        <option value="">All Status</option>
        <option value="open" ' . selected($status_filter, 'open', false) . '>Open</option>
        <option value="in_progress" ' . selected($status_filter, 'in_progress', false) . '>In Progress</option>
        <option value="resolved" ' . selected($status_filter, 'resolved', false) . '>Resolved</option>
      </select>';

echo '<button type="submit" class="button button-primary">Filter</button>';

echo '<a href="' . esc_url(admin_url('admin.php?page=wpsd-tickets')) . '" class="button">Reset</a>';

echo '</form>';


    if (isset($_GET['wpsd_msg'])) {
    if ($_GET['wpsd_msg'] === 'status_updated') {
        echo '<div class="notice notice-success is-dismissible"><p>✅ Ticket status updated.</p></div>';
    }
    if ($_GET['wpsd_msg'] === 'ticket_deleted') {
        echo '<div class="notice notice-success is-dismissible"><p>🗑 Ticket deleted successfully.</p></div>';
    }
}


    if (empty($tickets)) {
        echo '<p>No tickets found.</p>';
        echo '</div>';
        return;
    }

    echo '<table class="widefat fixed striped">';
    echo '<thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Subject</th>
            <th>Message</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
      </thead>';


    echo '<tbody>';
    foreach ($tickets as $ticket) {
        echo '<tr>';
        echo '<td>' . esc_html($ticket->id) . '</td>';
        echo '<td>' . esc_html($ticket->name) . '</td>';
        echo '<td>' . esc_html($ticket->email) . '</td>';
        echo '<td>' . esc_html($ticket->subject) . '</td>';
        echo '<td>' . esc_html($ticket->message) . '</td>';
        echo '<td>
        <form method="post" style="display:flex; gap:6px; align-items:center;">
            ' . wp_nonce_field('wpsd_update_status', 'wpsd_status_nonce', true, false) . '
            <input type="hidden" name="ticket_id" value="' . esc_attr($ticket->id) . '">

            <select name="status">
                <option value="open" ' . selected($ticket->status, 'open', false) . '>Open</option>
                <option value="in_progress" ' . selected($ticket->status, 'in_progress', false) . '>In Progress</option>
                <option value="resolved" ' . selected($ticket->status, 'resolved', false) . '>Resolved</option>
            </select>

            <button type="submit" name="wpsd_update_status" class="button button-primary">
                Update
            </button>
        </form>
      </td>';
        $delete_url = wp_nonce_url(
    admin_url('admin.php?page=wpsd-tickets&wpsd_delete_ticket=' . $ticket->id),
    'wpsd_delete_ticket_' . $ticket->id
);

echo '<td>
        <a href="' . esc_url($delete_url) . '" class="button button-secondary"
           onclick="return confirm(\'Are you sure you want to delete this ticket?\')">
           Delete
        </a>
      </td>';

        echo '<td>' . esc_html($ticket->created_at) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';

    echo '</table>';
    // ✅ Pagination Links
if ($total_pages > 1) {
    echo '<div style="margin-top:15px;">';

    $base_url = admin_url('admin.php?page=wpsd-tickets');

    // keep search & filter in pagination links
    if (!empty($search)) {
        $base_url = add_query_arg('s', $search, $base_url);
    }
    if (!empty($status_filter)) {
        $base_url = add_query_arg('status', $status_filter, $base_url);
    }

    // Previous button
    if ($current_page > 1) {
        echo '<a class="button" href="' . esc_url(add_query_arg('paged', $current_page - 1, $base_url)) . '">⬅ Prev</a> ';
    }

    echo '<span style="margin:0 10px;">Page <b>' . esc_html($current_page) . '</b> of <b>' . esc_html($total_pages) . '</b></span>';

    // Next button
    if ($current_page < $total_pages) {
        echo ' <a class="button" href="' . esc_url(add_query_arg('paged', $current_page + 1, $base_url)) . '">Next ➡</a>';
    }

    echo '</div>';
}

    echo '</div>';
}
// 6) Handle Admin Actions (Update Status + Delete Ticket)
add_action('admin_init', 'wpsd_handle_admin_actions');

function wpsd_handle_admin_actions() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $table_name = $wpdb->prefix . "wpsd_tickets";

    // ✅ Update Status
    if (isset($_POST['wpsd_update_status'])) {

        if (!isset($_POST['wpsd_status_nonce']) || !wp_verify_nonce($_POST['wpsd_status_nonce'], 'wpsd_update_status')) {
            wp_die("Security check failed!");
        }

        $ticket_id = intval($_POST['ticket_id']);
        $status = sanitize_text_field($_POST['status']);

        $allowed_status = ['open', 'in_progress', 'resolved'];
        if (!in_array($status, $allowed_status)) {
            $status = 'open';
        }

        $wpdb->update(
            $table_name,
            ['status' => $status],
            ['id' => $ticket_id]
        );

        wp_safe_redirect(admin_url('admin.php?page=wpsd-tickets&wpsd_msg=status_updated'));
        exit;
    }

    // ✅ Delete Ticket
    if (isset($_GET['wpsd_delete_ticket'])) {

        $ticket_id = intval($_GET['wpsd_delete_ticket']);

        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'wpsd_delete_ticket_' . $ticket_id)) {
            wp_die("Security check failed!");
        }

        $wpdb->delete($table_name, ['id' => $ticket_id]);

        wp_safe_redirect(admin_url('admin.php?page=wpsd-tickets&wpsd_msg=ticket_deleted'));
        exit;
    }
}
