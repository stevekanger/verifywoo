<?php

namespace VerifyWoo\Controllers\Frontend\Events;

use VerifyWoo\Core\DB;
use VerifyWoo\Core\Mail;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Token;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class InfoChange {
    public static function on_email_change($errors, $user_new_details) {
        $user_id = $user_new_details->ID;
        $email = $user_new_details->user_email;
        $user = get_user_by('ID', $user_new_details->ID);

        if (!$user) Router::redirect('/verification/?action=error&msg=' . urlencode(__('We could not find your account data. Check your details and try again.', 'verifywoo')));

        $exists = DB::get_row('SELECT email from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', $email);

        if ($exists) Router::redirect('/verification/?action=error&msg=' . urlencode(__('A user with that email already exists. Either sign in or resend verification.', 'verifywoo')));

        if ($user->user_email !== $email) {
            $token = Token::create();

            $inserted = DB::insert([
                'user_id' => $user_id,
                'token' => $token,
                'timestamp' => time(),
                'email' => $email
            ], ['%d', '%s', '%d', '%s']);

            if (!$inserted) Router::redirect('/verification/?action=error&msg=' . urlencode(__('There was in issue creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo')));

            $mail = Mail::send_token($email, $token);

            if (!$mail)  Router::redirect('/verification/?action=success&msg=' . urlencode('There was an issue sending your verification token. Please try again.'));

            Router::redirect('/verification/?action=success&msg=' . urlencode(__('Please verify your new email address. An email has been sent to the provided address. Your old email will remain active until you verify your new email address.', 'verifywoo')));
        }

        return $errors;
    }
}
