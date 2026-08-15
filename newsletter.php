<?php
/**
 * Newsletter Subscription Handler - DigitalKasur.com
 *
 * Handles newsletter subscription requests:
 * - Validates email address
 * - Checks if already subscribed
 * - Saves to newsletter_subscribers table
 * - Sets flash message
 * - Redirects back to referring page
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// ONLY ACCEPT POST REQUESTS
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash_message('error', 'Invalid request method.');
    redirect(SITE_URL);
    exit();
}

// ============================================================
// GET AND VALIDATE EMAIL
// ============================================================

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($email)) {
    set_flash_message('error', 'Please enter your email address.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash_message('error', 'Please enter a valid email address.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// Sanitize email
$email = clean_input($email);

// Check email length
if (strlen($email) > 150) {
    set_flash_message('error', 'Email address is too long.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// CHECK FOR SPAM / BOT SUBMISSIONS
// ============================================================

// Basic honeypot check - if a hidden field was filled, it's likely a bot
if (!empty($_POST['website']) || !empty($_POST['name_hp'])) {
    // Silently ignore bot submissions but show success to confuse bots
    set_flash_message('success', 'Thank you for subscribing to our newsletter!');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// Rate limiting: prevent rapid repeated submissions from same session
$last_subscribe = $_SESSION['last_newsletter_subscribe'] ?? 0;
if (time() - $last_subscribe < 10) {
    set_flash_message('error', 'Please wait a moment before subscribing again.');
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// CHECK IF ALREADY SUBSCRIBED
// ============================================================

$existing = DB::selectOne(
    "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?",
    [$email]
);

if ($existing) {
    if ($existing['is_active'] == 1) {
        set_flash_message('info', 'This email is already subscribed to our newsletter. Thank you!');
    } else {
        // Re-activate a previously unsubscribed email
        DB::update('newsletter_subscribers', [
            'is_active' => 1,
            'unsubscribed_at' => null,
            'subscribed_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$existing['id']]);
        set_flash_message('success', 'Welcome back! Your email has been re-subscribed to our newsletter.');
    }
    redirect($_SERVER['HTTP_REFERER'] ?? SITE_URL);
    exit();
}

// ============================================================
// SAVE NEW SUBSCRIBER
// ============================================================

$inserted = DB::insert('newsletter_subscribers', [
    'email' => $email,
    'is_active' => 1,
    'subscribed_at' => date('Y-m-d H:i:s')
]);

if ($inserted) {
    // Log the subscription
    $log_entry = date('Y-m-d H:i:s') . " | New subscriber: {$email}\n";
    @file_put_contents(__DIR__ . '/logs/newsletter_subscriptions.log', $log_entry, FILE_APPEND | LOCK_EX);

    // Send welcome email (non-blocking)
    $subject = 'Welcome to DigitalKasur Newsletter!';
    $message = '
    <html>
    <head><title>Welcome to DigitalKasur Newsletter</title></head>
    <body style="font-family: Poppins, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1E40AF, #3B82F6); color: #fff; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
            <h1 style="margin: 0; font-size: 24px;">Welcome to DigitalKasur!</h1>
        </div>
        <div style="background: #fff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px;">
            <p style="font-size: 16px;">Assalam o Alaikum!</p>
            <p style="font-size: 14px; line-height: 1.6;">
                Thank you for subscribing to the <strong>DigitalKasur</strong> newsletter! You will now receive updates about:
            </p>
            <ul style="font-size: 14px; line-height: 1.8; padding-left: 20px;">
                <li>Upcoming events in Kasur District</li>
                <li>New digital services and offers</li>
                <li>Local news and updates</li>
                <li>Job opportunities</li>
                <li>Business directory additions</li>
            </ul>
            <p style="font-size: 14px; line-height: 1.6;">
                Stay connected with Kasur District\'s digital hub!
            </p>
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">
            <p style="font-size: 12px; color: #9ca3af;">
                DigitalKasur.com - Event Management & Digital Services in Kasur District<br>
                <a href="' . SITE_URL . '/pages/privacy.php" style="color: #1E40AF;">Privacy Policy</a> |
                <a href="' . SITE_URL . '/newsletter.php?unsubscribe=' . urlencode($email) . '" style="color: #ef4444;">Unsubscribe</a>
            </p>
        </div>
    </body>
    </html>';

    @send_email($email, $subject, $message);

    // Notify admin about new subscriber
    $admin_subject = 'New Newsletter Subscriber - DigitalKasur';
    $admin_message = "A new user has subscribed to the newsletter:\n\nEmail: {$email}\nDate: " . date('Y-m-d H:i:s') . "\n";
    @send_email(ADMIN_EMAIL, $admin_subject, $admin_message);

    set_flash_message('success', 'Thank you for subscribing to our newsletter! Check your email for a welcome message.');
} else {
    error_log("Newsletter: Failed to insert subscriber - {$email}");
    set_flash_message('error', 'Something went wrong. Please try again later.');
}

// ============================================================
// HANDLE UNSUBSCRIBE (via GET parameter)
// ============================================================

// Check for unsubscribe in GET (for email links)
if (isset($_GET['unsubscribe'])) {
    $unsub_email = trim($_GET['unsubscribe']);

    if (!empty($unsub_email) && filter_var($unsub_email, FILTER_VALIDATE_EMAIL)) {
        $subscriber = DB::selectOne(
            "SELECT id, is_active FROM newsletter_subscribers WHERE email = ?",
            [$unsub_email]
        );

        if ($subscriber && $subscriber['is_active'] == 1) {
            DB::update('newsletter_subscribers', [
                'is_active' => 0,
                'unsubscribed_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$subscriber['id']]);

            set_flash_message('info', 'You have been unsubscribed from our newsletter. We\'re sorry to see you go!');
        } else {
            set_flash_message('info', 'This email is not currently subscribed to our newsletter.');
        }
    } else {
        set_flash_message('error', 'Invalid email address for unsubscribe.');
    }

    redirect(SITE_URL);
    exit();
}

// ============================================================
// RATE LIMIT TRACKING
// ============================================================

$_SESSION['last_newsletter_subscribe'] = time();

// ============================================================
// REDIRECT BACK
// ============================================================

$redirect_url = $_SERVER['HTTP_REFERER'] ?? SITE_URL;

// Validate the redirect URL is from our own site (prevent open redirect)
if (strpos($redirect_url, SITE_URL) !== 0 && strpos($redirect_url, '/') !== 0) {
    $redirect_url = SITE_URL;
}

redirect($redirect_url);
exit();
?>
