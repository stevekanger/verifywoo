<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_NAME;

defined('ABSPATH') || exit;

class Settings {
    static $required_woo_settings = [
        'account' => [
            'woocommerce_registration_generate_password' => [
                'value' => 'no',
                'field' => [
                    'desc' => 'When creating an account, send the new user a link to set their password (disabled by ' . PLUGIN_NAME . ' Plugin)'
                ]
            ]
        ]
    ];

    public static function configure_woocommerce_account_settings($settings) {
        foreach ($settings as $index => $field) {
            if (self::is_required_setting('account', $field['id'] ?? null)) {
                $settings[$index] = self::update_setting('account', $field);
            }
        }
        return $settings;
    }

    static function is_required_setting($settingsTab, $id) {
        return (self::$required_woo_settings[$settingsTab][$id] ?? null) ? true : false;
    }

    static function update_setting($settingsTab, $field) {
        $required = self::$required_woo_settings[$settingsTab][$field['id']];
        $current = get_option($field['id'] ?? null);
        if ($required['value'] !== $current) {
            update_option($field['id'], $required['value']);
        }
        return array_merge($field, $required['field']);
    }
}
