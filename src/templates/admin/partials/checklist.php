<?php

defined('ABSPATH') || exit; ?>

<p style="margin-bottom: 10px;"><?php echo $data['description'] ?? '' ?></p>

<?php foreach ($data['options'] as $option) : ?>
    <label for="<?php echo $data['id'] ?>-<?php echo $option ?>">
        <input id="<?php echo $data['id'] ?>-<?php echo $option ?>" type="checkbox" name="<?php echo $data['name'] ?>[]" value="<?php echo $option ?>" <?php if (in_array($option, $data['value'])) echo 'checked' ?>>
        <?php echo $option ?>
    </label>
    <br>
<?php endforeach ?>