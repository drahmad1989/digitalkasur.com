<?php
/**
 * DigitalKasur.com - Configuration File
 * Full CMS Platform for Kasur District
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'digitalk_digitalkasuradmin');
define('DB_PASS', 'Raffay@2026');
define('DB_NAME', 'digitalk_dk');

// Site Configuration
define('SITE_NAME', 'DigitalKasur');
define('SITE_URL', 'https://digitalkasur.com');
define('ADMIN_EMAIL', 'info@digitalkasur.com');
define('ADMIN_PHONE', '+92-333-3197977');
define('ADMIN_WHATSAPP', '923333197977');

// Social Media
define('SOCIAL_FACEBOOK', 'https://facebook.com/digitalkasur');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/digitalkasur');
define('SOCIAL_YOUTUBE', 'https://youtube.com/@digitalkasur');
define('SOCIAL_TWITTER', 'https://twitter.com/digitalkasur');
define('SOCIAL_TIKTOK', 'https://tiktok.com/@digitalkasur');
define('SOCIAL_WHATSAPP_CHANNEL', 'https://whatsapp.com/channel/digitalkasur');
define('SOCIAL_WHATSAPP_GROUP', 'https://chat.whatsapp.com/digitalkasur');

// SEO Configuration
define('SITE_TITLE', 'DigitalKasur - Event Management & Digital Services in Kasur District');
define('SITE_DESCRIPTION', 'Best Event Management and Digital Services in Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan and Theng More. Wedding Planning, Web Development, SEO, Social Media Marketing.');
define('SITE_KEYWORDS', 'Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan, Theng More, Event Management, Digital Services, Wedding Planning, Web Development, SEO, Pakistan, DigitalKasur');

// Path Configuration
define('BASE_PATH', dirname(__FILE__));
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('ADMIN_PATH', BASE_PATH . '/admin/');

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Payment Gateway Configuration
define('JAZZCASH_MERCHANT_ID', '');       // Add your JazzCash Merchant ID
define('JAZZCASH_PASSWORD', '');          // Add your JazzCash Password
define('JAZZCASH_INTEGRITY_SALT', '');    // Add your JazzCash Integrity Salt
define('JAZZCASH_RETURN_URL', SITE_URL . '/payment/jazzcash-return.php');

define('EASYPAISA_MERCHANT_ID', '');      // Add your EasyPaisa Merchant ID
define('EASYPAISA_PASSWORD', '');         // Add your EasyPaisa Password
define('EASYPAISA_RETURN_URL', SITE_URL . '/payment/easypaisa-return.php');

// Payment Mode: 'sandbox' for testing, 'live' for production
define('PAYMENT_MODE', 'sandbox');

// Timezone
date_default_timezone_set('Asia/Karachi');

// Error Reporting (0 in production, E_ALL for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start Session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Language Configuration
define('DEFAULT_LANG', 'en');
define('SUPPORTED_LANGS', ['en' => 'English', 'ur' => 'Roman Urdu']);

// Version
define('APP_VERSION', '2.0.0');
?>
