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