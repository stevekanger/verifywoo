<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Auth {
    public static function check_verification_on_login($errors, $login, $pass) {
        $user = get_user_by('login', $login);
        if (!$user) {
            $errors->add('login-error', __('Username does not exist.', 'woocommerce'));
            return $errors;
        }

        $data = get_user_meta($user->ID, PLUGIN_PREFIX . '_data', true);
        $verified = $data['verified'] ?? null;
        if (!$verified) {
            $errors->add('login-error', __('User email is not verified. Resend email verification <a href="' . home_url() . '/verification/?action=resend">Click to resend</a>', 'woocommerce'));
            return $errors;
        }

        return $errors;
    }

    static function create_token() {
        $token = bin2hex(random_bytes(32));
        $exists = get_users([
            'meta_key' => PLUGIN_PREFIX . '_token',
            'meta_value' => $token
        ]);

        return count($exists) ? self::create_token() : $token;
    }

    static function verify_token() {
        $token = $_GET['token'];
        if (!$token) return false;

        $user = get_users([
            'meta_key' => PLUGIN_PREFIX . '_token',
            'meta_value' => $token
        ])[0] ?? null;
        if (!$user) return false;

        $userData = get_user_meta($user->ID, PLUGIN_PREFIX . '_data', true);
        $timestamp = $userData['token_timestamp'] ?? null;

        if (!$timestamp || !self::verify_token_timestamp($timestamp)) return false;

        update_user_meta($user->ID, PLUGIN_PREFIX . '_token', null);
        update_user_meta($user->ID, PLUGIN_PREFIX . '_data', array_merge($userData, [
            'verified' => true,
            'token_timestamp' => null,
        ]));

        return true;
    }

    static function verify_token_timestamp($timestamp) {
        return (strtotime('+1 hour', $timestamp) - strtotime('now')) > 0 ? true : false;
    }
}
