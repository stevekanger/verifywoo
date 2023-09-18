<?php

defined('ABSPATH') || exit; ?>

<input class="regular-text" type="<?php echo $data['type'] ?? null ?>" id="<?php echo $data['id'] ?? null ?>" name="<?php echo $data['name'] ?? null ?>" value="<?php echo $data['value'] ?? null ?>">
<p><?php echo $data['description'] ?></p>