<?php

namespace VerifyWoo\Controllers\Admin\Routes;

use VerifyWoo\Core\Template;

class Verify {
    public static function get() {
        Template::include('admin/actions/verify');
    }
}
