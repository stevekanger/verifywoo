<?php

namespace verifywoo\controllers\app;

use verifywoo\core\DB;
use verifywoo\core\Mail;
use verifywoo\core\Template;
use verifywoo\core\Token;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Send {
    public function get() {
        Template::include('app/views/send-verification');
    }

    public function post() {
        $nonce = wp_verify_nonce($_POST['_wpnonce'] ?? null, PLUGIN_PREFIX . '_resend_verification_email');

        if (!$nonce) {
            return Template::error(__('Invalid request. Please try again.', 'verifywoo'));
        }

        $email = $_POST['email'] ?? null;

        if (!$email) {
            return Template::error(__('Email is required.', 'verifywoo'));
        }

        $query = DB::get_row('SELECT id, verified from ' . DB::table(PLUGIN_PREFIX) . ' where email = %s', $email);

        if (!$query) {
            return Template::error(__('There is no user with that email registered.', 'verifywoo'));
        } else  if ($query['verified']) {
            return Template::error(__('That email is already verified.', 'verifywoo'));
        }

        $token = Token::create();
        $verifywoo_table = DB::table(PLUGIN_PREFIX);
        $inserted = DB::update($verifywoo_table, [
            'token' => $token,
            'expires' => Token::set_exp()
        ], [
            'id' => $query['id']
        ]);

        if (!$inserted) {
            return Template::error(__('There was an error creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo'));
        }

        $mail = Mail::send_token($email, $token);

        if (!$mail) {
            return Template::error(__('There was an issue sending your verification token. Please contact your site administrator to verify your email manually.', 'verifywoo'));
        }

        Template::success(__('Verification has been sent to your email address.', 'verifywoo'));
    }
}
