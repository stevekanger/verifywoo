<?php

namespace verifywoo\routes;

use verifywoo\controllers\admin\DeleteUtility;
use verifywoo\core\Router;
use verifywoo\controllers\admin\SelectionTable;
use verifywoo\controllers\admin\UsersTable;

Router::get('admin-users', 'selection-table', [SelectionTable::class, 'get']);
Router::post('admin-users', 'selection-table', [SelectionTable::class, 'post']);
Router::get('admin-users', 'delete-utility', [DeleteUtility::class, 'get']);
Router::post('admin-users', 'delete-utility', [DeleteUtility::class, 'post']);
Router::get('admin-users', '*', [UsersTable::class, 'get']);
