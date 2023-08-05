<?php

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit; ?>

<?php wc_get_template('emails/email-header.php', [
    'email_heading' => get_option(PLUGIN_PREFIX . '_verification_email_heading') ?? 'Verify your email'
]); ?>

<p><?php echo get_option(PLUGIN_PREFIX . '_verification_email_content') ?></p>

<?php $link_uri = home_url() . '/verification/?action=verify&token=' . $data['token'] ?? null; ?>

<p><a href="<?php echo $link_uri; ?>"><?php echo $link_uri; ?></a></p>

<p>This link will be valid for <?php echo get_option(PLUGIN_PREFIX . '_verification_expiration_length') ?>.</p>

<?php wc_get_template('emails/email-footer.php'); ?>