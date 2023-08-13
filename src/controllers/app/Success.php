<?php

namespace VerifyWoo\Controllers\App;

use VerifyWoo\Core\Template;

defined('ABSPATH') || exit;

class Success {
    public function get() {
        Template::success($_GET['msg'] ?? __('Success', 'verifywoo'));
    }
}
