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

        $user = get_user_by('email', $email);

        if (!$user) {
            return Template::error(__('There is no user with that email registered.', 'verifywoo'));
        }

        $verifywoo_table = DB::table(PLUGIN_PREFIX);
        $table_query = DB::get_row('SELECT id, verified from ' . $verifywoo_table . ' where email = %s', $email);

        if ($table_query['verified']) {
            return Template::error(__('That email is already verified.', 'verifywoo'));
        }

        $token = Token::create();
        $inserted = null;

        if (!$table_query) {
            $inserted = DB::insert($verifywoo_table, [
                'user_id' => $user->ID,
                'token' => $token,
                'token_exp' => Token::set_exp(),
                'email' => $email,
            ], ['%d', '%s', '%d', '%s', '%s']);
        } else {
            $inserted = DB::update($verifywoo_table, [
                'token' => $token,
                'token_exp' => Token::set_exp()
            ], [
                'id' => $table_query['id']
            ]);
        }

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
