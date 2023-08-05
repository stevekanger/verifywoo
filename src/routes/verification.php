<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\App\Routes\Error;
use VerifyWoo\Controllers\App\Routes\Send;
use VerifyWoo\Controllers\App\Routes\Success;
use VerifyWoo\Controllers\App\Routes\Verify;
use VerifyWoo\Core\Template;

Router::get('verification', 'error', [Error::class, 'get']);
Router::get('verification', 'success', [Success::class, 'get']);
Router::get('verification', 'verify', [Verify::class, 'get']);
Router::get('verification', 'send', [Send::class, 'get']);
Router::post('verification', 'send', [Send::class, 'post']);

// Not found
Router::all('verification', '*', function () {
    Template::info(__('Nothing to do here yet.', 'verifywoo'), true);
});
