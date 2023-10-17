<?php

defined('ABSPATH') || exit; ?>

<p style="margin-bottom: 10px;"><?php echo $data['description'] ?? '' ?></p>

<select id="<?php echo $data['id'] ?? null ?>" name="<?php echo $data['name'] ?? null ?>">
    <?php foreach ($data['options'] ?? null as $option) : ?>
        <option value="<?php echo $option ?>" <?php if ($option == ($data['value'] ?? null)) echo 'selected' ?>><?php echo $option ?></option>
    <?php endforeach; ?>

</select>
