<?php

use VerifyWoo\Controllers\Admin\UsersListTable;

defined('ABSPATH') || exit;

$table = new UsersListTable();
$table->prepare_items();
?>

<h1>Users</h1>
<?php $table->views() ?>
<form id="verifywoo-users" method="GET">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $table->display() ?>
</form>