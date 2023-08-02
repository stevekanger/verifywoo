<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class EventHandlers {
    public static function on_login($errors, $login, $pass) {
        $user = get_user_by('login', $login);
        if (!$user) {
            $errors->add('login-error', __('Username does not exist.', 'woocommerce'));
            return $errors;
        }

        $data = DB::get_data_by_user_id($user->ID);
        $verified = $data->verified;

        if (!$verified) {
            $errors->add('login-error', __('User email is not verified. If you need to you can resend the verification link <a href="' . home_url() . '/verification/?action=send-verification">Click to resend</a>', 'woocommerce'));
            return $errors;
        }

        return $errors;
    }

    public static function on_email_change($send, $original_data, $updated_data) {
        $updated_data['email'] = $original_data['email'];
        return $updated_data;
    }


    public static function on_registration($customer_id, $new_customer_data, $password_generated) {
        $token = Token::create();

        DB::insert([
            'user_id' => $customer_id,
            'token' => $token,
            'timestamp' => time()
        ], ['%d', '%s', '%d']);
    }

    public static function on_registration_redirect($user) {
        wp_logout();
        wp_redirect(home_url() . '/verification/?action=success&msg=You have been successfully registered. Please check the email you provided to verifiy your email address.');
        exit;
    }

    public static function on_registration_password_validation($errors, $username, $email) {
        extract($_POST);
        if (strcmp($password, $password2) !== 0) {
            $errors->add('registration-error', __('Passwords do not match.', 'woocommerce'));
        }
        return $errors;
    }
}
