<?php
/**
 * DigitalKasur.com - Logout Page
 * Destroys session and redirects to homepage
 */

require_once '../config.php';
require_once '../includes/auth.php';

logout_user();
set_flash_message('success', 'You have been logged out successfully.');

// Redirect to homepage
header('Location: ../index.php');
exit();
