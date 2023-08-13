<?php

defined('ABSPATH') || exit; ?>

<h1><?php echo $data['action'] === 'verify' ? 'Verify' : 'Unverify'; ?> Users</h1>

<p>You have selected the following users <?php echo $data['action'] === 'verify' ? 'verification' : 'unverification' ?>.</p>

<form method="post">
    <?php wp_nonce_field() ?>
    <input type="hidden" name="users" value="<?php echo urlencode(serialize($data['users'])) ?>">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
    <input type="hidden" name="action" value="verify">
    <input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect'] ?? urlencode(admin_url('admin.php?page=' . ($_REQUEST['page'] ?? null))) ?>">

    <?php if ($data['users'] ?? null) : ?>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user) : ?>
                    <tr>
                        <td><?php echo $user['user_id'] ?></td>
                        <td><?php echo $user['user_login'] ?></td>
                        <td><?php echo $user['email'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>
            <button type="submit" class="button button-primary"><?php echo $data['action'] === 'verify' ? 'Verify' : 'Unverify' ?> Users</button>
        </p>
    <?php else : ?>
        <p>No users selected.</p>
    <?php endif; ?>
</form>