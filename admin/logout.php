<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    admin_logout();
    flash_set('success', 'You have been logged out.');
}

redirect('login.php');
