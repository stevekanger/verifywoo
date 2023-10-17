<?php

defined('ABSPATH') || exit; ?>

<p style="margin-bottom: 10px;"><?php echo $data['description'] ?? '' ?></p>

<input class="regular-text" type="<?php echo $data['type'] ?? null ?>" id="<?php echo $data['id'] ?? null ?>" name="<?php echo $data['name'] ?? null ?>" value="<?php echo $data['value'] ?? null ?>">