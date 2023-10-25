<?php

namespace verifywoo\core;

use WC_Emails;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Mail {
    public static function get_content_type() {
        $use_plaintext = get_option(PLUGIN_PREFIX . '_use_plaintext_emails');
        return $use_plaintext ? "text/plain" : "text/html";
    }

    public static function send($recipient, $subject, $content) {
        add_filter('woocommerce_email_content_type', [self::class, 'get_content_type']);

        $mail = wc_mail($recipient, $subject, WC_Emails::instance()->replace_placeholders($content));

        remove_filter('woocommerce_email_content_type', [self::class, 'get_content_type']);

        return $mail;
    }

    public static function send_token($email, $token) {
        $use_plaintext = get_option(PLUGIN_PREFIX . '_use_plaintext_emails');
        $mailContent = Template::get_clean($use_plaintext ? 'emails/plain/send-verification' : 'emails/send-verification', [
            'token' => $token
        ]);

        $subject = get_option(PLUGIN_PREFIX . '_verification_email_subject') ?? 'Verify your email';

        $mail = self::send($email, $subject, $mailContent);
        return $mail;
    }
}
