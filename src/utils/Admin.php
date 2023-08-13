<?php

namespace VerifyWoo\Utils;

use VerifyWoo\Core\DB;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Admin {
    public static function process_list_table_request_ids($request_ids) {
        if (!$request_ids) return null;

        $id_arr = [];
        if (is_array($request_ids)) {
            $id_arr = $request_ids;
        } else {
            $id_arr[] = $request_ids;
        }

        $users = array_map(function ($id) {
            $token_table = DB::prefix(PLUGIN_PREFIX);
            $users_table = DB::prefix('users');
            $user = DB::get_row("SELECT $token_table.user_id, $token_table.verified, $token_table.email, $users_table.user_login from $token_table, $users_table where $token_table.user_id = %d and $users_table.ID = $token_table.user_id", [$id]);
            return $user;
        }, $id_arr);

        return $users;
    }
}
