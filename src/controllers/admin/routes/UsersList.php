<?php

namespace VerifyWoo\Controllers\Admin\Routes;

use VerifyWoo\Core\Template;

class UsersList {
    public static function get() {
        Template::include('admin/actions/users-list');
    }
}
