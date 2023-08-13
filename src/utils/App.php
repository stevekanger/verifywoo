<?php

namespace VerifyWoo\Utils;

use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\DB;

defined('ABSPATH') || exit;

class App {
    public static function email_verified($email) {
        $query = DB::get_var('SELECT verified from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', [$email]);
        return $query['verified'] ?? 0;
    }

    public static function id_verified($ID) {
        $query = DB::get_var('SELECT verified from ' . DB::prefix(PLUGIN_PREFIX) . ' where user_id = %d', [$ID]);
        return $query['verified'] ?? 0;
    }
}
