<?php

namespace verifywoo\core;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Users {
    public static function check_email($email) {
        $query = DB::get_var('SELECT verified from ' . DB::table(PLUGIN_PREFIX) . ' where email = %s', [$email]);
        return $query['verified'] ?? 0;
    }

    public static function check_id($user_id) {
        $query = DB::get_var('SELECT verified from ' . DB::table(PLUGIN_PREFIX) . ' where user_id = %d', [$user_id]);
        return $query['verified'] ?? 0;
    }

    public static function register($user_id, $email) {
        $token = Token::create();
        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        $inserted = DB::insert($verifywoo_table, [
            'user_id' => $user_id,
            'token' => $token,
            'token_exp' => Token::set_exp(),
            'email' => $email,
        ], ['%d', '%s', '%d', '%s', '%s']);

        if (!$inserted) {
            Session::set([
                'registration_redirect' => [
                    'view' => 'error',
                    'msg' => urlencode(__('Your account was created successfully but there was an issue creating your verification information. Please contact your site administrator to verify your email.', 'verifywoo'))
                ]
            ]);
        }

        $mail = Mail::send_token($email, $token);
        if (!$mail) {
            Session::set([
                'registration_redirect' => [
                    'view' => 'error',
                    'msg' => urlencode(__('Your account was created successfully but there was an issue sending your verification link. Please contact your site administrator to verify your email.', 'verifywoo'))
                ]
            ]);
        }

        wp_logout();
    }

    public static function update_email($user_id, $email) {
        return wp_update_user([
            'ID' => $user_id,
            'user_email' => $email
        ]);
    }

    public static function verify($user_id) {
        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        $in_table = DB::get_row("SELECT * from $verifywoo_table where user_id = %d", [$user_id]);

        if (!$in_table) {
            $user = get_user_by('ID', $user_id);
            $verifywoo_table = DB::table(PLUGIN_PREFIX);

            return DB::insert($verifywoo_table, [
                'user_id' => $user_id,
                'token' => null,
                'token_exp' => null,
                'email' => $user->user_email,
                'verified' => true,
            ], ['%d', '%s', '%d', '%s', '%s']);
        }

        return DB::update($verifywoo_table, [
            'token' => null,
            'token_exp' => null,
            'verified' => true,
        ], [
            'user_id' => $user_id
        ]);
    }

    public static function unverify($user_id) {
        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        return DB::update($verifywoo_table, [
            'verified' => false,
        ], [
            'user_id' => $user_id
        ]);
    }

    public static function delete($user_id) {
        require_once(ABSPATH . 'wp-admin/includes/user.php');
        return wp_delete_user($user_id);
    }

    public static function delete_unverified() {
        $users = self::get_unverified();

        foreach ($users as $user) {
            self::delete($user['ID']);
        }
    }

    public static function get_roles($just_keys = false) {
        global $wp_roles;
        $roles = $wp_roles->roles;

        if ($just_keys) {
            return array_keys($roles);
        }

        return $roles;
    }

    public static function get_editable_roles($just_keys = true) {
        global $wp_roles;

        $all_roles = $wp_roles->roles;
        $editable_roles = apply_filters('editable_roles', $all_roles);
        unset($editable_roles['administrator']);

        if ($just_keys) {
            return array_keys($editable_roles);
        }

        return $editable_roles;
    }

    public static function required_user_fields() {
        list($users_table, $verifywoo_table, $usermeta_table) = DB::tables('users', 'verifywoo', 'usermeta');
        return "$users_table.ID, $users_table.user_login, $users_table.user_email, $verifywoo_table.verified, $verifywoo_table.token_exp, $usermeta_table.meta_value as roles";
    }

    public static function get_one($field, $get_by = 'ID') {
        list($users_table, $verifywoo_table, $usermeta_table) = DB::tables('users', 'verifywoo', 'usermeta');

        if ($get_by === 'ID') {
            $statement = "SELECT " . self::required_user_fields() . "
                FROM $users_table 
                LEFT JOIN $verifywoo_table ON $users_table.ID = $verifywoo_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE $users_table.ID = %d";
            $user = DB::get_row($statement, [$field]);
        } else {
            $statement = "SELECT " . self::required_user_fields() . "
                FROM $users_table 
                LEFT JOIN $verifywoo_table ON $users_table.ID = $verifywoo_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE $users_table.user_login = %s OR $users_table.user_email = %s";
            $user = DB::get_row($statement, [$field, $field]);
        }

        if ($user['roles']) $user['roles'] = unserialize($user['roles']);

        return $user;
    }

    public static function get($args) {
        list($users_table, $verifywoo_table, $usermeta_table) = DB::tables('users', 'verifywoo', 'usermeta');

        if (isset($args['where'])) {
            $statement = "SELECT " . self::required_user_fields() . " 
                FROM $users_table
                LEFT JOIN $verifywoo_table ON $users_table.ID = $verifywoo_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE " . $args['where'] . " 
                ORDER BY %1s %1s limit %d offset %d";
            $users = DB::get_results(
                $statement,
                [$args['orderby'] ?? 'ID', $args['order'] ?? "ASC", $args['limit'] ?? '25', $args['offset'] ?? '0'],
            );
        } else {
            $statement = "SELECT " . self::required_user_fields() . " 
                FROM $users_table
                LEFT JOIN $verifywoo_table ON $users_table.ID = $verifywoo_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                ORDER BY %1s %1s limit %d offset %d";
            $users = DB::get_results(
                $statement,
                [$args['orderby'] ?? "ID", $args['order'] ?? "ASC", $args['limit'] ?? '25', $args['offset'] ?? '0']
            );
        }

        $users = array_map(function ($user) {
            $user['roles'] = unserialize($user['roles'] ?? null);
            return $user;
        }, $users);

        return $users;
    }

    public static function get_unverified($exclude_specified_roles = true) {
        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        $count = self::count();
        $now = time();

        $users = self::get([
            'limit' => $count,
            'where' => "($verifywoo_table.verified = false OR $verifywoo_table.verified IS NULL) AND ($verifywoo_table.token_exp < $now)"
        ]);

        if ($exclude_specified_roles) return self::filter_excluded_roles($users);

        return $users;
    }

    private static function filter_excluded_roles($users) {
        $excluded_roles = get_option(PLUGIN_PREFIX . '_delete_users_exclude_roles') ?? [];
        array_push($excluded_roles, "administrator");

        $filter_roles = function ($user) use ($excluded_roles) {
            $has_role = false;

            for ($i = 0; $i < count($excluded_roles); $i++) {
                if ($user['roles'][$excluded_roles[$i]] ?? null) {
                    $has_role = true;
                    break;
                }
            }

            if (!$has_role) return $user;
        };

        return array_filter($users, $filter_roles);
    }

    public static function count($where = null) {
        list($users_table, $verifywoo_table) = DB::tables('users', 'verifywoo');

        if (!$where) {
            return DB::get_var("SELECT count(*) from $users_table");
        }

        $statement = "SELECT count(*)
                FROM $users_table 
                LEFT JOIN $verifywoo_table ON $users_table.ID = $verifywoo_table.user_id
                WHERE " . $where;

        return DB::get_var($statement);
    }
}
