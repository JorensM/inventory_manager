<?php

require_once __DIR__ . '/../../classes/Admin_Notice.php';

//Add Admin_Notice class to admin_notices
add_action('admin_notices', [new Admin_Notice(), 'displayAdminNotice']);