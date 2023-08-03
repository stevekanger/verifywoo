<?php

namespace VerifyWoo\Core;

use WC_Emails;

defined('ABSPATH') || exit;

class Mail {
    static $mailer = null;

    static function init_mailer() {
        if (self::$mailer) return;
        self::$mailer = WC()->mailer();
    }

    public static function send($recipient, $subject, $content) {
        self::init_mailer();

        $mail = self::$mailer->send($recipient, $subject, WC_Emails::instance()->replace_placeholders($content), ['Content-Type: text/html; charset=UTF-8']);
        return $mail;
    }
}
