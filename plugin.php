<?php

namespace VerifyWoo;

/**
 * @package verify-woo
 * 
 * Plugin Name: Verify Woo - WooCommerce User Verificaiton
 * Plugin URI: http://stevekanger.com
 * Description: .
 * Version: 0.0.1
 * Author: Steve Kanger
 * Author URI: https://stevekanger.com
 * License: GPLv2 or later
 * Text Domain: verify-woo
 * 
 * */

defined('ABSPATH') || exit;

const PLUGIN_NAME = 'Verify Woo';
const PLUGIN_PREFIX = 'verifywoo';
const PLUGIN_ROOT_FILE = __FILE__;
const PLUGIN_ROOT_DIR = __DIR__;

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/src/bootstrap.php';
