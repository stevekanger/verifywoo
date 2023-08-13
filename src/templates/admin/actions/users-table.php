<?php

use VerifyWoo\Core\Router;
use VerifyWoo\Inc\Admin\UsersListTable;

defined('ABSPATH') || exit;

$table = new UsersListTable();
$table->prepare_items();

$redirect = Router::get_query_string();
?>

<h1>Users</h1>
<?php $table->views() ?>
<form id="verifywoo-users" method="GET">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <input type="hidden" name="redirect" value="<?php echo urlencode(admin_url('admin.php' . $redirect)) ?>">
    <?php $table->display() ?>
</form>