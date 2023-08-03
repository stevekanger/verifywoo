<?php defined('ABSPATH') || exit; ?>

<?php wc_get_template('emails/email-header.php', [
    'email_heading' => 'Verify Your Email'
]); ?>

<p>Thank you for being a valued customer. Before logging in please verify your email address below.</p>

<?php $link_uri = home_url() . '/verification/?action=verify&token=' . $data['token'] ?? null; ?>

<p>Verify your email address - <a href="<?php echo $link_uri; ?>"><?php echo $link_uri; ?></a></p>

<p>This link will be valid for 1 hour.</p>

<?php wc_get_template('emails/email-footer.php'); ?>