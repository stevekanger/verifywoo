<?php

use const verifywoo\PLUGIN_NAME;
use const verifywoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1><?php echo PLUGIN_NAME; ?></h1>
    <form method='POST' action="options.php">
        <?php wp_nonce_field(PLUGIN_PREFIX . '_settings'); ?>
        <?php
        settings_fields(PLUGIN_PREFIX);
        do_settings_sections(PLUGIN_PREFIX);
        submit_button();
        ?>
    </form>
</div>
