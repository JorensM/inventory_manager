<?php

require_once(__DIR__ . "/../../classes/Admin_Notice.php");

add_action('admin_notices', [new Admin_Notice(), 'displayAdminNotice']);