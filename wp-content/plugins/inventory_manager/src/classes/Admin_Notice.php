<?php

    /**
     * Custom class to display admin notices. 
     * This class allows you to display a notice even after a page refresh, 
     * for example after a post has been published/updated.
     * 
     * Usage:
     *      1. First, somewhere in your code add the following action:
     *         add_action('admin_notices', [new Admin_Notice(), 'displayAdminNotice']);
     * 
     *      2. Now, to display a notice, add the following code where you want it to be displayed:
     *         Admin_Notice::displayInfo( 'My message' );
     *         In addition to displayInfo, you can use:
     *              *displayError
     *              *displayWarning
     *              *displaySuccess
     * 
     * Code taken from this Stack Exchange answer:
     * https://wordpress.stackexchange.com/a/222027/230784
     */
    class Admin_Notice
    {
        const NOTICE_FIELD = 'my_admin_notice_message';

        public function displayAdminNotice()
        {
            $option      = get_option(self::NOTICE_FIELD);
            $message     = isset($option['message']) ? $option['message'] : false;
            $noticeLevel = ! empty($option['notice-level']) ? $option['notice-level'] : 'notice-error';

            if ($message) {
                echo "<div class='notice {$noticeLevel} is-dismissible'><p>{$message}</p></div>";
                delete_option(self::NOTICE_FIELD);
            }
        }

        public static function displayError($message)
        {
            self::updateOption($message, 'notice-error');
        }

        public static function displayWarning($message)
        {
            self::updateOption($message, 'notice-warning');
        }

        public static function displayInfo($message)
        {
            self::updateOption($message, 'notice-info');
        }

        public static function displaySuccess($message)
        {
            self::updateOption($message, 'notice-success');
        }

        protected static function updateOption($message, $noticeLevel) {
            update_option(self::NOTICE_FIELD, [
                'message' => $message,
                'notice-level' => $noticeLevel
            ]);
        }
    }