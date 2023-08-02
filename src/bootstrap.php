<?php

namespace VerifyWoo;

use const VerifyWoo\PLUGIN_ROOT_FILE;
use const VerifyWoo\PLUGIN_PREFIX;

use VerifyWoo\Core\EventHandlers;
use VerifyWoo\Core\Plugin;
use VerifyWoo\Core\Settings;
use VerifyWoo\Core\Routing;

defined('ABSPATH') || exit;

// Plugin activation
register_activation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'activate']);
register_deactivation_hook(PLUGIN_ROOT_FILE, [Plugin::class, 'deactivate']);

// Plugin initializations
add_action('init', [Plugin::class, 'register_pages']);
add_action('init', [Plugin::class, 'add_shortcodes']);
add_filter('body_class', [Plugin::class, 'add_woocommerce_page_class']);

// Plugin Settings
add_filter('woocommerce_get_settings_account', [Settings::class, 'configure_woocommerce_account_settings'], 10, 2);
add_filter('woocommerce_min_password_strength', [Settings::class, 'set_min_password_strength'], 10);
add_action('woocommerce_register_form', [Settings::class, 'add_retype_password_input']);
add_filter('woocommerce_email_additional_content_' . 'customer_new_account', [Mail::class, 'append_registration_email'], 10, 3);

// Handle User Events
add_action('woocommerce_created_customer', [EventHandlers::class, 'on_registration'], 10, 3);
add_filter('woocommerce_registration_redirect', [EventHandlers::class, 'on_registration_redirect']);
add_filter('woocommerce_registration_errors', [EventHandlers::class, 'on_registration_password_validation'], 10, 3);
add_filter('send_email_change_email', [EventHandlers::class, 'on_email_change'], 10, 1);
add_filter('woocommerce_process_login_errors', [EventHandlers::class, 'on_login'], 10, 3);

// Routing
add_action(PLUGIN_PREFIX . '_route_verification_actions', [Routing::class, 'route_verification_actions']);
