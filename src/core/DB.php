<?php

namespace verifywoo\core;

defined('ABSPATH') || exit;

class DB {
    public static function table($table) {
        global $wpdb;
        return $wpdb->prefix . $table;
    }

    public static function tables(...$tables) {
        return array_map(function ($table) {
            return self::table($table);
        }, $tables);
    }

    public static function maybe_create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = 'CREATE TABLE ' . self::table('verifywoo') . ' (
            id bigint(20) UNSIGNED PRIMARY KEY AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            email varchar(255) UNIQUE NOT NULL,
            token varchar(100) UNIQUE DEFAULT NULL,
            expires bigint(20) DEFAULT NULL,
            verified boolean NOT NULL DEFAULT false,
            FOREIGN KEY (user_id) REFERENCES ' . $wpdb->prefix . 'users (ID) ON DELETE CASCADE 
        ) AUTO_INCREMENT=1, ' . $charset_collate . ';';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        maybe_create_table(self::table('verifywoo'), $sql);
    }

    public static function insert($table, $data, $format = null) {
        global $wpdb;
        return $wpdb->insert($table, $data, $format);
    }

    public static function update($table, $data, $where, $format = null, $where_format = null) {
        global $wpdb;
        return $wpdb->update($table, $data, $where, $format, $where_format);
    }

    public static function get_var($query, $params = null) {
        global $wpdb;
        return $wpdb->get_var(
            self::prepare($query, $params)
        );
    }

    public static function get_row($query, $params = null) {
        global $wpdb;
        return $wpdb->get_row(
            self::prepare($query, $params),
            ARRAY_A
        );
    }

    public static function get_results($query, $params = null) {
        global $wpdb;
        return $wpdb->get_results(
            self::prepare($query, $params),
            ARRAY_A
        );
    }

    public static function delete($table, $where, $where_format) {
        global $wpdb;
        return $wpdb->delete($table, $where, $where_format);
    }

    public static function prepare($query, $params = null) {
        global $wpdb;
        return $params ? $wpdb->prepare($query, $params) : $query;
    }

    public static function query($query, $params = null) {
        global $wpdb;
        return $wpdb->query(
            self::prepare($query, $params)
        );
    }
}
