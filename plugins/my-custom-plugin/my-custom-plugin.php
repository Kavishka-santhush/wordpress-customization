<?php
/**
 * Plugin Name: My Custom Plugin
 * Description: A custom plugin to register Custom Post Types and learn plugin development.
 * Version: 1.0
 * Author: Santhush
 */

// ආරක්ෂාව සඳහා: සෘජුව මෙම ගොනුවට ඇතුළුවීම වැළැක්වීම
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Custom Post Type එකක් ලියාපදිංචි කිරීම (Registering Portfolio Custom Post Type)
function my_register_portfolio_cpt() {
    $labels = array(
        'name'               => 'Portfolios',
        'singular_name'      => 'Portfolio',
        'menu_name'          => 'Portfolios',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Portfolio',
        'edit_item'          => 'Edit Portfolio',
        'new_item'           => 'New Portfolio',
        'view_item'          => 'View Portfolio',
        'search_items'       => 'Search Portfolios',
        'not_found'          => 'No portfolios found',
        'not_found_in_trash' => 'No portfolios found in trash',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'portfolio' ),
        'capability_type'    => 'post',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, // වැදගත්: REST API එකට support කරයි (MERN stack එකකට ඩේටා ගන්න ඕනේ වුණොත් මේක අත්‍යවශ්‍යයි!)
    );

    register_post_type( 'portfolio', $args );
}
add_action( 'init', 'my_register_portfolio_cpt' );



// --- Custom Filter Hook උදාහරණයක් ---
function my_custom_content_footer( $content ) {
    // Single page වල හෝ main post loop එක ඇතුළේ පමණක් ක්‍රියාත්මක වීමට
    if ( is_single() || is_singular( 'portfolio' ) ) {
        $custom_note = '<p style="color: #e74c3c; font-style: italic; margin-top: 20px;">--- Thank you for reading this article! Built with Custom WordPress Plugin. ---</p>';
        $content .= $custom_note;
    }
    return $content;
}
add_filter( 'the_content', 'my_custom_content_footer' );


// --- Shortcode එකක් සෑදීම ---
// --- Shortcode එකක් සෑදීම ---
function my_welcome_shortcode( $atts ) {
    // Attributes ලබාගැනීම (උදා: [my_welcome_msg name="Santhush"])
    $atts = shortcode_atts( array(
        'name' => 'Developer',
    ), $atts, 'my_welcome_msg' );

    return '<div style="background: #e8f8f5; border-left: 4px solid #1abc9c; padding: 10px 15px; margin: 15px 0;">' .
           '<strong>Hello, ' . esc_html( $atts['name'] ) . '!</strong> Welcome to my custom WordPress development journey.' .
           '</div>';
}
add_shortcode( 'my_welcome_msg', 'my_welcome_shortcode' );



// --- AJAX Integration සඳහා Script එක load කිරීම ---
function my_custom_ajax_scripts() {
    // Frontend එකට script එකක් ඇතුළත් කිරීම
    wp_enqueue_script( 'my-ajax-script', plugins_url( 'js/custom-ajax.js', __FILE__ ), array('jquery'), '1.0', true );

    // JavaScript එකට WordPress Admin AJAX URL එක pass කිරීම (Security සහ routing සඳහා)
    wp_localize_script( 'my-ajax-script', 'my_ajax_obj', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ) );
}
add_action( 'wp_enqueue_scripts', 'my_custom_ajax_scripts' );

// --- AJAX Request එක Handle කිරීම (Logged-in සහ Guest දෙපිරිසටම) ---
function my_handle_ajax_portfolio_request() {

    // ඩේටා ක්වෙරි කිරීම
    $args = array(
        'post_type'      => 'portfolio',
        'posts_per_page' => 3,
    );
    $query = new WP_Query( $args );
    $portfolios = array();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $portfolios[] = array(
                'title'   => get_the_title(),
                'link'    => get_permalink(),
                'excerpt' => get_the_excerpt(),
            );
        }
        wp_reset_postdata();
    }

    // JSON response එකක් යැවීම (MERN වල res.json() වගේ)
    wp_send_json_success( $portfolios );
}
// Action hooks දෙක (WordPress වල logged-in සහ non-logged-in යූසර්ලාට ajax වැඩ කරන්න මේ දෙකම ඕනේ)
add_action( 'wp_ajax_load_more_portfolios', 'my_handle_ajax_portfolio_request' );
add_action( 'wp_ajax_nopriv_load_more_portfolios', 'my_handle_ajax_portfolio_request' );
