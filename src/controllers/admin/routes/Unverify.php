<?php

namespace VerifyWoo\Controllers\Admin\Routes;

use VerifyWoo\Core\Template;

class Unverify {
    public static function get() {
        Template::include('admin/actions/unverify');
    }
}
