<?php

namespace verifywoo\controllers\admin;

use const verifywoo\PLUGIN_PREFIX;
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
        $nonce = $_REQUEST['_wpnonce'] ?? null;

        if (!wp_verify_nonce($nonce, PLUGIN_PREFIX . '_selection_table')) {
            Template::admin_message(__('Invalid request.', 'verifywoo'));
        }

        Users::delete_unverified();
        Template::admin_message(__('Users deleted successfully.', 'verifywoo'));
    }
}
