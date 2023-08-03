<?php

namespace VerifyWoo\Controllers\Frontend\Events;

use const VerifyWoo\PLUGIN_PREFIX;

use VerifyWoo\Core\DB;

defined('ABSPATH') || exit;

class Login {
    public static function on_login($errors, $login, $pass) {
        $user = get_user_by('login', $login);
        if (!$user) {
            $errors->add('login-error', __('Username does not exist.', 'woocommerce'));
            return $errors;
        }

        $data = DB::get_row('SELECT * from ' . DB::prefix(PLUGIN_PREFIX) . ' where user_id = %d', $user->ID);
        $verified = $data->verified;

        if (!$verified) {
            $errors->add('login-error', __('User email is not verified. If you need to you can resend the verification link <a href="' . home_url() . '/verification/?action=send">Click to resend</a>', 'woocommerce'));
            return $errors;
        }
        return $errors;
    }
}
