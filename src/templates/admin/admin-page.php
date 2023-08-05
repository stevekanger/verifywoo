<?php

use const VerifyWoo\PLUGIN_NAME;
use const VerifyWoo\PLUGIN_PREFIX;

defined('ABSPATH') || exit; ?>

<div class="wrap">
    <h1><?php echo PLUGIN_NAME; ?></h1>
    <form method='POST' action="options.php">
        <?php
        settings_fields(PLUGIN_PREFIX);
        do_settings_sections(PLUGIN_PREFIX);
        submit_button();
        ?>
    </form>

</div>