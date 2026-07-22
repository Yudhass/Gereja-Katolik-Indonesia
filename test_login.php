<?php
require_once 'app/init.php';

// Test login
$model = new ModelAdmin();
$adminList = ->selectWhere('email', 'admin@gereja.com');
$admin = ($adminList && count($adminList) > 0) ? $adminList[0] : null;
if ($admin) {
    echo "Admin found: " . $admin->email . "\n";
    echo "Password hash: " . $admin->password_hash . "\n";
    echo "Verify test: " . (verify_password('admin123', $admin->password_hash) ? "PASS" : "FAIL") . "\n";
} else {
    echo "Admin NOT found in database!\n";
}
