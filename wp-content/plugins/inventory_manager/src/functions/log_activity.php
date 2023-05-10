<?php


function log_activity($label, $message) {

    $log_dir = __DIR__ . '/../activity_log.txt';

    $timestamp = '[' . gmdate('d-M-Y H:i:s') . ' UTC]';
    //$timestamp = gmdate('d-M-Y H:i:s', strtotime('2012-06-28 23:55'));

    $str = PHP_EOL."$timestamp <b>{$label}: </b>{$message}";

    file_put_contents($log_dir, $str, FILE_APPEND);
}

function clear_activity_log() {
    $log_dir = __DIR__ . '/../activity_log.txt';

    file_put_contents($log_dir, '');
}