<?php
/**
 * Admin Logout - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Destroy session and redirect
logout_user();
set_flash_message('success', 'You have been logged out successfully.');
header("Location: login.php");
exit();
