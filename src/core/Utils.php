<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Utils {
    public static function debug(...$items) {
        $backtrace = debug_backtrace();
        $caller = array_shift($backtrace);
        if (true === \WP_DEBUG) {
            $line = '--- DEBUG --- in file: ' . $caller['file'] . ' On line: ' . strval($caller['line']);
            error_log($line);
            foreach ($items as $item) {
                if (is_array($item) || is_object($item)) {
                    error_log(print_r($item, true));
                } else {
                    error_log($item ?? '');
                }
            }
        }
    }
}
