<?php

defined('ABSPATH') || exit; ?>

<p style="margin-bottom: 10px;"><?php echo $data['description'] ?? null ?></p>

<a class="button button-secondary" href="<?php echo $data['link_href'] ?>"><?php echo $data['link_text'] ?></a>
