<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use VerifyWoo\Core\Template;

defined('ABSPATH') || exit;

class Success {
    public function get() {
        Template::include('actions/message', [
            'type' => 'message',
            'msg' => $_GET['msg'] ?? 'Success'
        ]);
    }
}
