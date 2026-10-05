<?php
// Theme Setup සහ Styles load කිරීම
function my_custom_theme_scripts() {
    // style.css එක load කිරීම
    wp_enqueue_style( 'my-custom-theme-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'my_custom_theme_scripts' );