<?php

namespace VerifyWoo\Controllers\Frontend\Routes;

use VerifyWoo\Core\Template;

defined('ABSPATH') || exit;

class Success {
    public function get() {
        Template::success($_GET['msg'] ?? 'Success');
    }
}
