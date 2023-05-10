<?php

    /**
     * Returns sanitized id of current page
     * Replaces dashes with underscores, and makes all letters lowercase
     * 
     * @return string sanitized page id
     */
    function get_sanitized_page_id() {
        $page_id = get_current_screen()->id;

        $sanitized_page_id = strtolower($page_id);
        $sanitized_page_id = str_replace('-', '_', $page_id);

        return $sanitized_page_id;
    }