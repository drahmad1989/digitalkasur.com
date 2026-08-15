<?php
/**
 * DigitalKasur.com - Newsletter Subscription Handler
 * Processes email subscription and redirects back
 */

require_once '../config.php';
require_once '../db.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message('error', 'Please enter a valid email address.');
    } else {
        // Check if already subscribed
        $existing = DB::selectOne("SELECT id, is_active FROM newsletter_subscribers WHERE email = ?", [$email]);

        if ($existing) {
            if ($existing['is_active']) {
                set_flash_message('info', 'You are already subscribed to our newsletter!');
            } else {
                // Re-activate
                DB::update('newsletter_subscribers', ['is_active' => 1], 'id = ?', [$existing['id']]);
                set_flash_message('success', 'Welcome back! Your subscription has been reactivated.');
            }
        } else {
            $result = DB::insert('newsletter_subscribers', [
                'email' => $email,
                'is_active' => 1,
            ]);

            if ($result) {
                set_flash_message('success', 'Thank you for subscribing to our newsletter!');
            } else {
                set_flash_message('error', 'Something went wrong. Please try again.');
            }
        }
    }
}

// Redirect back
$referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: $referer");
exit();
