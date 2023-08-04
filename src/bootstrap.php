<?php

namespace VerifyWoo;

use const VerifyWoo\PLUGIN_ROOT_FILE;
use const VerifyWoo\PLUGIN_PREFIX;

use VerifyWoo\Core\Plugin;
use VerifyWoo\Core\Router;
use VerifyWoo\Core\Session;
use VerifyWoo\Controllers\Backend\Settings;
use VerifyWoo\Controllers\Frontend\Events\InfoChange;
use VerifyWoo\Controllers\Frontend\Events\Login;
use VerifyWoo\Controllers\Frontend\Events\Registration;

defined('ABSPATH') || exit;

// Plugin activation
register_activation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'activate']);
register_deactivation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'deactivate']);

// Plugin initializations
add_action('init', [Session::class, 'init']);
add_action('init', [Plugin::class, 'register_pages']);
add_action('init', [Plugin::class, 'add_shortcodes']);
add_filter('display_post_states', [Plugin::class, 'register_pages_post_state'], 10, 2);
add_filter('body_class', [Plugin::class, 'add_woocommerce_page_class']);

// Handle Settings
add_filter('woocommerce_get_settings_account', [Settings::class, 'configure_woocommerce_account_settings'], 10, 2);
add_filter('woocommerce_email_classes', [Settings::class, 'configure_woocommerce_email_settings'], 10, 2);
add_filter('woocommerce_min_password_strength', [Settings::class, 'set_min_password_strength'], 10);

// Handle Registration Events
add_action('woocommerce_register_form', [Registration::class, 'add_retype_password_input']);
add_action('woocommerce_created_customer', [Registration::class, 'on_registration'], 10, 3);
add_filter('woocommerce_registration_redirect', [Registration::class, 'on_registration_redirect']);
add_filter('woocommerce_registration_errors', [Registration::class, 'on_registration_password_validation'], 10, 3);

// Handle Information Change Events
add_filter('woocommerce_save_account_details_errors', [InfoChange::class, 'on_email_change'], 10, 2);

// Handle Login Events
add_filter('woocommerce_process_login_errors', [Login::class, 'on_login'], 10, 3);

// Routing
add_action(PLUGIN_PREFIX . '_routes', [Router::class, 'resolve']);
