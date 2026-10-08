<?php
/**
 * Plugin Name: My To-Do Plugin
 * Description: A custom CRUD To-Do application plugin managed via WP Admin and displayed on frontend.
 * Version: 1.0
 * Author: Santhush
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. ප්ලගීන් එක Activate කරන විට Custom Database Table එක සෑදීම
function my_todo_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos'; // උදාහරණයක් ලෙස: wp_todos
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        task varchar(255) NOT NULL,
        status varchar(50) DEFAULT 'Pending' NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook( __FILE__, 'my_todo_create_table' );




// 2. Admin Dashboard එකට මෙනු එකක් එකතු කිරීම
function my_todo_add_admin_menu() {
    add_menu_page(
        'To-Do Manager',          // Page Title
        'To-Do App',              // Menu Title
        'manage_options',         // Capability
        'my-todo-plugin',         // Menu Slug
        'my_todo_admin_page_html',// Callback Function
        'dashicons-yes-alt',      // Icon
        25                        // Position
    );
}
add_action( 'admin_menu', 'my_todo_add_admin_menu' );

// 3. Admin Page එකේ HTML සහ CRUD Logic එක
function my_todo_admin_page_html() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';

    // Form එක Submit කළ විට (Create Task)
    if ( isset( $_POST['my_todo_submit'] ) && check_admin_referer( 'my_todo_action_nonce', 'my_todo_nonce_field' ) ) {
        $task_name = sanitize_text_field( $_POST['task_name'] );
        if ( ! empty( $task_name ) ) {
            $wpdb->insert(
                $table_name,
                array(
                    'task' => $task_name,
                    'status' => 'Pending'
                )
            );
            echo '<div class="updated"><p>Task added successfully!</p></div>';
        }
    }

    // Delete Task එකක් ක්ලික් කළ විට (Delete Task)
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['id'] ) ) {
        $task_id = intval( $_GET['id'] );
        $wpdb->delete( $table_name, array( 'id' => $task_id ) );
        echo '<div class="updated"><p>Task deleted successfully!</p></div>';
    }

    // Database එකෙන් සියලුම To-Dos ලබාගැනීම (Read)
    $todos = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );
    ?>
    <div class="wrap">
        <h1>To-Do Application Manager</h1>
        
        <!-- Task එකක් ඇඩ් කරන Form එක -->
        <form method="POST" action="" style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccd0d4; max-width: 600px;">
            <?php wp_nonce_field( 'my_todo_action_nonce', 'my_todo_nonce_field' ); ?>
            <h2>Add New Task</h2>
            <p>
                <input type="text" name="task_name" placeholder="Enter task description..." required style="width: 100%; padding: 8px;">
            </p>
            <p>
                <input type="submit" name="my_todo_submit" value="Add Task" class="button button-primary">
            </p>
        </form>

        <h2>Existing Tasks</h2>
        <table class="wp-list-table widefat fixed striped" style="max-width: 800px; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Task</th>
                    <th style="width: 100px;">Status</th>
                    <th style="width: 100px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $todos ) ) : ?>
                    <?php foreach ( $todos as $todo ) : ?>
                        <tr>
                            <td><?php echo $todo->id; ?></td>
                            <td><?php echo esc_html( $todo->task ); ?></td>
                            <td><?php echo esc_html( $todo->status ); ?></td>
                            <td>
                                <a href="<?php echo admin_url( 'admin.php?page=my-todo-plugin&action=delete&id=' . $todo->id ); ?>" onclick="return confirm('Are you sure?');" style="color: #a00;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No tasks found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}