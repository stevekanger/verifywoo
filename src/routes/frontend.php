<?php

namespace VerifyWoo\Routes;

use VerifyWoo\Core\Router;
use VerifyWoo\Controllers\Frontend\Routes\Error;
use VerifyWoo\Controllers\Frontend\Routes\Send;
use VerifyWoo\Controllers\Frontend\Routes\Success;
use VerifyWoo\Controllers\Frontend\Routes\Verify;

Router::get('error', [Error::class, 'get']);
Router::get('success', [Success::class, 'get']);
Router::get('verify', [Verify::class, 'get']);
Router::get('send', [Send::class, 'get']);
Router::post('send', [Send::class, 'post']);
