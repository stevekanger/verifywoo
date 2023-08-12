<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\Admin\Routes\UsersList;
use VerifyWoo\Controllers\Admin\Routes\Verify;
use VerifyWoo\Controllers\Admin\Routes\Unverify;
use VerifyWoo\Controllers\Admin\Routes\Delete;

Router::get('admin-users', 'verify', [Verify::class, 'get']);
Router::get('admin-users', 'unverify', [Unverify::class, 'get']);
Router::get('admin-users', 'delete', [Delete::class, 'get']);
Router::get('admin-users', '*', [UsersList::class, 'get']);
