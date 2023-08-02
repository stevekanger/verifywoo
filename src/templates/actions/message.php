<?php defined('ABSPATH') || exit; ?>

<div class="woocommerce-<?php echo $data['type'] ?? 'message'; ?>" role="alert">
    <?php echo $data['msg'] ?? ''; ?>
    <?php if ($data['show_resend'] ?? null) : ?>
        <a href="<?php echo home_url() ?>/verification/?action=send-verification">Resend Verification</a>
    <?php endif; ?>
</div>