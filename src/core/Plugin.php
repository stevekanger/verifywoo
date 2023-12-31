<?php

namespace verifywoo\core;

use verifywoo\core\Cron;

use const verifywoo\PLUGIN_NAME;
use const verifywoo\PLUGIN_PREFIX;
use const verifywoo\PLUGIN_ROOT_FILE;

defined('ABSPATH') || exit;

class Plugin {
    static $pages = [
        'verification' => [
            'post_title' => 'Email Verification',
            'post_name' => 'email-verification',
            'post_state' => PLUGIN_NAME . ' Verification Page'
        ]
    ];

    public static function activate() {
        DB::maybe_create_table();
    }

    public static function deactivate() {
        self::remove_pages();
        Cron::plugin_deactivate();
    }

    public static function init() {
        self::register_pages();
        self::add_shortcodes();
    }

    public static function admin_init() {
        self::require_woocommerce();
    }

    public static function require_woocommerce() {
        if (is_admin() && current_user_can('activate_plugins') &&  !is_plugin_active('woocommerce/woocommerce.php')) {
            add_action('admin_notices', function () {
                Template::include('admin/notices/require-woocommerce');
            });

            deactivate_plugins(plugin_basename(PLUGIN_ROOT_FILE));

            if (isset($_GET['activate'])) {
                unset($_GET['activate']);
            }
        }
    }

    public static function register_pages() {
        foreach (self::$pages as $page) {
            if (get_page_by_path($page['post_name'])) return;
            $page = array(
                'post_title' => $page['post_title'],
                'post_name' => $page['post_name'],
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_content' => '<!-- wp:shortcode --> [' . PLUGIN_PREFIX . ' template="app/page-' . $page['post_name'] . '"] <!-- /wp:shortcode -->'
            );
            wp_insert_post($page, false);
        }
    }

    public static function register_pages_post_state($post_states, $post) {
        foreach (self::$pages as $page) {
            if (get_page_by_path($page['post_name'])->ID === $post->ID) {
                $post_states[PLUGIN_PREFIX . '_' . $page['post_name'] . '_post_state'] = __($page['post_state'], 'woocommerce');
            }
        }
        return $post_states;
    }

    public static function add_shortcodes() {
        add_shortcode(PLUGIN_PREFIX, [self::class, 'page_template_shortcode']);
    }

    public static function page_template_shortcode($args) {
        return Template::get_clean($args['template']);
    }

    public static function add_woocommerce_page_class($classes) {
        foreach (self::$pages as $page) {
            if (is_page($page['post_name'])) {
                $classes[] = 'woocommerce-page';
            }
        }
        return $classes;
    }

    static function remove_pages() {
        foreach (self::$pages as $page) {
            $registeredPage = get_page_by_path($page['post_name']);
            if ($registeredPage) {
                $pageID = $registeredPage->ID;
                wp_delete_post($pageID, true);
            }
        }
    }
}
