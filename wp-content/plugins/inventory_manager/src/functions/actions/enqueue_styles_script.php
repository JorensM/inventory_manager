<?php

    /**
     * Enqueue styles and scripts
     */
    function enqueue_styles_scripts() {
        wp_enqueue_style('inv-mgr-style', plugin_dir_url(__FILE__) . '../../style.css');
    }
    //Add action for enqueueing styles/scripts
    add_action( 'admin_enqueue_scripts', 'enqueue_styles_scripts' );