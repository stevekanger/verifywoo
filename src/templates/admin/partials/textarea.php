<?php

defined('ABSPATH') || exit; ?>

<textarea style="min-width: 25em; min-height: 7em;" id="<?php echo $data['id'] ?? null ?>" name="<?php echo $data['name'] ?? null ?>"><?php echo $data['value'] ?? null ?></textarea>
<p><?php echo $data['description'] ?? null ?></p>