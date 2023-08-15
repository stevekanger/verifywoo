<?php

namespace VerifyWoo\Controllers\App;

use Verifywoo\Core\Template;
use VerifyWoo\Core\Token;
use VerifyWoo\Core\DB;
use VerifyWoo\Core\Users;
use WP_Error;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Verify {
    public function get() {
        $token = $_GET['token'] ?? null;
        $data = Token::verify($token);

        if (!$data) return Template::error(__('There was an issue verifying your token.', 'verifywoo'));


        if ($data['email'] && $data['user_id']) {
            $updated_email = Users::update_email($data['user_id'], $data['email']);

            if ($updated_email instanceof WP_Error) return Template::error(__('There was an issue saving your new email information. Please contact your site administrator.', 'verifywoo'));
        }

        $updated = Users::verify($data['user_id']);
        if (!$updated) return Template::error(__('There was an issue verifying your token.', 'verifywoo'));

        DB::query('DELETE from ' . DB::table() . ' where user_id = %d AND id <> %d', [$data['user_id'], $data['id']]);

        Template::success(__('You have successfully verified your email address. You can now proceed to the account page.', 'verifywoo'), true);
    }
}
