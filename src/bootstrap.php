<?php

namespace VerifyWoo;

use const VerifyWoo\PLUGIN_ROOT_FILE;
use const VerifyWoo\PLUGIN_ROOT_DIR;

use VerifyWoo\Core\Plugin;
use VerifyWoo\Core\Session;
use VerifyWoo\Controllers\Admin\AdminSettings;
use VerifyWoo\Controllers\Admin\WooSettings;
use VerifyWoo\Controllers\App\InfoChange;
use VerifyWoo\Controllers\App\Login;
use VerifyWoo\Controllers\App\Registration;

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
add_filter('woocommerce_get_settings_account', [WooSettings::class, 'configure_woocommerce_account_settings'], 10, 2);
add_filter('woocommerce_email_classes', [WooSettings::class, 'configure_woocommerce_email_settings'], 10, 2);
add_filter('woocommerce_min_password_strength', [WooSettings::class, 'min_password_strength'], 10);

// Handle Admin Page
add_action('admin_menu', [AdminSettings::class, 'admin_menu']);
add_action('admin_init', [AdminSettings::class, 'add_settings_sections']);
add_action('init', [AdminSettings::class, 'register_settings']);

// Handle Registration Events
add_action('user_register', [Registration::class, 'on_registration'], 10, 2);
add_filter('woocommerce_registration_auth_new_customer', [Registration::class, 'woocommerce_registration_auth_new_customer']);
add_action('woocommerce_register_form', [Registration::class, 'include_retype_password_input']);
add_filter('woocommerce_registration_errors', [Registration::class, 'on_registration_password_validation'], 10, 3);

// Handle Information Change Events
add_filter('woocommerce_save_account_details_errors', [InfoChange::class, 'on_email_change'], 10, 2);

// Handle Login Events
add_filter('woocommerce_process_login_errors', [Login::class, 'on_login'], 10, 3);

// Include Routes
require PLUGIN_ROOT_DIR . '/src/routes/email-verification.php';
require PLUGIN_ROOT_DIR . '/src/routes/admin-users.php';
