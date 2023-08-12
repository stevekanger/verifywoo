<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Session {
    public static function init() {
        if (!session_id()) {
            session_start();
        }

        $_SESSION[PLUGIN_PREFIX] = [
            'registration_action' => [
                'action' => 'success',
                'msg' => urlencode(__('You have successfully registered. Please check the email you provided to verify your email address.', 'verifywoo'))
            ]
        ];
    }

    public static function get_item($item) {
        return $_SESSION[PLUGIN_PREFIX][$item] ?? null;
    }

    public static function get() {
        return $_SESSION[PLUGIN_PREFIX] ?? null;
    }

    public static function set($arr) {
        $_SESSION[PLUGIN_PREFIX] = array_merge($_SESSION[PLUGIN_PREFIX], $arr);
    }
}
