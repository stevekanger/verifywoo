<?php

namespace VerifyWoo\Controllers\Frontend\Events;

use VerifyWoo\Core\Token;
use VerifyWoo\Core\DB;
use VerifyWoo\Core\Template;
use VerifyWoo\Core\Mail;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Session;

defined('ABSPATH') || exit;

class Registration {
    public static function on_registration($customer_id, $new_customer_data, $password_generated) {
        $token = Token::create();
        $email = $new_customer_data['user_email'];

        $inserted = DB::insert([
            'user_id' => $customer_id,
            'token' => $token,
            'timestamp' => time(),
            'email' => $email
        ], ['%d', '%s', '%d', '%s']);

        if (!$inserted) {
            Session::set([
                'registration_redirect' => '/verification/?action=error&msg' . urlencode('Your account was created successfully but there was an issue creating your verification information. Please contact your site administrator to verify your email.')
            ]);
        }

        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);

        $mail = Mail::send($email, get_bloginfo('title') . ' - Verify your email', $mailContent);
        if (!$mail) {
            Session::set([
                'registration_redirect' => '/verification/?action=error&msg' . urlencode('Your account was created successfully but there was an issue sending your verification link. Please contact your site administrator to verify your email.')
            ]);
        }
    }

    public static function on_registration_redirect($user) {
        wp_logout();
        Router::redirect(Session::get_item('registration_redirect'));
    }

    public static function on_registration_password_validation($errors, $username, $email) {
        extract($_POST);
        if (strcmp($password, $password2) !== 0) {
            $errors->add('registration-error', __('Passwords do not match.', 'woocommerce'));
        }
        return $errors;
    }

    public static function add_retype_password_input() {
        Template::include('partials/input-retype-password');
    }
}
