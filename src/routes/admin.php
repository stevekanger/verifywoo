<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\Admin\UsersTable;
use VerifyWoo\Controllers\Admin\Verify;
use VerifyWoo\Controllers\Admin\Unverify;

Router::get('admin-users', 'verify', [Verify::class, 'get']);
Router::post('admin-users', 'verify', [Verify::class, 'post']);
Router::get('admin-users', 'unverify', [Unverify::class, 'get']);
Router::post('admin-users', 'unverify', [Unverify::class, 'post']);
Router::get('admin-users', '*', [UsersTable::class, 'get']);
