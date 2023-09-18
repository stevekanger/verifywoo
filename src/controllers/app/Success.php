<?php

namespace verifywoo\controllers\app;

use verifywoo\core\Template;

defined('ABSPATH') || exit;

class Success {
    public function get() {
        Template::success($_GET['msg'] ?? __('Success', 'verifywoo'));
    }
}
