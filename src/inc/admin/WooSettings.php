<?php

namespace verifywoo\inc\admin;

use const verifywoo\PLUGIN_NAME;
use const verifywoo\PLUGIN_PREFIX;
use const verifywoo\PLUGIN_ROOT_DIR;

defined('ABSPATH') || exit;

class WooSettings {
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
            if (self::is_required_woocommerce_setting('account', $field['id'] ?? null)) {
                $settings[$index] = self::update_woocommerce_setting('account', $field);
            }
        }
        return $settings;
    }

    public static function configure_woocommerce_email_settings($settings) {
        $settings['WC_Email_Customer_New_Account'] = include PLUGIN_ROOT_DIR . '/src/inc/admin/Override_WC_Email_Customer_New_Account.php';
        return $settings;
    }

    static function is_required_woocommerce_setting($settingsTab, $id) {
        return (self::$required_woo_settings[$settingsTab][$id] ?? null) ? true : false;
    }

    static function update_woocommerce_setting($settingsTab, $field) {
        $required = self::$required_woo_settings[$settingsTab][$field['id']];
        $current = get_option($field['id'] ?? null);
        if ($required['value'] !== $current) {
            update_option($field['id'], $required['value']);
        }
        return array_merge($field, $required['field']);
    }

    public static function min_password_strength() {
        return get_option(PLUGIN_PREFIX . '_min_password_strength') ?? 3;
    }
}
