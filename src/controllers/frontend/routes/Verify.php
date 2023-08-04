<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use Verifywoo\Core\Template;
use VerifyWoo\Core\Token;
use VerifyWoo\Core\DB;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Verify {
    public function get() {
        $token = $_GET['token'] ?? null;
        $data = Token::verify($token);

        if (!$data) return Template::error('There was an issue verifying your token.');


        if ($data->email && $data->user_id) {
            wp_update_user([
                'ID' => $data->user_id,
                'user_email' => $data->email
            ]);
        }

        $updated = DB::update([
            'token' => null,
            'timestamp' => null,
            'verified' => true,
        ], [
            'user_id' => $data->user_id
        ]);

        if (!$updated) return Template::error('There was an issue verifying your token.');

        DB::query('DELETE from ' . DB::prefix(PLUGIN_PREFIX) . ' where user_id = %d AND id <> %d', [$data->user_id, $data->id]);

        Template::success('You have successfully verified your email address. You can now proceed to the account page.', true);
    }
}
