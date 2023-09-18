<?php

namespace verifywoo\utils;

use const verifywoo\PLUGIN_PREFIX;
use verifywoo\core\DB;

defined('ABSPATH') || exit;

class App {
    public static function email_verified($email) {
        $query = DB::get_var('SELECT verified from ' . DB::table() . ' where email = %s', [$email]);
        return $query['verified'] ?? 0;
    }

    public static function id_verified($ID) {
        $query = DB::get_var('SELECT verified from ' . DB::table() . ' where user_id = %d', [$ID]);
        return $query['verified'] ?? 0;
    }
}