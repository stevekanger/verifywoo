<?php

use verifywoo\core\Router;

use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit;

echo get_option(PLUGIN_PREFIX . '_verification_email_heading') ?? get_bloginfo('title');

echo "\r\n---------------------------------------------------\r\n";

echo  get_option(PLUGIN_PREFIX . '_verification_email_content') ?? 'Please verify your email at the link below.';

echo "\n\n";

$link_uri = Router::get_page_permalink('email-verification', [
    'view' => 'verify',
    'token' => $data['token'] ?? null
]);

echo $link_uri . "\r\n";

$expiration_length = get_option(PLUGIN_PREFIX . '_verification_expiration_length');
$resend_link = Router::get_page_permalink('email-verification');

echo "This link will be valid for $expiration_length. If your link has expired resend your verification link here $resend_link.";
