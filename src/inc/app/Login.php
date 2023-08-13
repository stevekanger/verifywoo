<?php

namespace VerifyWoo\Inc\App;

use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\DB;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Utils;

defined('ABSPATH') || exit;

class Login {
    public static function on_login($errors, $login, $pass) {
        $user = DB::get_row('SELECT ID from ' . DB::prefix('users') . ' where user_login = %s OR user_email = %s', [$login, $login]);

        if (!$user) {
            $errors->add('username-login-error', __('Username does not exist.', 'verifywoo'));
            return $errors;
        }

        $data = DB::get_row('SELECT * from ' . DB::prefix(PLUGIN_PREFIX) . ' where user_id = %d', $user['ID']);
        $verified = $data['verified'] ?? null;
        Utils::debug($data);

        if (!$verified) {
            $errors->add('email-verification-error', __('User email is not verified. If you need to you can resend the verification link <a href="' . Router::get_page_permalink('email-verification') . '">Click to resend</a>', 'verifywoo'));
            return $errors;
        }

        return $errors;
    }
}
