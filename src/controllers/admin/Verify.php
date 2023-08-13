<?php

namespace VerifyWoo\Controllers\Admin;

use VerifyWoo\Core\Router;
use VerifyWoo\Core\Template;
use VerifyWoo\Core\User;
use VerifyWoo\Utils\Admin;

defined('ABSPATH') || exit;

class Verify {
    public static function get() {
        $users = Admin::process_list_table_request_ids($_REQUEST['id'] ?? null);

        if (!$users) return Template::admin_message(__('No users found.', 'verifywoo'));

        Template::include('admin/actions/selection-table', [
            'users' => $users,
            'action' => 'verify'
        ]);
    }

    public static function post() {
        $users = unserialize(urldecode($_REQUEST['users'] ?? []));
        $redirect = urldecode($_REQUEST['redirect']) ?? admin_url('admin.php?page=' . ($_REQUEST['page'] ?? null));

        if (!$users) return Template::admin_message(__('No users found.', 'verifywoo'));

        foreach ($users as $user) {
            User::verify($user['user_id']);
        }

        Router::redirect($redirect);
    }
}
