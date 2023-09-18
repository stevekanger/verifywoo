<?php

namespace verifywoo\routes;

use verifywoo\core\Router;
use verifywoo\controllers\app\Error;
use verifywoo\controllers\app\Send;
use verifywoo\controllers\app\Success;
use verifywoo\controllers\app\Verify;

Router::get('email-verification', 'error', [Error::class, 'get']);
Router::get('email-verification', 'success', [Success::class, 'get']);
Router::get('email-verification', 'verify', [Verify::class, 'get']);
Router::get('email-verification', '*', [Send::class, 'get']);
Router::post('email-verification', '*', [Send::class, 'post']);
