<?php

namespace verifywoo\core;

use WC_Emails;

use const verifywoo\PLUGIN_PREFIX;

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

    public static function send_token($email, $token) {
        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);

        $subject = get_option(PLUGIN_PREFIX . '_verification_email_subject') ?? 'Verify your email';

        $mail = self::send($email, $subject, $mailContent);
        return $mail;
    }
}
