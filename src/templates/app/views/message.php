<?php

use VerifyWoo\Core\Router;

defined('ABSPATH') || exit; ?>

<div class="woocommerce-<?php echo $data['type'] ?? 'message'; ?>" role="alert">
    <?php echo $data['msg'] ?? ''; ?>
</div>

<?php if (!($data['hide_resend'] ?? false)) : ?>
    <a class="woocommerce-Button button wp-element-button" href="<?php echo Router::get_page_permalink('email-verification') ?>"><?php echo __('Resend Verification', 'verifywoo'); ?></a>
<?php endif; ?>