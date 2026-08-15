<?php
/**
 * DigitalKasur.com - Data Deletion Policy & Callback Page (Root Level)
 * URL: https://digitalkasur.com/data-deletion.php
 *
 * This page serves TWO purposes:
 *  1. Facebook App Data Deletion Callback — receives POST requests from Facebook
 *     with a signed_request when a user requests account deletion via Facebook.
 *     See: https://developers.facebook.com/docs/apps/review/login-permissions
 *  2. Human-readable instructions page — tells users how to request data deletion
 *     from DigitalKasur.com directly (without going through Facebook).
 */

require_once __DIR__ . '/config.php';

/**
 * Handle Facebook Data Deletion Request (POST signed_request)
 *
 * Facebook sends a POST request with `signed_request` containing:
 *   - algorithm
 *   - expires_in
 *   - issued_at
 *   - user_id
 *   - code
 *
 * On receipt we MUST:
 *   1. Verify the signature using the FB App Secret
 *   2. Store the request in a queue (we log it to a file)
 *   3. Respond with JSON: { url: <status_url>, confirmation_code: <code> }
 */
function handle_facebook_data_deletion_request() {
    // Skip if no signed_request — means this is a normal human visitor
    if (empty($_POST['signed_request'])) {
        return null;
    }

    // Facebook App Secret (set in config or hardcode after FB App Review)
    $app_secret = defined('FB_APP_SECRET') ? FB_APP_SECRET : '';

    list($encoded_sig, $payload) = explode('.', $_POST['signed_request'], 2);

    // Decode signature
    $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
    // Decode payload
    $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

    // Verify algorithm
    if (empty($data['algorithm']) || strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        http_response_code(400);
        echo json_encode(['error' => 'Unknown algorithm']);
        exit;
    }

    // Verify signature if app secret is configured
    if ($app_secret) {
        $expected_sig = hash_hmac('sha256', $payload, $app_secret, true);
        if (!hash_equals($sig, $expected_sig)) {
            http_response_code(400);
            echo json_encode(['error' => 'Bad Signed JSON signature']);
            exit;
        }
    }

    // Generate a confirmation code for tracking
    $confirmation_code = 'DK-DEL-' . strtoupper(substr(md5(uniqid('', true)), 0, 10));
    $fb_user_id = $data['user_id'] ?? 'unknown';

    // Log the deletion request to a file (server-side queue)
    $log_dir = __DIR__ . '/uploads/logs';
    if (!is_dir($log_dir)) {
        @mkdir($log_dir, 0755, true);
    }
    $log_entry = date('Y-m-d H:i:s') . " | Code: {$confirmation_code} | FB UserID: {$fb_user_id} | Status: pending\n";
    @file_put_contents($log_dir . '/data_deletion_requests.log', $log_entry, FILE_APPEND);

    // Try to send email notification to admin
    $email_body = "A new Facebook data deletion request has been received.\n\n" .
                  "Confirmation Code: {$confirmation_code}\n" .
                  "Facebook User ID: {$fb_user_id}\n" .
                  "Received: " . date('Y-m-d H:i:s') . "\n\n" .
                  "Action required: Within 48 hours, locate any user accounts linked to this FB user ID, " .
                  "anonymize/delete their personal data, and update the log entry to 'completed'.";
    @mail(ADMIN_EMAIL, '[DigitalKasur] FB Data Deletion Request ' . $confirmation_code, $email_body);

    // Build the status URL — Facebook polls this URL to check deletion status
    $status_url = SITE_URL . '/data-deletion.php?code=' . urlencode($confirmation_code);

    // Respond to Facebook with the required JSON
    header('Content-Type: application/json');
    echo json_encode([
        'url'                => $status_url,
        'confirmation_code'  => $confirmation_code,
    ]);
    exit;
}

// Try handling FB callback; returns null for normal visitors
handle_facebook_data_deletion_request();

// --- Below: Normal HTML page for human visitors ---

$page_title = 'Data Deletion Policy - ' . SITE_NAME;
$page_description = 'Data Deletion Policy for DigitalKasur.com - Learn how to request deletion of your personal data, including Facebook-connected account data.';

require_once __DIR__ . '/header.php';

// If ?code=XXX is set in URL, show a status check response (for Facebook polling)
$status_message = '';
if (!empty($_GET['code'])) {
    $code = preg_replace('/[^A-Z0-9\-]/', '', strtoupper($_GET['code']));
    $log_file = __DIR__ . '/uploads/logs/data_deletion_requests.log';
    $status_message = 'pending'; // default optimistic response
    if (file_exists($log_file)) {
        $lines = file($log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, $code) !== false) {
                if (strpos($line, 'Status: completed') !== false) {
                    $status_message = 'completed';
                } elseif (strpos($line, 'Status: pending') !== false) {
                    $status_message = 'pending';
                }
                break;
            }
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1 class="page-title">Data Deletion Policy</h1>
        <p class="page-subtitle">Last updated: June 2026</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="legal-content">

                    <?php if ($status_message): ?>
                        <!-- Facebook status poll response -->
                        <div class="alert alert-info" style="background:#e7f4ff;border-left:4px solid var(--primary-color);padding:1rem 1.25rem;border-radius:6px;margin-bottom:2rem;">
                            <strong>Data Deletion Status Check</strong><br>
                            Confirmation Code: <code><?php echo htmlspecialchars($code); ?></code><br>
                            Current Status: <strong><?php echo ucfirst($status_message); ?></strong><br>
                            <?php if ($status_message === 'pending'): ?>
                                Your data deletion request has been received and is being processed.
                                We will complete the deletion within 48 hours of receipt.
                            <?php else: ?>
                                Your data deletion request has been completed. All linked personal data has been permanently removed from our systems.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <h2>1. Overview</h2>
                    <p>At DigitalKasur.com, we respect your right to control your personal information. This Data Deletion Policy explains how you can request the deletion of your personal data from our systems, including data collected through your Facebook account if you used Facebook Login to register on our platform. This policy is provided in compliance with Facebook's Platform Policies and Pakistani data protection principles.</p>
                    <p>When you request data deletion, we will permanently remove your personal information from our active systems within 48 hours of receiving your verified request. Some data may be retained for a longer period where required by Pakistani law (for example, financial transaction records required for tax compliance).</p>

                    <h2>2. What Data Can Be Deleted</h2>
                    <p>When you submit a data deletion request, the following types of data associated with your account will be deleted or anonymized:</p>
                    <ul>
                        <li>Personal profile information (name, email, phone, address)</li>
                        <li>Account credentials (username, hashed password, security questions)</li>
                        <li>Business directory listings you have created</li>
                        <li>Job postings you have submitted as an employer</li>
                        <li>Job applications you have submitted as a job seeker</li>
                        <li>Event registration records</li>
                        <li>Newsletter subscription</li>
                        <li>Profile picture and uploaded media files</li>
                        <li>Comments, reviews, and other user-generated content (your name will be anonymized to "Deleted User" where the content itself must be retained for context)</li>
                        <li>Session data, login history, and activity logs</li>
                    </ul>

                    <h2>3. Data That May Be Retained</h2>
                    <p>In certain limited circumstances, we may retain specific data even after a deletion request, where required by law or for legitimate business purposes:</p>
                    <ul>
                        <li><strong>Financial Records:</strong> Payment transaction records are retained for 5 years as required by Pakistani tax law</li>
                        <li><strong>Legal Hold:</strong> Data subject to ongoing legal proceedings or investigations</li>
                        <li><strong>Fraud Prevention:</strong> Anonymized data points used to prevent fraudulent re-registration</li>
                        <li><strong>Backup Data:</strong> Data may exist in encrypted backups for up to 30 days before being permanently purged</li>
                    </ul>

                    <h2>4. How to Request Data Deletion</h2>
                    <p>We provide multiple convenient methods for you to request deletion of your personal data. Choose whichever method is most convenient for you:</p>

                    <h3>4.1 Via Facebook (For Facebook Login Users)</h3>
                    <p>If you registered on DigitalKasur.com using your Facebook account, you can request data deletion directly through Facebook's interface:</p>
                    <ul>
                        <li>Go to <strong>Facebook Settings &rarr; Apps and Websites</strong></li>
                        <li>Find <strong>DigitalKasur</strong> in the list of connected apps</li>
                        <li>Click <strong>Remove</strong> and select the option to delete your activities</li>
                        <li>Facebook will automatically send a data deletion request to our callback URL (this page)</li>
                        <li>You will receive a <strong>confirmation code</strong> — save it to track your request status</li>
                    </ul>
                    <p>Our system will process Facebook-initiated requests within 48 hours and provide status updates through the confirmation code lookup on this page.</p>

                    <h3>4.2 Via Email</h3>
                    <p>Send a data deletion request email to <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a> with the subject line "Data Deletion Request". Please include:</p>
                    <ul>
                        <li>Your registered email address</li>
                        <li>Your registered phone number (for verification)</li>
                        <li>A clear statement requesting data deletion</li>
                        <li>A government-issued ID copy (only if account verification is required — we will delete the ID copy immediately after verification)</li>
                    </ul>

                    <h3>4.3 Via WhatsApp</h3>
                    <p>Send a message to our WhatsApp number <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>" target="_blank" rel="noopener"><?php echo ADMIN_PHONE; ?></a> with the text "Data Deletion Request" along with your registered email address. Our support team will guide you through the verification process.</p>

                    <h3>4.4 Via Phone</h3>
                    <p>Call us at <?php echo ADMIN_PHONE; ?> during business hours (9 AM to 6 PM PKT, Monday to Saturday) and request data deletion. Our representative will verify your identity and process the request.</p>

                    <h3>4.5 Via Website Contact Form</h3>
                    <p>Visit our <a href="<?php echo SITE_URL; ?>/pages/contact.php">Contact Page</a> and submit a message with the subject "Data Deletion Request" along with your account details.</p>

                    <h2>5. Verification Process</h2>
                    <p>For your security, we verify the identity of the requester before processing any data deletion. The verification process typically involves:</p>
                    <ul>
                        <li>Matching the request details (email, phone) with our account records</li>
                        <li>Sending a one-time verification code to your registered email or phone</li>
                        <li>Confirming the code within 24 hours</li>
                        <li>Processing the deletion once verification is complete</li>
                    </ul>
                    <p>If you have lost access to your registered email and phone, please contact us with alternative proof of identity.</p>

                    <h2>6. Processing Timeline</h2>
                    <p>Our commitment to data deletion timelines:</p>
                    <ul>
                        <li><strong>Acknowledgment:</strong> Within 24 hours of receiving your request</li>
                        <li><strong>Verification:</strong> Within 24 hours of receiving your request</li>
                        <li><strong>Deletion from active systems:</strong> Within 48 hours of verification</li>
                        <li><strong>Deletion from backups:</strong> Within 30 days (backups are rotated and overwritten)</li>
                        <li><strong>Email confirmation:</strong> Sent to your registered email once deletion is complete</li>
                    </ul>

                    <h2>7. Tracking Your Request</h2>
                    <p>If you received a confirmation code (starting with "DK-DEL-"), you can check the status of your data deletion request below. Simply enter your confirmation code in the field and submit.</p>

                    <form method="get" action="" class="data-deletion-status-form" style="background:#f8f9fa;padding:1.5rem;border-radius:8px;margin:1.5rem 0;">
                        <div class="form-group" style="margin-bottom:1rem;">
                            <label for="code" style="display:block;font-weight:600;margin-bottom:0.5rem;">Confirmation Code:</label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="DK-DEL-XXXXXXXXXX" value="<?php echo isset($code) ? htmlspecialchars($code) : ''; ?>" style="width:100%;padding:0.6rem 0.8rem;border:1px solid #ddd;border-radius:4px;font-family:monospace;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="background:var(--primary-color);color:white;border:none;padding:0.7rem 1.5rem;border-radius:4px;cursor:pointer;font-weight:600;">Check Status</button>
                    </form>

                    <h2>8. Partial Deletion Requests</h2>
                    <p>If you wish to delete only specific data while keeping your account active (for example, deleting a business listing but keeping your user account), you can do so directly from your <a href="<?php echo SITE_URL; ?>/pages/profile.php">Profile Page</a>. Most user-generated content can be edited or deleted by the account holder without contacting support.</p>

                    <h2>9. Consequences of Account Deletion</h2>
                    <p>Please be aware that when you request full account deletion:</p>
                    <ul>
                        <li>You will lose access to all features that require an account</li>
                        <li>Your business listings will be removed from the directory</li>
                        <li>Your job postings and applications will be permanently removed</li>
                        <li>Any active event registrations will be canceled (subject to the refund policy in our <a href="<?php echo SITE_URL; ?>/terms.php">Terms of Service</a>)</li>
                        <li>Newsletter subscriptions will be canceled</li>
                        <li>This action cannot be undone — you will need to register again if you wish to use our services in the future</li>
                    </ul>

                    <h2>10. Children's Data</h2>
                    <p>If you believe that a child under the age of 18 has provided personal information to us without parental consent, please contact us immediately at <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a>. We will take steps to verify the situation and delete the child's information promptly.</p>

                    <h2>11. Third-Party Data</h2>
                    <p>If you used Facebook, Google, or another third-party service to log in to DigitalKasur.com, requesting data deletion from us only deletes data stored on our systems. To delete data held by the third-party provider, you must separately request deletion from them (e.g., via Facebook's App Settings or Google's Account Settings).</p>

                    <h2>12. Changes to This Policy</h2>
                    <p>We may update this Data Deletion Policy from time to time. Changes will be effective immediately upon posting on this page with an updated "Last updated" date. We encourage you to review this page periodically to stay informed about how we handle data deletion requests.</p>

                    <h2>13. Contact Information</h2>
                    <p>For any questions, concerns, or to submit a data deletion request, please contact us:</p>
                    <ul>
                        <li><strong>DigitalKasur.com</strong></li>
                        <li>Email: <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></li>
                        <li>Phone: <?php echo ADMIN_PHONE; ?></li>
                        <li>WhatsApp: <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>" target="_blank" rel="noopener"><?php echo ADMIN_PHONE; ?></a></li>
                        <li>Address: Kasur, Punjab, Pakistan</li>
                    </ul>

                    <p class="text-muted mt-4" style="font-size:0.85rem;">This Data Deletion Policy is governed by the laws of Pakistan, including the Prevention of Electronic Crimes Act, 2016. It complements our <a href="<?php echo SITE_URL; ?>/privacy-policy.php">Privacy Policy</a> and <a href="<?php echo SITE_URL; ?>/terms.php">Terms of Service</a>. For Facebook App Review purposes, the callback URL for data deletion requests is: <code><?php echo SITE_URL; ?>/data-deletion.php</code></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: 0; }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.legal-content h2 { font-size: 1.4rem; margin-top: 2rem; margin-bottom: 0.75rem; color: var(--primary-color); border-bottom: 2px solid var(--primary-color); padding-bottom: 0.5rem; }
.legal-content h3 { font-size: 1.1rem; margin-top: 1.5rem; margin-bottom: 0.5rem; color: var(--heading-color); }
.legal-content p { line-height: 1.75; margin-bottom: 1rem; color: var(--text-color); }
.legal-content ul { padding-left: 1.5rem; margin-bottom: 1rem; }
.legal-content ul li { list-style: disc; padding: 0.25rem 0; color: var(--text-color); line-height: 1.6; }
.legal-content a { color: var(--primary-color); text-decoration: underline; }
.legal-content code { background: #f1f3f5; padding: 0.15rem 0.4rem; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 0.9em; color: #c92a2a; }
.data-deletion-status-form input:focus { outline: none; border-color: var(--primary-color); }
.data-deletion-status-form button:hover { opacity: 0.9; }
</style>

<?php require_once __DIR__ . '/footer.php'; ?>
