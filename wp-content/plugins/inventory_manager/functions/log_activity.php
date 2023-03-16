<?php


function log_activity($label, $message) {
    error_log('logging activity');

    $log_dir = __DIR__ . '/../activity_log.txt';

    $str = PHP_EOL."<b>{$label}: </b>{$message}";

    error_log($log_dir);
    file_put_contents($log_dir, $str, FILE_APPEND);
}