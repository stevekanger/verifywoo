<?php

namespace VerifyWoo\Controllers\App;

use VerifyWoo\Core\DB;
use VerifyWoo\Core\Mail;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Token;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class InfoChange {
    public static function on_email_change($errors, $user_new_details) {
        $notice_errors = wc_get_notices()['error'] ?? null;
        if ($notice_errors) return $errors;

        $user_id = $user_new_details->ID;
        $email = $user_new_details->user_email;
        $user = get_user_by('ID', $user_new_details->ID);

        if (!$user) {
            wc_clear_notices();
            Router::redirect_to_permalink('email-verification', [
                'action' => 'error',
                'msg' => urlencode(__('We could not find your account data. Check your details and try again.', 'verifywoo'))
            ]);
        }

        if ($user->user_email === $email) {
            return $errors;
        }

        wc_clear_notices();

        $exists = DB::get_row('SELECT email from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', $email);

        if ($exists) Router::redirect_to_permalink('email-verification', [
            'action' => 'error',
            'msg' => urlencode(__('A user with that email already exists. Either sign in or resend verification.', 'verifywoo'))
        ]);

        $token = Token::create();

        $inserted = DB::insert([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Token::set_exp(),
            'email' => $email
        ], ['%d', '%s', '%d', '%s']);

        if (!$inserted) Router::redirect_to_permalink('email-verificaiton', [
            'action' => 'error',
            'msg' => urlencode(__('There was in issue creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo'))
        ]);

        $mail = Mail::send_token($email, $token);

        if (!$mail)  Router::redirect_to_permalink('email-verification', [
            'action' => 'error',
            'msg' => urlencode(__('There was an issue sending your verification token. Please try again.', 'verifywoo'))
        ]);

        Router::redirect_to_permalink('email-verification', [
            'action' => 'success',
            'msg' => urlencode(__('Please check the email you provided to verify your email address. Your old email will remain active until you verify your new email address.', 'verifywoo'))
        ]);
    }
}
