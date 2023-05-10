<?php

    /**
     * Register custom post statuses
     */
    function register_post_statuses(){
        global $text_domain;
        // global $reverbManager;
        // $reverbManager->endListing(wc_get_product(261));

        register_post_status("sold",
            [
                "label" => _x("Sold", $text_domain),
                // "public" => true,
                "show_in_admin_all_list" => true,
                "show_in_admin_status_list" => true
            ]
            );
    }
    add_action("init", "register_post_statuses");