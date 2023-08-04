<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use VerifyWoo\Core\Template;

defined('ABSPATH') || exit;

class Error {
    public function get() {
        Template::error($_GET['msg'] ?? 'Error');
    }
}
