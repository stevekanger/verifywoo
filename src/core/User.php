<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class User {
    public static function verify_on_login($errors, $login, $pass) {
        $user = get_user_by('login', $login);
        if (!$user) {
            $errors->add('login-error', __('Username does not exist.', 'woocommerce'));
            return $errors;
        }

        $data = DB::get_data_by_user_id($user->ID);
        $verified = $data->verified;

        if (!$verified) {
            $errors->add('login-error', __('User email is not verified. Resend email verification <a href="' . home_url() . '/verification/?action=send-verification">Click to resend</a>', 'woocommerce'));
            return $errors;
        }

        return $errors;
    }
}
