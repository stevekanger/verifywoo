<?php defined('ABSPATH') || exit; ?>

<form class="woocommerce-form" method="post">
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="email">Email address&nbsp;<span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="email" autocomplete="email" value="">
    </p>
    <button type="submit" class="woocommerce-button button woocommerce-form-login__submit wp-element-button" name="send" value="send">Send Verification</button>
</form>