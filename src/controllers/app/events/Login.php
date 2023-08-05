<?php

namespace VerifyWoo\Controllers\App\Events;

use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\DB;

defined('ABSPATH') || exit;

class Login {
    public static function on_login($errors, $login, $pass) {
        $user = DB::get_row('SELECT ID from ' . DB::prefix('users') . ' where user_login = %s OR user_email = %s', [$login, $login]);

        if (!$user) {
            $errors->add('username-login-error', __('Username does not exist.', 'verifywoo'));
            return $errors;
        }

        $data = DB::get_row('SELECT * from ' . DB::prefix(PLUGIN_PREFIX) . ' where user_id = %d', $user->ID);
        $verified = $data->verified;

        if (!$verified) {
            $errors->add('email-verification-error', __('User email is not verified. If you need to you can resend the verification link <a href="' . home_url() . '/verification/?action=send">Click to resend</a>', 'verifywoo'));
            return $errors;
        }

        return $errors;
    }
}
