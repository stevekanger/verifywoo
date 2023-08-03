<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\DB;
use VerifyWoo\Core\Mail;
use VerifyWoo\Core\Template;
use VerifyWoo\Core\Token;

defined('ABSPATH') || exit;

class Send {
    public function get() {
        Template::include('actions/send-verification');
    }

    public function post() {
        $email = $_POST['email'] ?? null;

        if (!$email) {
            return Template::include('actions/message', [
                'type' => 'error',
                'msg' => 'No email was given.'
            ]);
        }

        // Select database-row-id where wp_users email = $email and user_id = wp_users->ID
        $query = DB::get_row('SELECT ' . DB::prefix(PLUGIN_PREFIX . '.id') . ' from ' . DB::prefix(PLUGIN_PREFIX) . ', ' . DB::prefix('users') . ' where ' . DB::prefix('users') . '.user_email = %s AND ' . DB::prefix(PLUGIN_PREFIX) . '.user_id = ' . DB::prefix('users') . '.ID', $email);
        if (!$query) {
            return Template::include('actions/message', [
                'type' => 'error',
                'msg' => 'There is no user with that email registered.',
                'show_resend' => true
            ]);
        }

        $token = Token::create();
        $timestamp = time();

        $inserted = DB::update([
            'token' => $token,
            'timestamp' => $timestamp
        ], [
            'id' => $query->id
        ]);

        if (!$inserted) {
            return Template::include('actions/message', [
                'type' => 'error',
                'msg' => 'There was an error creating the verirification data. Please try again.'
            ]);
        }

        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);
        $mail = Mail::send($email, get_bloginfo('title') . ' - Verify your email', $mailContent);

        if (!$mail) {
            return Template::include('actions/message', [
                'type' => 'error',
                'msg' => 'There was an issue sending your verification token. Please contact your site administrator to verify your email manually.',
                'show_resend' => true
            ]);
        }
        Template::include('actions/message', [
            'type' => 'message',
            'msg' => 'Verification has been sent to your email address.',
            'show_resend' => true
        ]);
    }
}
