<?php

namespace verifywoo\controllers\admin;

use verifywoo\core\Users;
use verifywoo\core\Template;

defined('ABSPATH') || exit;

class DeleteUtility {
    public static function get() {
        $users = Users::get_unverified();

        if (!$users) {
            return Template::admin_message(__('No unverified users found.', 'verifywoo'));
        }

        Template::include('admin/views/selection-table', [
            'users' => $users,
            'action' => 'delete'
        ]);
    }

    public static function post() {
        Users::delete_unverified();
        Template::admin_message(__('Users deleted successfully.', 'verifywoo'));
    }
}
