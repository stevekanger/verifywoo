<?php

namespace verifywoo\core;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Token {
    static function create() {
        $token = bin2hex(random_bytes(32));
        $query = DB::query('SELECT token from ' . DB::table(PLUGIN_PREFIX) . ' where token = %s', $token);
        return $query ? self::create() : $token;
    }

    static function set_exp() {
        return strtotime('+' . get_option(PLUGIN_PREFIX . '_verification_expiration_length'), time());
    }

    static function verify($token_exp) {
        return time() < $token_exp;
    }
}
