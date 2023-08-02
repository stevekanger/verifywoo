<?php

namespace VerifyWoo\Core;

defined('ABSPATH') || exit;

class Mail {
    static $mailer = null;

    static function initMailer() {
        if (self::$mailer) return;
        self::$mailer = WC()->mailer();
    }

    public static function send($recipient, $subject, $content) {
        self::initMailer();
        $content = str_replace('{site_title}', get_bloginfo('title'), $content);
        $content = str_replace('{WooCommerce}', '<a href="https://woocommerce.com">WooCommerce</a>', $content);
        $mail = self::$mailer->send($recipient, $subject, $content, ['Content-Type: text/html; charset=UTF-8']);
        return $mail;
    }
}
