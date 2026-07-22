<?php 
require_once 'app/init.php';
$model = new ModelAdmin();
$list = $model->selectWhere('email', 'admin@gereja.com');
$admin = ($list && count($list) > 0) ? $list[0] : null;
if ($admin) { echo 'Admin: ' . $admin->email . PHP_EOL; echo 'Verify: ' . (verify_password('admin123', $admin->password_hash) ? 'PASS' : 'FAIL') . PHP_EOL; } else { echo 'NOT FOUND'; }
