<?php

namespace verifywoo\inc\app;

use verifywoo\core\Template;
use verifywoo\core\Router;
use verifywoo\core\Session;
use verifywoo\core\Users;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Registration {
    public static function on_registration($user_id, $user_data,) {
        $email = $user_data['user_email'];
        Users::register($user_id, $email);
    }

    public static function woocommerce_registration_auth_new_customer($new_customer) {
        Router::redirect('permalink', 'email-verification', Session::get_item('registration_redirect'));
    }

    public static function on_registration_password_validation($errors, $username, $email) {
        $include_retype_password = get_option(PLUGIN_PREFIX . '_include_retype_password');
        if (!$include_retype_password) return;

        if (strcmp($_POST['password'], $_POST['password2']) !== 0) {
            $errors->add('registration-error', __('Your passwords do not match.', 'verifywoo'));
        }
        return $errors;
    }

    public static function include_retype_password_input() {
        $include_retype_password = get_option(PLUGIN_PREFIX . '_include_retype_password');
        if (!$include_retype_password) return;

        Template::include('app/partials/input-retype-password');
    }
}
