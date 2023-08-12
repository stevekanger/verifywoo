<?php

namespace VerifyWoo\Controllers\Admin;

use VerifyWoo\Core\DB;
use VerifyWoo\Core\Token;
use VerifyWoo\Core\Utils;
use WP_List_Table;

use const VerifyWoo\PLUGIN_PREFIX;

class UsersListTable extends WP_List_Table {
    public function __construct() {
        parent::__construct(
            [
                'singular' => 'User',
                'plural'   => 'Users',
            ]
        );
    }

    public function prepare_items() {
        $token_table = DB::prefix(PLUGIN_PREFIX);
        $users_table = DB::prefix('users');

        $per_page = 2;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);
        // $this->process_bulk_actions();
        $total_items = DB::get_var('SELECT COUNT(id) FROM ' . DB::prefix(PLUGIN_PREFIX));

        $paged = $this->get_pagenum();
        $orderby = $this->get_orderby();
        $order = strtoupper($_REQUEST['order'] ?? 'asc');
        $offset = ($paged * $per_page) - $per_page;
        $where = $this->get_where($token_table);

        // Select verifywoo.verified, verifywoo.email, verifywoo.expires, users.ID, users.user_login where verifywoo.user_id = users.ID limit 25
        $query = "SELECT $token_table.verified, $token_table.email, $token_table.expires, $users_table.ID, $users_table.user_email, $users_table.user_login from $token_table, $users_table WHERE $token_table.user_id = $users_table.ID $where ORDER BY %1s %1s limit %d offset %d";
        $items = DB::get_results($query, [$orderby, $order, $per_page, $offset]);
        $items = array_map([$this, 'map_token_status'], $items);

        $this->items = $items;

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ));
    }

    private function get_where($token_table) {
        $status = $_REQUEST['status'] ?? null;
        switch ($status) {
            case 'verified':
                return "AND $token_table.verified = true";
            case 'unverified':
                return "AND $token_table.verified = false";
            case 'active':
                return DB::prepare("AND $token_table.expires > %d", [time()]);
            case 'expired':
                return DB::prepare("AND $token_table.expires < %d", [time()]);
            default:
                return '';
        }
    }

    private function get_orderby() {
        $orderby = $_REQUEST['orderby'] ?? null;
        switch ($orderby) {
            case 'user_login':
                return DB::prefix('users') . '.user_login';
            case 'email':
                return DB::prefix(PLUGIN_PREFIX) . '.email';
            default:
                return DB::prefix('users') . '.ID';
        }
    }

    private function map_token_status($item) {
        $expires = $item['expires'] ?? null;
        if (!$expires) {
            $item['token_status'] = 'null';
        } else if (!Token::verify_exp($expires)) {
            $item['token_status'] = 'expired';
        } else {
            $item['token_status'] = 'active';
        }
        return $item;
    }

    protected function get_views() {
        $table = DB::prefix(PLUGIN_PREFIX);
        $status = $_REQUEST['status'] ?? null;
        $links = [
            [
                'status' => 'all',
                'current' => !$status || ($status === 'all'),
                'count' => DB::get_var("SELECT count(*) from $table"),
            ],
            [
                'status' => 'verified',
                'current' => $status === 'verified',
                'count' => DB::get_var("SELECT count(*) from $table where verified = true"),
            ],
            [
                'status' => 'unverified',
                'current' => $status === 'unverified',
                'count' => DB::get_var("SELECT count(*) from $table where verified = false"),
            ],
            [
                'status' => 'active',
                'current' => $status === 'active',
                'count' => DB::get_var("SELECT count(*) from $table where expires > %d", [time()])
            ],
            [
                'status' => 'expired',
                'current' => $status === 'expired',
                'count' => DB::get_var("SELECT count(*) from $table where expires < %d", [time()])
            ]
        ];

        $status_links = array_map(function ($link) {
            return '<a class="' . ($link['current'] ? 'current' : '') . '" href="?page=' . $_REQUEST['page'] . '&status=' . $link['status'] . '">' . ucfirst($link['status']) . ' (' . $link['count'] . ')</a>';
        }, $links);

        return $status_links;
    }

    function column_default($item, $column_title) {
        return $item[$column_title];
    }

    function column_cb($user) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $user['ID']
        );
    }

    function column_user_login($user) {
        $actions = array(
            'delete' => sprintf('<a href="?page=%s&id=%s&action=delete">%s</a>', $_REQUEST['page'], $user['ID'], __('Delete', 'verifywoo')),
        );

        if ($user['verified']) {
            $actions['unverify'] = sprintf('<a href="?page=%s&id=%s&action=unverify">%s</a>', $_REQUEST['page'], $user['ID'], __('Unverify', 'verifywoo'));
        } else {
            $actions['verify'] = sprintf('<a href="?page=%s&id=%s&action=verify">%s</a>', $_REQUEST['page'], $user['ID'], __('Verify', 'verifywoo'));
        }

        return sprintf(
            '%s %s',
            $user['user_login'],
            $this->row_actions($actions)
        );
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user_login' => __('Username', 'verifywoo'),
            'ID' => __('User ID', 'verifywoo'),
            'email' => __('Email', 'verifywoo'),
            'verified' => __('Verified', 'verifywoo'),
            'token_status' => __('Token Status', 'verifywoo')
        );
        return $columns;
    }

    protected function get_sortable_columns() {
        $columns = array(
            'ID' => ['ID', false],
            'user_login' => ['user_login', false],
            'email' => ['email', false],
        );

        return $columns;
    }


    function get_bulk_actions() {
        if (!current_user_can('delete_users'))  return [];

        $actions = [
            'delete' => __('Delete', 'verifywoo'),
            'verify' => __('Verify', 'verifywoo'),
            'unverify' => __('Unverify', 'verifywoo'),
        ];
        return $actions;
    }

    function process_bulk_action() {
        if (!current_user_can('delete_users')) return;

        // global $wpdb;
        // $table_name = DB::prefix(PLUGIN_PREFIX);

        // if ('delete' === $this->current_action()) {
        //     $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
        //     if (is_array($ids)) $ids = implode(',', $ids);

        //     if (!empty($ids)) {
        //         $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
        //     }
        // }
    }
}
