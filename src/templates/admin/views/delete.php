<?php

defined('ABSPATH') || exit; ?>

<h1>Delete Users</h1>


<form method="post">
    <?php wp_nonce_field() ?>
    <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
    <input type="hidden" name="action" value="delete">

    <ul>
        <?php foreach ($data['users'] as $user) : ?>
            <li>ID #<?php echo $user->ID ?> <?php echo $user->user_email ?></li>
        <?php endforeach; ?>
    </ul>

    <p style="color:#ff0000">Danger Zone: this will permenantly delete the following users from the database.</p>
    <button type="submit" class="button button-primary">Confirm Deletion</button>
</form>