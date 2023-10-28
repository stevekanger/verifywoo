<?php

namespace verifywoo\controllers\app;

use verifywoo\core\Template;
use verifywoo\core\Token;
use verifywoo\core\DB;
use verifywoo\core\Users;
use WP_Error;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Verify {
    public function get() {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            return Template::error(__('There was an issue verifying your token.', 'verifywoo'));
        }

        $data = DB::get_row('SELECT * from ' . DB::table(PLUGIN_PREFIX) . ' where token = %s', $token);

        if (!$data || !($data['token_exp'] ?? null)) {
            return Template::error(__('There was an issue verifying your token.', 'verifywoo'));
        }

        $verified = Token::verify($data['token_exp']);

        if (!$verified) {
            return Template::error(__('There was an issue verifying your token.', 'verifywoo'));
        }

        if ($data['email_change']) {
            $email_taken = get_user_by('user_email', $data['email_change']);

            if ($email_taken) {
                return Template::error(__('It seems there is already a user registered with that email.', 'verifywoo'));
            }

            $updated_email = Users::update_email($data['user_id'], $data['email_change']);

            if ($updated_email instanceof WP_Error) {
                return Template::error(__('There was an issue saving your new email information. Please contact your site administrator.', 'verifywoo'));
            }
        }

        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        $updated = DB::update($verifywoo_table, [
            'token' => null,
            'token_exp' => null,
            'verified' => true,
            'email' => $data['email_change'] ?? $data['email'],
            'email_change' => null
        ], [
            'user_id' => $data['user_id']
        ]);

        if (!$updated) {
            return Template::error(__('There was an issue verifying your token.', 'verifywoo'));
        }

        Template::success(__('You have successfully verified your email address. You can now login.', 'verifywoo'), true);
    }
}
