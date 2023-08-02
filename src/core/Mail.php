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

    public static function append_registration_email($content, $user, $email) {
        $token = DB::get_data_by_user_id($user->ID)->token;
        $activation_uri = get_home_url() . '/verification?action=verification-registration&token=' . $token;
        $new_content = 'Before you can log in to your account please verify your email address. Click here <a href="' . $activation_uri . '">' . $activation_uri . '</a>';
        return $new_content . "\n\n" . 'This link will be valid for 1 hour.' . "\n\n" . $content;
    }
}
