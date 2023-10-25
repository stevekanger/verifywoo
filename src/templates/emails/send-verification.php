<?php

use const verifywoo\PLUGIN_PREFIX;
use verifywoo\core\Router;

defined('ABSPATH') || exit; ?>

<?php wc_get_template('emails/email-header.php', [
    'email_heading' => get_option(PLUGIN_PREFIX . '_verification_email_heading') ?? get_bloginfo('title')
]); ?>

<p><?php echo get_option(PLUGIN_PREFIX . '_verification_email_content') ?? 'Please verify your email at the link below.' ?></p>

<?php $link_uri = Router::get_page_permalink('email-verification', [
    'view' => 'verify',
    'token' => $data['token'] ?? null
]) ?>

<p><a href="<?php echo $link_uri; ?>"><?php echo $link_uri; ?></a></p>

<p>This link will be valid for <?php echo get_option(PLUGIN_PREFIX . '_verification_expiration_length') ?>. If your link has expired resend your verification link. <a href="<?php echo Router::get_page_permalink('email-verification') ?>">Resend verification link</a></p>

<?php wc_get_template('emails/email-footer.php'); ?>
