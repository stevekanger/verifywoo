<?php defined('ABSPATH') || exit; ?>

<div class="woocommerce-<?php echo $data['type'] ?? 'message'; ?>" role="alert">
    <?php echo $data['msg'] ?? ''; ?>
    <a href="<?php echo home_url() ?>/verification/?action=send">Resend Verification</a>
</div>