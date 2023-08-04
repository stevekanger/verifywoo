<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Token {
    static function create() {
        $token = bin2hex(random_bytes(32));
        $query = DB::query('SELECT token from ' . DB::prefix(PLUGIN_PREFIX) . ' where token = %s', $token);
        return $query ? self::create() : $token;
    }

    static function verify($token) {
        if (!$token) return null;

        $data = DB::get_row('SELECT * from ' . DB::prefix(PLUGIN_PREFIX) . ' where token = %s', $token);
        if (!$data) return null;

        $timestamp = $data->timestamp ?? null;
        if (!$timestamp || !self::verify_timestamp($timestamp)) return null;

        return $data;
    }

    static function verify_timestamp($timestamp) {
        return (strtotime('+1 hour', $timestamp) - strtotime('now')) > 0 ? true : false;
    }
}
