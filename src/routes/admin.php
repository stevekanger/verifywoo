<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\Admin\SelectionTable;
use VerifyWoo\Controllers\Admin\UsersTable;

Router::get('admin-users', 'selection-table', [SelectionTable::class, 'get']);
Router::post('admin-users', 'selection-table', [SelectionTable::class, 'post']);
Router::get('admin-users', '*', [UsersTable::class, 'get']);
