<?php

namespace verifywoo\routes;

use verifywoo\core\Router;
use verifywoo\controllers\admin\SelectionTable;
use verifywoo\controllers\admin\UsersTable;

Router::get('admin-users', 'selection-table', [SelectionTable::class, 'get']);
Router::post('admin-users', 'selection-table', [SelectionTable::class, 'post']);
Router::get('admin-users', '*', [UsersTable::class, 'get']);
