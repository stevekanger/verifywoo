<?php

namespace verifywoo\core;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Cron {
    public static $jobs = [
        PLUGIN_PREFIX . '_delete_users'
    ];

    public static function schedule() {
    }

    public static function unschedule($hook) {
        $timestamp = wp_next_scheduled($hook);
        wp_unschedule_event($timestamp, $hook);
    }

    public static function unschedule_all() {
        foreach (self::$jobs as $job) {
            self::unschedule($job);
        }
    }

    public static function delete_users() {
    }

    public static function update_option($option_name, $old_value, $new_value) {
    }
}
