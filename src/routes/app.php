<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\App\Error;
use VerifyWoo\Controllers\App\Send;
use VerifyWoo\Controllers\App\Success;
use VerifyWoo\Controllers\App\Verify;

Router::get('email-verification', 'error', [Error::class, 'get']);
Router::get('email-verification', 'success', [Success::class, 'get']);
Router::get('email-verification', 'verify', [Verify::class, 'get']);
Router::get('email-verification', '*', [Send::class, 'get']);
Router::post('email-verification', '*', [Send::class, 'post']);
