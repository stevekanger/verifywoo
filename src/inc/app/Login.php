<?php

namespace verifywoo\inc\app;

use verifywoo\core\Router;
use verifywoo\core\Users;

defined('ABSPATH') || exit;

class Login {
    public static function woocommerce_process_login_errors($errors, $login, $pass) {
        $user = Users::get_one($login, 'login');

        if (!$user) {
            $errors->add('username-login-error', __('Username does not exist.', 'verifywoo'));
            return $errors;
        }

        $verified = $user['verified'] ?? null;
        if (!$verified) {
            $errors->add('email-verification-error', __('User email is not verified. If you need to you can resend the verification link <a href="' . Router::get_page_permalink('email-verification') . '">Click to resend</a>', 'verifywoo'));
            return $errors;
        }

        return $errors;
    }
}
