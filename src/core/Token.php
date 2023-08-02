<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Token {
    static function create() {
        $token = bin2hex(random_bytes(32));
        $exists = get_users([
            'meta_key' => PLUGIN_PREFIX . '_token',
            'meta_value' => $token
        ]);

        return count($exists) ? self::create() : $token;
    }

    static function verify($token) {
        if (!$token) return false;

        $data = DB::get_data_by_token($token);
        if (!$data) return false;

        $timestamp = $data->timestamp ?? null;
        if (!$timestamp || !self::verify_timestamp($timestamp)) return false;

        DB::update([
            'token' => null,
            'timestamp' => null,
            'verified' => true,
        ], [
            'user_id' => $data->user_id
        ]);

        return true;
    }

    static function verify_timestamp($timestamp) {
        return (strtotime('+1 hour', $timestamp) - strtotime('now')) > 0 ? true : false;
    }
}
