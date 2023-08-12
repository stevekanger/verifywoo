<?php

namespace VerifyWoo\Controllers\Admin\Routes;

use VerifyWoo\Core\Template;

class Delete {
    public static function get() {
        Template::include('admin/actions/delete');
    }
}
