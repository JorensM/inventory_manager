<?php

    //Path to activity log file
    $activity_log_dir = __DIR__ . '/../../activity_log.txt';

    /**
     * Log activity into the activity log that is in the dashboard
     * 
     * @param string $label label of message
     * @param string $message message
     * 
     * @return void
     */
    function log_activity($label, $message) {
        global $activity_log_dir;

        //Get timestamp
        $timestamp = '[' . gmdate('d-M-Y H:i:s') . ' UTC]';

        //String that will be added to the log
        $str = PHP_EOL."$timestamp <b>{$label}: </b>{$message}";

        //Add string to file
        file_put_contents($activity_log_dir, $str, FILE_APPEND);
    }

    /**
     * Clears activity log file
     */
    function clear_activity_log() {
        global $activity_log_dir;
        file_put_contents($activity_log_dir, '');
    }