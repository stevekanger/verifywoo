<?php

namespace VerifyWoo\Controllers\App\Routes;

use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\DB;
use VerifyWoo\Core\Mail;
use VerifyWoo\Core\Template;
use VerifyWoo\Core\Token;

defined('ABSPATH') || exit;

class Send {
    public function get() {
        Template::include('app/actions/send-verification');
    }

    public function post() {
        $email = $_POST['email'] ?? null;

        if (!$email) return Template::error(__('Email is required.', 'verifywoo'));

        $query = DB::get_row('SELECT id, verified from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', $email);

        if (!$query) return Template::error(__('There is no user with that email registered.', 'verifywoo'));
        if ($query['verified']) return Template::error(__('That email is already verified.', 'verifywoo'));

        $token = Token::create();
        $inserted = DB::update([
            'token' => $token,
            'expires' => Token::set_exp()
        ], [
            'id' => $query['id']
        ]);

        if (!$inserted) return Template::error(__('There was an error creating your verification data. Please try again. If the problem persists contact your site administrator.', 'verifywoo'));

        $mail = Mail::send_token($email, $token);

        if (!$mail) return Template::error(__('There was an issue sending your verification token. Please contact your site administrator to verify your email manually.', 'verifywoo'));

        Template::success(__('Verification has been sent to your email address.', 'verifywoo'));
    }
}
