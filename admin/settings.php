<?php
/**
 * Admin Settings - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Settings';
$breadcrumb = ['Settings' => 'settings.php'];

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.');
        redirect('settings.php');
    }

    $section = clean_input($_POST['section'] ?? 'general');

    switch ($section) {
        case 'general':
            $fields = ['site_name', 'site_tagline', 'site_email', 'site_phone', 'site_whatsapp', 'site_address'];
            foreach ($fields as $field) {
                set_setting($field, clean_input($_POST[$field] ?? ''));
            }
            set_flash_message('success', 'General settings saved.');
            break;

        case 'social':
            $fields = ['social_facebook', 'social_instagram', 'social_youtube', 'social_twitter', 'social_tiktok', 'social_whatsapp_channel', 'social_whatsapp_group'];
            foreach ($fields as $field) {
                set_setting($field, clean_input($_POST[$field] ?? ''));
            }
            set_flash_message('success', 'Social media settings saved.');
            break;

        case 'payment':
            $fields = ['jazzcash_merchant_id', 'jazzcash_password', 'jazzcash_integrity_salt', 'easypaisa_merchant_id', 'easypaisa_password', 'payment_mode'];
            foreach ($fields as $field) {
                set_setting($field, clean_input($_POST[$field] ?? ''));
            }
            set_flash_message('success', 'Payment settings saved.');
            break;

        case 'seo':
            $fields = ['seo_title', 'seo_description', 'seo_keywords', 'google_analytics_id', 'google_tag_manager_id'];
            foreach ($fields as $field) {
                set_setting($field, clean_input($_POST[$field] ?? ''));
            }
            set_flash_message('success', 'SEO settings saved.');
            break;

        default:
            set_flash_message('error', 'Invalid section.');
            break;
    }
    redirect('settings.php?section=' . $section);
}

$active_section = clean_input($_GET['section'] ?? 'general');

// Get current settings
$settings_keys = [
    'site_name', 'site_tagline', 'site_email', 'site_phone', 'site_whatsapp', 'site_address',
    'social_facebook', 'social_instagram', 'social_youtube', 'social_twitter', 'social_tiktok', 'social_whatsapp_channel', 'social_whatsapp_group',
    'jazzcash_merchant_id', 'jazzcash_password', 'jazzcash_integrity_salt', 'easypaisa_merchant_id', 'easypaisa_password', 'payment_mode',
    'seo_title', 'seo_description', 'seo_keywords', 'google_analytics_id', 'google_tag_manager_id'
];
$settings = [];
foreach ($settings_keys as $key) {
    $settings[$key] = get_setting($key);
}

include 'includes/header.php';
?>

<div class="page-header">
    <div><h1 class="page-title">Settings</h1><p class="page-subtitle">Manage site configuration</p></div>
</div>

<!-- Settings Tabs -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?php echo $active_section==='general'?'active':''; ?>" href="?section=general"><i class="fas fa-cog me-1"></i> General</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_section==='social'?'active':''; ?>" href="?section=social"><i class="fas fa-share-alt me-1"></i> Social Media</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_section==='payment'?'active':''; ?>" href="?section=payment"><i class="fas fa-credit-card me-1"></i> Payments</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo $active_section==='seo'?'active':''; ?>" href="?section=seo"><i class="fas fa-search me-1"></i> SEO</a>
    </li>
</ul>

<?php if ($active_section === 'general'): ?>
<!-- General Settings -->
<div class="settings-section">
    <h5><i class="fas fa-globe"></i> General Settings</h5>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="section" value="general">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" class="form-control" value="<?php echo clean_input($settings['site_name'] ?? SITE_NAME); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Site Tagline</label>
                    <input type="text" name="site_tagline" class="form-control" value="<?php echo clean_input($settings['site_tagline'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Contact Email</label>
                    <input type="email" name="site_email" class="form-control" value="<?php echo clean_input($settings['site_email'] ?? ADMIN_EMAIL); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="site_phone" class="form-control" value="<?php echo clean_input($settings['site_phone'] ?? ADMIN_PHONE); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="text" name="site_whatsapp" class="form-control" value="<?php echo clean_input($settings['site_whatsapp'] ?? ADMIN_WHATSAPP); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="site_address" class="form-control" value="<?php echo clean_input($settings['site_address'] ?? ''); ?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i> Save General Settings</button>
    </form>
</div>

<?php elseif ($active_section === 'social'): ?>
<!-- Social Media Settings -->
<div class="settings-section">
    <h5><i class="fas fa-share-alt"></i> Social Media Links</h5>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="section" value="social">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-facebook text-primary me-1"></i> Facebook</label>
                    <input type="url" name="social_facebook" class="form-control" value="<?php echo clean_input($settings['social_facebook'] ?? SOCIAL_FACEBOOK); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-instagram text-danger me-1"></i> Instagram</label>
                    <input type="url" name="social_instagram" class="form-control" value="<?php echo clean_input($settings['social_instagram'] ?? SOCIAL_INSTAGRAM); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-youtube text-danger me-1"></i> YouTube</label>
                    <input type="url" name="social_youtube" class="form-control" value="<?php echo clean_input($settings['social_youtube'] ?? SOCIAL_YOUTUBE); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-twitter text-info me-1"></i> Twitter</label>
                    <input type="url" name="social_twitter" class="form-control" value="<?php echo clean_input($settings['social_twitter'] ?? SOCIAL_TWITTER); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-tiktok me-1"></i> TikTok</label>
                    <input type="url" name="social_tiktok" class="form-control" value="<?php echo clean_input($settings['social_tiktok'] ?? SOCIAL_TIKTOK); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp Channel</label>
                    <input type="url" name="social_whatsapp_channel" class="form-control" value="<?php echo clean_input($settings['social_whatsapp_channel'] ?? SOCIAL_WHATSAPP_CHANNEL); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-whatsapp text-success me-1"></i> WhatsApp Group</label>
                    <input type="url" name="social_whatsapp_group" class="form-control" value="<?php echo clean_input($settings['social_whatsapp_group'] ?? SOCIAL_WHATSAPP_GROUP); ?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i> Save Social Settings</button>
    </form>
</div>

<?php elseif ($active_section === 'payment'): ?>
<!-- Payment Settings -->
<div class="settings-section">
    <h5><i class="fas fa-mobile-alt"></i> JazzCash Settings</h5>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="section" value="payment">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Merchant ID</label>
                    <input type="text" name="jazzcash_merchant_id" class="form-control" value="<?php echo clean_input($settings['jazzcash_merchant_id'] ?? JAZZCASH_MERCHANT_ID); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="jazzcash_password" class="form-control" value="<?php echo clean_input($settings['jazzcash_password'] ?? JAZZCASH_PASSWORD); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Integrity Salt</label>
                    <input type="password" name="jazzcash_integrity_salt" class="form-control" value="<?php echo clean_input($settings['jazzcash_integrity_salt'] ?? JAZZCASH_INTEGRITY_SALT); ?>">
                </div>
            </div>
        </div>

        <h5 class="mt-4"><i class="fas fa-wallet"></i> EasyPaisa Settings</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Merchant ID</label>
                    <input type="text" name="easypaisa_merchant_id" class="form-control" value="<?php echo clean_input($settings['easypaisa_merchant_id'] ?? EASYPAISA_MERCHANT_ID); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="easypaisa_password" class="form-control" value="<?php echo clean_input($settings['easypaisa_password'] ?? EASYPAISA_PASSWORD); ?>">
                </div>
            </div>
        </div>

        <h5 class="mt-4"><i class="fas fa-sliders-h"></i> Payment Mode</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Mode</label>
                    <select name="payment_mode" class="form-select">
                        <option value="sandbox" <?php echo ($settings['payment_mode'] ?? PAYMENT_MODE)==='sandbox'?'selected':''; ?>>Sandbox (Testing)</option>
                        <option value="live" <?php echo ($settings['payment_mode'] ?? PAYMENT_MODE)==='live'?'selected':''; ?>>Live (Production)</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i> Save Payment Settings</button>
    </form>
</div>

<?php elseif ($active_section === 'seo'): ?>
<!-- SEO Settings -->
<div class="settings-section">
    <h5><i class="fas fa-search"></i> SEO Settings</h5>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="section" value="seo">
        <div class="row g-3">
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Default SEO Title</label>
                    <input type="text" name="seo_title" class="form-control" value="<?php echo clean_input($settings['seo_title'] ?? SITE_TITLE); ?>">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Default Meta Description</label>
                    <textarea name="seo_description" class="form-control" rows="3"><?php echo clean_input($settings['seo_description'] ?? SITE_DESCRIPTION); ?></textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">Default Keywords</label>
                    <textarea name="seo_keywords" class="form-control" rows="2"><?php echo clean_input($settings['seo_keywords'] ?? SITE_KEYWORDS); ?></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-google me-1"></i> Google Analytics ID</label>
                    <input type="text" name="google_analytics_id" class="form-control" placeholder="e.g. G-XXXXXXXXXX" value="<?php echo clean_input($settings['google_analytics_id'] ?? ''); ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label"><i class="fab fa-google me-1"></i> Google Tag Manager ID</label>
                    <input type="text" name="google_tag_manager_id" class="form-control" placeholder="e.g. GTM-XXXXXXX" value="<?php echo clean_input($settings['google_tag_manager_id'] ?? ''); ?>">
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3"><i class="fas fa-save me-1"></i> Save SEO Settings</button>
    </form>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
