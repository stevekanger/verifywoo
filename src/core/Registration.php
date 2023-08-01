<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Registration {
    public static function on_redirect($user) {
        wp_logout();
        wp_redirect(home_url() . '/verification/?action=success&msg=You have been successfully registered. Please check the email you provided to verifiy your email address.');
        exit;
    }

    public static function on_registration($customer_id, $new_customer_data, $password_generated) {
        $token = Auth::create_token();
        $timestamp = time();

        update_user_meta($customer_id, PLUGIN_PREFIX . '_token', $token);
        update_user_meta($customer_id, PLUGIN_PREFIX . '_data', [
            'verified' => false,
            'token_timestamp' => $timestamp,
            'prev_email' => null,
        ]);
    }

    public static function append_registration_email($content, $user, $email) {
        $token = get_user_meta($user->ID, PLUGIN_PREFIX . '_token', true);
        $activation_link = '<a href="' . get_home_url() . '/verification?action=verify&token=' . $token . '">Verify Your Email Address</a>';
        $new_content = 'Please verify your email address ' . $activation_link;
        return $new_content . "\n\n" . $content;
    }

    public static function add_retype_password_input() {
        Template::include('input-retype-password');
    }

    public static function validate_registration($errors, $username, $email) {
        extract($_POST);
        if (strcmp($password, $password2) !== 0) {
            $errors->add('registration-error', __('Passwords do not match.', 'woocommerce'));
        }
        return $errors;
    }

    public static function set_min_password_strength() {
        return 1;
    }
}
