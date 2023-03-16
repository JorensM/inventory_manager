<?php

function enqueue_styles_scripts() {
    wp_enqueue_style('inv-mgr-style', plugin_dir_url(__FILE__) . '../../style.css');
}

add_action( 'admin_enqueue_scripts', 'enqueue_styles_scripts' );