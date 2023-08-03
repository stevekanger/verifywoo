<?php

use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit; ?>

<div class="woocommerce">
    <?php do_action(PLUGIN_PREFIX . '_routes'); ?>
</div>