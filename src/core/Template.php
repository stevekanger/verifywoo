<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_ROOT_DIR;

defined('ABSPATH') || exit;

class Template {
    public static function include($template, $data = null) {
        if (!$template) return;
        $themeTemplate = apply_filters('verify_woocommerce_user_templates', $template) . '/' . $template . '.php';
        if (file_exists($themeTemplate)) {
            include $themeTemplate;
            return;
        }
        include PLUGIN_ROOT_DIR . '/src/templates/' . $template . '.php';
    }

    public static function show_shortcode_template($args) {
        ob_start();
        self::include($args['template']);
        return ob_get_clean();
    }
}
