<?php

namespace VerifyWoo\Controllers\Admin;

use VerifyWoo\Core\Template;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Utils;
use VerifyWoo\Core\Users;

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

        Utils::debug($users);

        // switch ($action) {
        //     case 'delete':
        //         foreach ($users as $user) {
        //             Users::delete($user['ID']);
        //         }
        //         break;
        //     case 'verify':
        //         foreach ($users as $user) {
        //             Users::verify($user['ID']);
        //         }
        //         break;
        //     case 'unverify':
        //         foreach ($users as $user) {
        //             Users::unverify($user['ID']);
        //         }
        // }

        Router::redirect('url', $redirect);
    }

    private static function get_users() {
        $ids = $_REQUEST['ids'] ?? [];
        $users = [];

        foreach ($ids as $id) {
            $user = Users::get($id);
            if ($user) $users[] = $user;
        }

        return $users;
    }
}
