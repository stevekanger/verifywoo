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

        if (!$email) return Template::error('Email is required');

        $query = DB::get_row('SELECT id, verified from ' . DB::prefix(PLUGIN_PREFIX) . ' where email = %s', $email);

        if (!$query) return Template::error('There is no user with that email registered.');
        if ($query->verified) return Template::error('That email is already registered.');

        $token = Token::create();
        $timestamp = time();

        $inserted = DB::update([
            'token' => $token,
            'timestamp' => $timestamp
        ], [
            'id' => $query->id
        ]);

        if (!$inserted) return Template::error('There was an error creating your verification data. Please try again. If the problem persists contact your site administrator.');

        $mail = Mail::send_token($email, $token);

        if (!$mail) return Template::error('There was an issue sending your verification token. Please contact your site administrator to verify your email manually.');

        Template::success('Verification has been sent to your email address.');
    }
}
