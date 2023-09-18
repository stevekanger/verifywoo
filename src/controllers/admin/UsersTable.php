<?php

namespace verifywoo\controllers\admin;

use verifywoo\core\Template;

class UsersTable {
    public static function get() {
        Template::include('admin/views/users-table');
    }
}
