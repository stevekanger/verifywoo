<?php

namespace verifywoo\inc\app;

use verifywoo\core\DB;
use verifywoo\core\Mail;
use verifywoo\core\Router;
use verifywoo\core\Token;

use const verifywoo\PLUGIN_PREFIX;

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
            Router::redirect('permalink', 'email-verification', [
                'view' => 'error',
                'msg' => urlencode(__('We could not find your account data. Check your details and try again.', 'verifywoo'))
            ]);
        }

        if ($user->user_email === $email) {
            return $errors;
        }

        wc_clear_notices();

        $exists = DB::get_row('SELECT email from ' . DB::table('verifywoo') . ' where email = %s', $email);

        if ($exists) Router::redirect('permalink', 'email-verification', [
            'view' => 'error',
            'msg' => urlencode(__('A user with that email already exists. Either sign in or resend verification.', 'verifywoo'))
        ]);

        $token = Token::create();

        $verifywoo_table = DB::table('verifywoo');
        $inserted = DB::insert($verifywoo_table, [
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Token::set_exp(),
            'email' => $email
        ], ['%d', '%s', '%d', '%s']);

        if (!$inserted) Router::redirect('permalink', 'email-verificaiton', [
            'view' => 'error',
            'msg' => urlencode(__('There was in issue creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo'))
        ]);

        $mail = Mail::send_token($email, $token);

        if (!$mail)  Router::redirect('permalink', 'email-verification', [
            'view' => 'error',
            'msg' => urlencode(__('There was an issue sending your verification token. Please try again.', 'verifywoo'))
        ]);

        Router::redirect('permalink', 'email-verification', [
            'view' => 'success',
            'msg' => urlencode(__('Please check the email you provided to verify your email address. Your old email will remain active until you verify your new email address.', 'verifywoo'))
        ]);
    }
}
