<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Plugin {
    static $pages = [
        'verification' => [
            'post_title' => 'Verification',
            'post_name' => 'verification',
        ]
    ];

    public static function activate() {
        DB::create_table();
    }

    public static function deactivate() {
        self::remove_pages();
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
                'post_content' => '<!-- wp:shortcode --> [' . PLUGIN_PREFIX . '-page template="page-' . $page['post_name'] . '"] <!-- /wp:shortcode -->'
            );
            wp_insert_post($page, false);
        }
    }

    public static function add_shortcodes() {
        add_shortcode(PLUGIN_PREFIX . '-page', [self::class, 'page_template_shortcode']);
    }

    public static function page_template_shortcode($args) {
        return Template::get_clean($args['template']);
    }

    public static function add_woocommerce_page_class($classes) {
        foreach (self::$pages as $key => $value) {
            if (is_page($key)) {
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
