<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use Verifywoo\Core\Template;
use VerifyWoo\Core\Token;
use VerifyWoo\Core\DB;

defined('ABSPATH') || exit;

class Verify {
    public function get() {

        $token = $_GET['token'] ?? null;
        $data = Token::verify($token);

        if (!$data) {
            return Template::include('actions/message', [
                'type' =>  'error',
                'msg' =>  'There was an issue verifying your token.',
                'show_resend' => true
            ]);
        }

        if ($data->email) wp_update_user(['user_email' => $data->email]);

        DB::update([
            'token' => null,
            'timestamp' => null,
            'verified' => true,
        ], [
            'user_id' => $data->user_id
        ]);

        Template::include('actions/message', [
            'type' => 'message',
            'msg' => 'Your email was successfully verified. You may now login.',
            'show_resend' => true
        ]);
    }
}
