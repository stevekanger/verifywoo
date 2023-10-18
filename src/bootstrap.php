<?php

namespace verifywoo;

use const verifywoo\PLUGIN_ROOT_FILE;

use verifywoo\core\Plugin;
use verifywoo\core\Session;
use verifywoo\core\Cron;
use verifywoo\inc\admin\AdminSettings;
use verifywoo\inc\admin\WooSettings;
use verifywoo\inc\app\InfoChange;
use verifywoo\inc\app\Login;
use verifywoo\inc\app\Registration;

defined('ABSPATH') || exit;

// Plugin activation
register_activation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'activate']);
register_deactivation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'deactivate']);

// Plugin initializations
add_action('init', [Plugin::class, 'init']);
add_action('init', [Session::class, 'init']);
add_action('init', [Cron::class, 'init']);
add_action('admin_init', [Plugin::class, 'admin_init']);
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
/* add_action('user_register', [Registration::class, 'on_registration'], 10, 2); */
add_filter('woocommerce_registration_auth_new_customer', [Registration::class, 'woocommerce_registration_auth_new_customer']);
add_action('woocommerce_register_form', [Registration::class, 'include_retype_password_input']);
add_filter('woocommerce_registration_errors', [Registration::class, 'on_registration_password_validation'], 10, 3);

// Handle Information Change Events
add_filter('woocommerce_save_account_details_errors', [InfoChange::class, 'on_email_change'], 10, 2);

// Handle Login Events
add_filter('woocommerce_process_login_errors', [Login::class, 'on_login'], 10, 3);

include_once('utils/dummy_data.php');
