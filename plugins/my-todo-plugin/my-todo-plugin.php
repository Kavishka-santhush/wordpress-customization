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
// 3. Admin Page එකේ සම්පූර්ණ CRUD (Add, Edit, Update Status, Delete) Logic එක
function my_todo_admin_page_html() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';

    // 1. Task එකක් අලුතින් ඇඩ් කිරීම (Create)
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

    // 2. Task එකක් Update කිරීම (Edit / Change Status)
    if ( isset( $_POST['my_todo_update'] ) && check_admin_referer( 'my_todo_action_nonce', 'my_todo_nonce_field' ) ) {
        $task_id    = intval( $_POST['task_id'] );
        $task_name  = sanitize_text_field( $_POST['task_name'] );
        $task_status = sanitize_text_field( $_POST['task_status'] );

        if ( ! empty( $task_name ) ) {
            $wpdb->update(
                $table_name,
                array(
                    'task'   => $task_name,
                    'status' => $task_status
                ),
                array( 'id' => $task_id )
            );
            echo '<div class="updated"><p>Task updated successfully!</p></div>';
        }
    }

    // 3. Task එකක් Delete කිරීම (Delete)
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'delete' && isset( $_GET['id'] ) ) {
        $task_id = intval( $_GET['id'] );
        $wpdb->delete( $table_name, array( 'id' => $task_id ) );
        echo '<div class="updated"><p>Task deleted successfully!</p></div>';
    }

    // 4. Edit කරන අතරතුර අදාළ Task එකේ විස්තර ලබාගැනීම
    $edit_task = null;
    if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && isset( $_GET['id'] ) ) {
        $edit_id = intval( $_GET['id'] );
        $edit_task = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $edit_id ) );
    }

    // Database එකෙන් සියලුම To-Dos ලබාගැනීම (Read)
    $todos = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );
    ?>
    <div class="wrap">
        <h1>To-Do Application Manager</h1>
        
        <?php if ( $edit_task ) : ?>
            <!-- Task එක Edit කරන Form එක -->
            <form method="POST" action="<?php echo admin_url( 'admin.php?page=my-todo-plugin' ); ?>" style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccd0d4; max-width: 600px;">
                <?php wp_nonce_field( 'my_todo_action_nonce', 'my_todo_nonce_field' ); ?>
                <input type="hidden" name="task_id" value="<?php echo $edit_task->id; ?>">
                <h2>Edit Task #<?php echo $edit_task->id; ?></h2>
                <p>
                    <label>Task Description:</label><br>
                    <input type="text" name="task_name" value="<?php echo esc_attr( $edit_task->task ); ?>" required style="width: 100%; padding: 8px;">
                </p>
                <p>
                    <label>Status:</label><br>
                    <select name="task_status" style="width: 100%; padding: 8px;">
                        <option value="Pending" <?php selected( $edit_task->status, 'Pending' ); ?>>Pending</option>
                        <option value="In Progress" <?php selected( $edit_task->status, 'In Progress' ); ?>>In Progress</option>
                        <option value="Completed" <?php selected( $edit_task->status, 'Completed' ); ?>>Completed</option>
                    </select>
                </p>
                <p>
                    <input type="submit" name="my_todo_update" value="Update Task" class="button button-primary">
                    <a href="<?php echo admin_url( 'admin.php?page=my-todo-plugin' ); ?>" class="button" style="margin-left: 10px;">Cancel</a>
                </p>
            </form>
        <?php else : ?>
            <!-- අලුතින් Task එකක් ඇඩ් කරන Form එක -->
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
        <?php endif; ?>

        <h2>Existing Tasks</h2>
        <table class="wp-list-table widefat fixed striped" style="max-width: 850px; margin-top: 15px;">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Task</th>
                    <th style="width: 130px;">Status</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $todos ) ) : ?>
                    <?php foreach ( $todos as $todo ) : ?>
                        <tr>
                            <td><?php echo $todo->id; ?></td>
                            <td><?php echo esc_html( $todo->task ); ?></td>
                            <td><strong><?php echo esc_html( $todo->status ); ?></strong></td>
                            <td>
                                <a href="<?php echo admin_url( 'admin.php?page=my-todo-plugin&action=edit&id=' . $todo->id ); ?>" style="color: #0073aa; margin-right: 10px;">Edit</a>
                                <a href="<?php echo admin_url( 'admin.php?page=my-todo-plugin&action=delete&id=' . $todo->id ); ?>" onclick="return confirm('Are you sure you want to delete this task?');" style="color: #a00;">Delete</a>
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


// 4. Frontend එකේ To-Do ඩේටා පෙන්වීම සඳහා Shortcode එකක් සෑදීම
function my_todo_display_shortcode() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'todos';

    // ඩේටබේස් එකෙන් ටාස්ක් ටික ලබාගැනීම
    $todos = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC" );

    // Output එක එකතු කිරීම
    $output = '<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; border-radius: 8px; max-width: 600px; margin: 20px 0;">';
    $output .= '<h3 style="margin-top: 0; color: #333;">📝 My Task List (From Database)</h3>';

    if ( ! empty( $todos ) ) {
        $output .= '<ul style="list-style: none; padding: 0;">';
        foreach ( $todos as $todo ) {
            $status_color = ( $todo->status == 'Completed' ) ? 'color: green;' : 'color: #e67e22;';
            $output .= '<li style="background: #fff; margin-bottom: 8px; padding: 10px 15px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">';
            $output .= '<span>' . esc_html( $todo->task ) . '</span>';
            $output .= '<span style="font-size: 0.85rem; font-weight: bold; ' . $status_color . '">' . esc_html( $todo->status ) . '</span>';
            $output .= '</li>';
        }
        $output .= '</ul>';
    } else {
        $output .= '<p>No tasks found in the database.</p>';
    }

    $output .= '</div>';
    return $output;
}
add_shortcode( 'display_todos', 'my_todo_display_shortcode' );