<?php

namespace VerifyWoo;

use const VerifyWoo\PLUGIN_ROOT_FILE;
use const VerifyWoo\PLUGIN_PREFIX;
use VerifyWoo\Core\Plugin;
use VerifyWoo\Core\Registration;
use VerifyWoo\Core\Settings;
use VerifyWoo\Core\Auth;
use VerifyWoo\Core\Router;

defined('ABSPATH') || exit;

// Plugin activation hooks
register_activation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'activate']);
register_deactivation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'deactivate']);

// Required initializations
add_action('init', [Plugin::class, 'register_pages']);
add_action('init', [Plugin::class, 'add_shortcodes']);
add_filter('body_class', [Plugin::class, 'add_woocommerce_page_class']);

// Handle Woocommerce settings
add_filter('woocommerce_get_settings_account', [Settings::class, 'configure_woocommerce_account_settings'], 10, 2);

// Handle user registration
add_action('woocommerce_created_customer', [Registration::class, 'on_registration'], 10, 3);
add_filter('woocommerce_email_additional_content_' . 'customer_new_account', [Registration::class, 'append_registration_email'], 10, 3);
add_filter('woocommerce_min_password_strength', [Registration::class, 'set_min_password_strength'], 10);
add_action('woocommerce_register_form', [Registration::class, 'add_retype_password_input']);
add_filter('woocommerce_registration_errors', [Registration::class, 'validate_registration'], 10, 3);
add_filter('woocommerce_registration_redirect', [Registration::class, 'on_redirect']);

// Handle verification
add_filter('woocommerce_process_login_errors', [Auth::class, 'check_verificaiton_on_login'], 10, 3);

// Handle routing
add_action(PLUGIN_PREFIX . '_verification_page_routing', [Router::class, 'verification_page']);
