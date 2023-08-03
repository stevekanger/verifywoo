<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use VerifyWoo\Core\Template;

defined('ABSPATH') || exit;

class Error {
    public function get() {
        Template::include('actions/message', [
            'type' => 'error',
            'msg' => $_GET['msg'] ?? 'Error',
        ]);
    }
}
