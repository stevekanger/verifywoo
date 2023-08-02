<?php

namespace VerifyWoo\Core;

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

class Routing {
    public static function route_verification_actions() {
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'success':
                return self::success();
            case 'error':
                return self::error();
            case 'verification-registration':
                return self::verification_registration();
            case 'verification-email-change':
                return self::verification_email_change();
            case 'send-verification':
                return self::send_verification();
            default:
                return Template::include('actions/message', [
                    'type' => 'info',
                    'msg' => 'Nothing to do yet.'
                ]);
        }
    }

    static function success() {
        Template::include('actions/message', [
            'type' => 'message',
            'msg' => $_GET['msg'] ?? 'Success.',
            'show_resend' => true
        ]);
    }

    static function error() {
        Template::include('actions/message', [
            'type' => 'error',
            'msg' => $_GET['msg'] ?? 'Error.',
            'show_resend' => true
        ]);
    }

    static function verification_registration() {
        $verified = Token::verify($_GET['token'] ?? null);
        if ($verified) {
            Template::include('actions/message', [
                'type' => 'message',
                'msg' => 'Your email was successfully verified. You may now login.',
                'show_resend' => true
            ]);
            return;
        }
        Template::include('actions/message', [
            'type' =>  'error',
            'msg' =>  'There was an issue verifying your token.',
            'show_resend' => true
        ]);
    }

    static function verification_email_change() {
        Template::include('actions/message', [
            'type' => 'message',
            'msg' => 'Email change verification page',
        ]);
    }

    static function send_verification() {
        $email = $_POST['email'] ?? null;
        if (!$email) {
            Template::include('actions/send-verification');
            return;
        }

        $user = get_user_by('email', $email);
        if (!$user) return Template::include('action/message', [
            'type' => 'error',
            'msg' => 'There is no user with that email registered.',
            'show_resend' => true
        ]);

        $token = Token::create();
        $timestamp = time();
        $userData = get_user_meta($user->ID, PLUGIN_PREFIX . '_data', true);

        update_user_meta($user->ID, PLUGIN_PREFIX . '_token', $token);
        update_user_meta($user->ID, PLUGIN_PREFIX . '_data', array_merge($userData, [
            'token_timestamp' => $timestamp,
        ]));

        $mailContent = Template::get_clean('emails/send-verification', [
            'token' => $token
        ]);
        $mail = Mail::send($email, get_bloginfo('title') . ' - Verify your email', $mailContent);

        return Template::include('actions/message', [
            'type' => $mail ? 'message' : 'error',
            'msg' =>  $mail ? 'A verification link has been sent to the email provided.' : 'There was an error sending the link to your email.',
            'show_resend' => true
        ]);
    }
}
