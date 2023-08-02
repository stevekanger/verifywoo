<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Registration {
    public static function on_redirect($user) {
        wp_logout();
        wp_redirect(home_url() . '/verification/?action=success&msg=You have been successfully registered. Please check the email you provided to verifiy your email address.');
        exit;
    }

    public static function on_registration($customer_id, $new_customer_data, $password_generated) {
        $token = Token::create();

        DB::insert([
            'user_id' => $customer_id,
            'token' => $token,
            'timestamp' => time()
        ], ['%d', '%s', '%d']);
    }

    public static function append_registration_email($content, $user, $email) {
        $token = DB::get_data_by_user_id($user->ID)->token;
        $activation_uri = get_home_url() . '/verification?action=verification-registration&token=' . $token;
        $new_content = 'Before you can log in to your account please verify your email address. Click here <a href="' . $activation_uri . '">' . $activation_uri . '</a>';
        return $new_content . "\n\n" . 'This link will be valid for 1 hour.' . "\n\n" . $content;
    }

    public static function add_retype_password_input() {
        Template::include('partials/input-retype-password');
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
