<?php defined('ABSPATH') || exit; ?>

<?php wc_get_template('emails/email-header.php', [
    'email_heading' => 'Verify Your Email'
]); ?>

<p>Thank you for being a valued customer. Before logging in please verify your email address below.</p>

<a href="<?php echo home_url() ?>/verification/?">Verify your email</a>

<p>This link will be valid for 1 hour.</p>

<?php wc_get_template('emails/email-footer.php'); ?>