<?php

namespace verifywoo\controllers\admin;

use verifywoo\core\Template;
use verifywoo\core\Router;
use verifywoo\core\Users;

defined('ABSPATH') || exit;

class SelectionTable {
    public static function get() {
        $users = self::get_users();

        if (!$users) return Template::admin_message(__('No users found.', 'verifywoo'));

        Template::include('admin/views/selection-table', [
            'users' => $users,
            'action' => $_REQUEST['action'] ?? null
        ]);
    }

    public static function post() {
        $users = unserialize(urldecode($_REQUEST['users'] ?? []));
        $redirect = urldecode($_REQUEST['redirect']) ?? admin_url('admin.php?page=' . ($_REQUEST['page'] ?? null));

        if (!$users) return Template::admin_message(__('No users found.', 'verifywoo'));

        $action = $_REQUEST['action'] ?? null;

        switch ($action) {
            case 'delete':
                foreach ($users as $user) {
                    $can_delete = get_option('admin_email') !== $user['user_email'];
                    if (!$can_delete) {
                        continue;
                    }
                }
                break;
            case 'verify':
                foreach ($users as $user) {
                    Users::verify($user['ID']);
                }
                break;
            case 'unverify':
                foreach ($users as $user) {
                    Users::unverify($user['ID']);
                }
            default:
                return Template::admin_message(__('Invalid action.', 'verifywoo'));
        }

        Router::redirect('url', $redirect);
    }

    private static function get_users() {
        $ids = $_REQUEST['ids'] ?? [];
        $users = [];

        foreach ($ids as $id) {
            $user = Users::get_one($id);
            if ($user) $users[] = $user;
        }

        return $users;
    }
}
