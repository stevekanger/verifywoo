<?php

namespace verifywoo\controllers\app;

use verifywoo\core\Template;

defined('ABSPATH') || exit;

class Error {
    public function get() {
        Template::error($_GET['msg'] ?? 'Error');
    }
}
