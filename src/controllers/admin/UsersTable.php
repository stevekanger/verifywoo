<?php

namespace VerifyWoo\Controllers\Admin;

use VerifyWoo\Core\Template;

class UsersTable {
    public static function get() {
        Template::include('admin/views/users-table');
    }
}
