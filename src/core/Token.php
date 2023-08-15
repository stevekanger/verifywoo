<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Token {
    static function create() {
        $token = bin2hex(random_bytes(32));
        $query = DB::query('SELECT token from ' . DB::table() . ' where token = %s', $token);
        return $query ? self::create() : $token;
    }

    static function verify($token) {
        if (!$token) return null;

        $data = DB::get_row('SELECT * from ' . DB::table() . ' where token = %s', $token);
        if (!$data) return null;

        $expires = $data['expires'] ?? null;
        if (!$expires || !self::verify_exp($expires)) return null;

        return $data;
    }

    static function set_exp() {
        return strtotime('+' . get_option(PLUGIN_PREFIX . '_verification_expiration_length'), time());
    }

    static function verify_exp($expires) {
        return time() < $expires;
    }
}
