<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Utils {
    public static function debug(...$items) {
        if (true === \WP_DEBUG) {
            foreach ($items as $item) {
                if (is_array($item) || is_object($item)) {
                    error_log(print_r($item, true));
                } else {
                    error_log($item);
                }
            }
        }
    }
}
