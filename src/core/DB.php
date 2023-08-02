<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class DB {
    public static function get_tablename() {
        global $wpdb;
        return $wpdb->prefix . PLUGIN_PREFIX;
    }

    public static function create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = 'CREATE TABLE ' . self::get_tablename() . ' (
            id bigint(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            token varchar(100) UNIQUE DEFAULT NULL,
            timestamp bigint(20) DEFAULT NULL,
            verified boolean NOT NULL DEFAULT false,
            current_email varchar(255) DEFAULT NULL,
            new_email varchar(255) DEFAULT NULL,
            FOREIGN KEY (user_id) REFERENCES ' . $wpdb->prefix . 'users (ID) ON DELETE CASCADE 
        ) AUTO_INCREMENT=1, ' . $charset_collate . ';';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table(self::get_tablename(), $sql);
    }

    public static function get_data_by_user_id($user_id) {
        $query = self::get_row('SELECT * from ' . self::get_tablename() . ' where user_id = %d', $user_id);
        return $query;
    }

    public static function get_data_by_token($token) {
        $query = self::get_row('SELECT * from ' . self::get_tablename() . ' where token = %s', $token);
        return $query;
    }

    public static function insert($data, $format = null) {
        global $wpdb;
        return $wpdb->insert(self::get_tablename(), $data, $format);
    }

    public static function update($data, $where, $format = null, $where_format = null) {
        global $wpdb;
        return $wpdb->update(self::get_tablename(), $data, $where, $format, $where_format);
    }

    public static function get_row($query, $params) {
        global $wpdb;
        return $wpdb->get_row(
            $wpdb->prepare($query, $params)
        );
    }

    public static function get_results($query, $params) {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare($query, $params)
        );
    }

    public static function query($query, $params) {
        global $wpdb;
        return $wpdb->query(
            $wpdb->prepare($query, $params)
        );
    }
}
