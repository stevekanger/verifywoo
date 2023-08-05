<?php

defined('ABSPATH') || exit; ?>

<label for="<?php echo $data['id'] ?? null ?>">
    <input type="checkbox" name="<?php echo $data['id'] ?? null ?>" id="<?php echo $data['id'] ?? null ?>" <?php if ($data['value']) echo 'checked' ?>>
    <?php echo $data['description'] ?? null ?>
</label>