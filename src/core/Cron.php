<?php

namespace verifywoo\core;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Cron {
    public static $jobs = [
        'delete_users' => [
            'hook' => PLUGIN_PREFIX . '_automatically_delete_unverified_users',
            'fn' => [self::class, 'delete_unverified_users'],
        ]
    ];

    public static function init() {
        foreach (self::$jobs as $job) {
            add_action($job['hook'], $job['fn']);
        }

        add_filter('cron_schedules', function ($schedules) {
            $schedules['minute'] = array(
                'interval' => 60,
                'display'  => esc_html__('Every Minute'),
            );
            return $schedules;
        });
    }

    public static function deactivate() {
        self::unschedule_all();
    }

    public static function schedule($hook, $frequency) {
        if (wp_next_scheduled($hook))  return;

        wp_schedule_event(time(), $frequency, $hook);
    }

    public static function unschedule($hook) {
        $timestamp = wp_next_scheduled($hook);
        wp_unschedule_event($timestamp, $hook);
    }

    public static function unschedule_all() {
        foreach (self::$jobs as $job) {
            self::unschedule($job['hook']);
        }
    }

    public static function delete_unverified_users() {
        Users::delete_unverified();
    }

    public static function update_option_automatically_delete_unverified_users($old_value, $new_value, $option_name) {
        if ($new_value === 'on') {
            $frequency = get_option(PLUGIN_PREFIX . '_automatically_delete_unverified_users_frequency');
            self::schedule(PLUGIN_PREFIX . '_automatically_delete_unverified_users', $frequency);
            return;
        }

        self::unschedule(PLUGIN_PREFIX . '_automatically_delete_unverified_users');
    }

    public static function update_option_automatically_delete_unverified_users_frequency($old_value, $new_value, $option_name) {
        if (!get_option(PLUGIN_PREFIX . '_automatically_delete_unverified_users')) return;

        self::unschedule(PLUGIN_PREFIX . '_automatically_delete_unverified_users');
        self::schedule(PLUGIN_PREFIX . '_automatically_delete_unverified_users', $new_value);
    }
}
