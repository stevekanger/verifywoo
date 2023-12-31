<?php

namespace verifywoo\core;

use const verifywoo\PLUGIN_ROOT_DIR;
use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Template {
    public static function include($template, $data = []) {
        if (!$template) return;
        $themeTemplate = self::get_theme_template($template);
        include $themeTemplate ? $themeTemplate : PLUGIN_ROOT_DIR . '/src/templates/' . $template . '.php';
    }

    public static function get_clean($template, $data = []) {
        if (!$template) return;
        ob_start();
        self::include($template, $data);
        return ob_get_clean();
    }

    static function get_theme_template($template) {
        $themeTemplate = apply_filters(PLUGIN_PREFIX . '_template_directory', $template) . '/' . $template . '.php';
        if (file_exists($themeTemplate)) {
            return $themeTemplate;
        }
        return null;
    }

    public static function error($msg, $hide_resend = false) {
        self::include('app/views/message', [
            'type' => 'error',
            'msg' => $msg,
            'hide_resend' => $hide_resend
        ]);
    }

    public static function success($msg, $hide_resend = false) {
        self::include('app/views/message', [
            'type' => 'message',
            'msg' => $msg,
            'hide_resend' => $hide_resend
        ]);
    }

    public static function info($msg, $hide_resend = false) {
        self::include('app/views/message', [
            'type' => 'info',
            'msg' => $msg,
            'hide_resend' => $hide_resend
        ]);
    }

    public static function admin_message($msg) {
        self::include('admin/views/message', [
            'msg' => $msg
        ]);
    }
}
