<?php defined('ABSPATH') || exit; ?>

<div class="woocommerce-<?php echo $data['type'] ?? 'message'; ?>" role="alert">
    <?php echo $data['msg'] ?? ''; ?>
</div>

<?php if ($data['show_resend'] ?? null) : ?>
    <p>Resend.</p>
<?php endif; ?>