<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Users {
    public static function register($user_id, $email) {
        $token = Token::create();

        $inserted = DB::insert([
            'user_id' => $user_id,
            'token' => $token,
            'expires' => Token::set_exp(),
            'email' => $email
        ], ['%d', '%s', '%d', '%s']);

        if (!$inserted) {
            Session::set([
                'registration_redirect' => [
                    'view' => 'error',
                    'msg' => urlencode(__('Your account was created successfully but there was an issue creating your verification information. Please contact your site administrator to verify your email.', 'verifywoo'))
                ]
            ]);
        }

        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);

        $mail = Mail::send($email, get_option(PLUGIN_PREFIX . '_verification_email_subject'), $mailContent);
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
        return DB::update([
            'token' => null,
            'expires' => null,
            'verified' => true,
        ], [
            'user_id' => $user_id
        ]);
    }

    public static function unverify($user_id) {
        return DB::update([
            'verified' => false,
        ], [
            'user_id' => $user_id
        ]);
    }

    public static function delete($user_id) {
        return wp_delete_user($user_id);
    }

    public static function required_user_fields() {
        list($users_table, $tokens_table, $usermeta_table) = DB::tables('users', 'tokens', 'usermeta');
        return "$users_table.ID, $users_table.user_login, $users_table.user_email, $tokens_table.verified, $tokens_table.expires, $usermeta_table.meta_value as roles";
    }

    public static function get($field, $get_by = 'ID') {
        list($users_table, $tokens_table, $usermeta_table) = DB::tables('users', 'tokens', 'usermeta');

        if ($get_by === 'ID') {
            $statement = "SELECT " . self::required_user_fields() . "
                FROM $users_table 
                LEFT JOIN $tokens_table ON $users_table.ID = $tokens_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE $users_table.ID = %d";
            $user = DB::get_row($statement, [$field]);
        } else {
            $statement = "SELECT " . self::required_user_fields() . "
                FROM $users_table 
                LEFT JOIN $tokens_table ON $users_table.ID = $tokens_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE $users_table.user_login = %s OR $users_table.user_email = %s";
            $user = DB::get_row($statement, [$field, $field]);
        }

        if ($user['roles']) $user['roles'] = unserialize($user['roles']);

        return $user;
    }

    public static function get_multiple($args) {
        list($users_table, $tokens_table, $usermeta_table) = DB::tables('users', 'tokens', 'usermeta');

        if (isset($args['where'])) {
            $statement = "SELECT " . self::required_user_fields() . " 
                FROM $users_table
                LEFT JOIN $tokens_table ON $users_table.ID = $tokens_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE " . $args['where'] . " 
                ORDER BY %1s %1s limit %d offset %d";
            $users = DB::get_results(
                $statement,
                [$args['orderby'] ?? null, $args['order'] ?? null, $args['limit'] ?? null, $args['offset'] ?? null],
            );
        } else {
            $statement = "SELECT " . self::required_user_fields() . " 
                FROM $users_table
                LEFT JOIN $tokens_table ON $users_table.ID = $tokens_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                ORDER BY %1s %1s limit %d offset %d";
            $users = DB::get_results(
                $statement,
                [$args['orderby'] ?? null, $args['order'] ?? null, $args['limit'] ?? null, $args['offset'] ?? null]
            );
        }

        $users = array_map(function ($user) {
            $user['roles'] = unserialize($user['roles'] ?? null);
            return $user;
        }, $users);

        return $users;
    }

    public static function count($where = null) {
        list($users_table, $tokens_table, $usermeta_table) = DB::tables('users', 'tokens', 'usermeta');

        if (!$where) {
            return DB::get_var("SELECT count(*) from $users_table");
        }

        $statement = "SELECT count(*)
                FROM $users_table 
                LEFT JOIN $tokens_table ON $users_table.ID = $tokens_table.user_id
                LEFT JOIN $usermeta_table ON $users_table.ID = $usermeta_table.user_id AND $usermeta_table.meta_key = 'wp_capabilities'
                WHERE " . $where;

        return DB::get_var($statement);
    }
}
