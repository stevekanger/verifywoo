<?php

namespace VerifyWoo\Controllers\Frontend\Events;

defined('ABSPATH') || exit;

class InfoChange {
    public static function on_email_change($send, $original_data, $updated_data) {
        // $updated_data['user_email'] = $original_data['user_email'];
        return $updated_data;
    }
}
