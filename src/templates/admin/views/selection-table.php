<?php

defined('ABSPATH') || exit; ?>

<?php if ($data['action']) : ?>
    <h1><?php echo ucfirst($data['action']) ?> Users</h1>

    <p>You have selected the following users to <?php echo $data['action'] ?>.</p>

    <form method="post">
        <?php wp_nonce_field() ?>
        <input type="hidden" name="users" value="<?php echo urlencode(serialize($data['users'])) ?>">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
        <input type="hidden" name="action" value="<?php echo $data['action'] ?>">
        <input type="hidden" name="redirect" value="<?php echo $_REQUEST['redirect'] ?? urlencode(admin_url('admin.php?page=' . ($_REQUEST['page'] ?? null))) ?>">

        <?php if ($data['users'] ?? null) : ?>
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Roles</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['users'] as $user) : ?>
                        <tr>
                            <td><?php echo $user['ID'] ?></td>
                            <td><?php echo $user['user_login'] ?></td>
                            <td><?php echo $user['user_email'] ?></td>
                            <td><?php echo implode(', ', array_keys($user['roles'] ?? [])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($data['action'] === 'delete') : ?>
                <p style="font-size: 1.2em; color: #ff0000">Danger Zone: This will permanently delete these users and cannot be undone without backing up your data. </p>
            <?php endif; ?>
            <?php if ($data['action'] === 'unverify') : ?>
                <p style="font-size: 1.2em; color: #ff0000">Note: Token status will be set to null and the user will need to resend a verification link. The selected users will not be automatically deleted if you have this option selected and you will need to manually delete them. </p>
            <?php endif; ?>
            <p>
                <button type="submit" class="button button-primary"><?php echo ucfirst($data['action']) ?> Users</button>
            </p>
        <?php else : ?>
            <p>No users selected.</p>
        <?php endif; ?>
    </form>
<?php else : ?>
    <p>No action to take.</p>
<?php endif; ?>