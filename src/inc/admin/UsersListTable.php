<?php

namespace verifywoo\inc\admin;

use verifywoo\core\DB;
use verifywoo\core\Users;
use verifywoo\core\Router;
use verifywoo\core\Token;
use verifywoo\core\Utils;
use WP_List_Table;

use const verifywoo\PLUGIN_PREFIX;

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
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $limit = 2;
        $paged = $this->get_pagenum();
        $offset = ($paged * $limit) - $limit;

        $orderby = $this->get_orderby();
        $order = strtoupper($_REQUEST['order'] ?? 'asc');
        $where = $this->get_where();

        $total_items = Users::count($where);
        $items = Users::get(compact('orderby', 'order', 'limit', 'offset', 'where'));

        $this->items = $items;

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'limit' => $limit,
            'total_pages' => ceil($total_items / $limit)
        ));
    }

    private function get_where() {
        $search = $_REQUEST['s'] ?? null;
        $status = $_REQUEST['status'] ?? null;
        $now = time();
        list($users_table, $verifywoo_table) = DB::tables('users', 'verifywoo');

        if ($search) {
            return DB::prepare("$users_table.user_email LIKE '%%%s%%' OR $users_table.user_login LIKE '%%%s%%'", [$search, $search]);
        } else if ($status === 'verified') {
            return "$verifywoo_table.verified = true";
        } else if ($status === 'unverified') {
            return "$verifywoo_table.verified = false OR $verifywoo_table.verified is NULL";
        } else if ($status === 'active') {
            return "$verifywoo_table.expires > $now";
        } else if ($status === 'expired') {
            return "$verifywoo_table.expires < $now";
        } else {
            return null;
        }
    }

    private function get_orderby() {
        $orderby = $_REQUEST['orderby'] ?? null;
        $users_table = DB::table('users');

        switch ($orderby) {
            case 'user_login':
                return "$users_table.user_login";
            case 'email':
                return "$users_table.user_email";
            default:
                return "$users_table.ID";
        }
    }

    protected function get_views() {
        $verifywoo_table = DB::table(PLUGIN_PREFIX);

        $status = $_REQUEST['status'] ?? null;
        $now = time();

        $links = [
            [
                'status' => 'all',
                'text' => 'All',
                'current' => !$status || ($status === 'all'),
                'count' => Users::count(),
            ],
            [
                'status' => 'verified',
                'text' => 'Verified',
                'current' => $status === 'verified',
                'count' => Users::count("$verifywoo_table.verified = true"),
            ],
            [
                'status' => 'unverified',
                'text' => 'Unverified',
                'current' => $status === 'unverified',
                'count' => Users::count("NOT $verifywoo_table.verified OR $verifywoo_table.verified is NULL"),
            ],
            [
                'status' => 'active',
                'text' => 'Active Token',
                'current' => $status === 'active',
                'count' =>  Users::count("$verifywoo_table.expires > $now")
            ],
            [
                'status' => 'expired',
                'text' => 'Expired Token',
                'current' => $status === 'expired',
                'count' => Users::count("$verifywoo_table.expires < $now")
            ]
        ];

        $status_links = array_map(function ($link) {
            return '<a class="' . ($link['current'] ? 'current' : '') . '" href="?page=' . $_REQUEST['page'] . '&status=' . $link['status'] . '">' . $link['text'] . ' (' . $link['count'] . ')</a>';
        }, $links);

        return $status_links;
    }

    function column_default($item, $column_title) {
        return $item[$column_title];
    }

    function column_cb($user) {
        return sprintf(
            '<input type="checkbox" name="ids[]" value="%s" />',
            $user['ID']
        );
    }

    function column_user_login($user) {
        $actions = [];
        $page = $_REQUEST['page'];
        $current_query = Router::get_query_string();
        $built_query = http_build_query([
            'redirect' => admin_url('admin.php' . $current_query),
            'ids' => [($user['ID'])]
        ]);

        $actions['delete'] = sprintf('<a href="?page=%s&action=delete&view=selection-table&%s">%s</a>', $page, $built_query, __('Delete User', 'verifywoo'));

        if ($user['verified'] === 'yes') {
            $actions['unverify'] = sprintf('<a href="?page=%s&action=unverify&view=selection-table&%s">%s</a>', $page, $built_query, __('Unverify', 'verifywoo'));
        } else {
            $actions['verify'] = sprintf('<a href="?page=%s&action=verify&view=selection-table&%s">%s</a>', $page, $built_query, __('Verify', 'verifywoo'));
        }

        return sprintf(
            '<a href="' . admin_url('user-edit.php') . '?user_id=%d">%s</a> %s',
            $user['ID'],
            $user['user_login'],
            $this->row_actions($actions)
        );
    }

    function column_verified($user) {
        return $user['verified'] ? '<span style="color: #008000" class="dashicons dashicons-yes"></span>' : '<span style="color: #ff0000" class="dashicons dashicons-no"></span>';
    }

    function column_token_status($item) {
        $expires = $item['expires'] ?? null;

        if (!$expires) {
            return 'null';
        } else if (!Token::verify_exp($expires)) {
            return '<b style="color: #ff0000">Expired</b>';
        } else {
            return '<b style="color: #008000">Active</b>';
        }
    }

    function column_roles($user) {
        return implode(', ', array_keys($user['roles'] ?? []));
    }

    function get_columns() {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'user_login' => __('Username', 'verifywoo'),
            'ID' => __('User ID', 'verifywoo'),
            'user_email' => __('Email', 'verifywoo'),
            'verified' => __('Verified', 'verifywoo'),
            'token_status' => __('Token Status', 'verifywoo'),
            'roles' => __('Roles', 'verifywoo')
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
        $actions = [
            'delete' => __('Delete', 'verifywoo'),
            'verify' => __('Verify', 'verifywoo'),
            'unverify' => __('Unverify', 'verifywoo'),
        ];
        return $actions;
    }
}
