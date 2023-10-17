<?php

defined('ABSPATH') || exit; ?>

<p style="margin-bottom: 10px"><?php echo $data['description'] ?? '' ?></p>

<textarea style="min-width: 25em; min-height: 7em;" id="<?php echo $data['id'] ?? null ?>" name="<?php echo $data['name'] ?? null ?>"><?php echo $data['value'] ?? null ?></textarea>
