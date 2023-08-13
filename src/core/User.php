<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class User {
    public static function register($user_id, $email) {
        $token = Token::create();

        $inserted = DB::insert([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Token::set_exp(),
            'email' => $email
        ], ['%d', '%s', '%d', '%s']);

        if (!$inserted) {
            Session::set([
                'registration_action' => [
                    'action' => 'error',
                    'msg' => urlencode(__('Your account was created successfully but there was an issue creating your verification information. Please contact your site administrator to verify your email.', 'verifywoo'))
                ]
            ]);
        }

        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);

        $mail = Mail::send($email, get_bloginfo('title') . ' - Verify your email', $mailContent);
        if (!$mail) {
            Session::set([
                'registration_action' => [
                    'action' => 'error',
                    'msg' => urlencode(__('Your account was created successfully but there was an issue sending your verification link. Please contact your site administrator to verify your email.', 'verifywoo'))
                ]
            ]);
        }
        wp_logout();
    }

    public static function update_email($user_id, $email) {
        return wp_update_user([
            'ID' => $user_id,
            'user_email' => $email
        ]);
    }

    public static function verify($user_id) {
        return DB::update([
            'token' => null,
            'expires' => null,
            'verified' => true,
        ], [
            'user_id' => $user_id
        ]);
    }

    public static function unverify($user_id) {
        return DB::update([
            'verified' => false,
        ], [
            'user_id' => $user_id
        ]);
    }
}
