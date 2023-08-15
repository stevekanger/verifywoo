<?php

use VerifyWoo\Core\Router;
use VerifyWoo\Inc\Admin\UsersListTable;

defined('ABSPATH') || exit;

$_SERVER['REQUEST_URI'] = remove_query_arg('_wp_http_referer', $_SERVER['REQUEST_URI']);
$table = new UsersListTable();
$table->prepare_items();
$redirect = Router::get_query_string(); ?>

<h1>Users</h1>
<?php $table->views() ?>
<form method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $table->search_box('Search users', 'search_id'); ?>
</form>
<form id="verifywoo-users" method="GET">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <input type="hidden" name="redirect" value="<?php echo urlencode(admin_url('admin.php' . $redirect)) ?>">
    <input type="hidden" name="view" value="selection-table">
    <?php $table->display() ?>
</form>