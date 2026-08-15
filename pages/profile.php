<?php
/**
 * DigitalKasur.com - Profile Page
 * View and edit user profile (requires login)
 */

require_once __DIR__ . '/../config.php';

$page_title = 'My Profile - ' . SITE_NAME;

require_once __DIR__ . '/../header.php';

require_login();

$user = get_logged_user();
if (!$user) {
    set_flash_message('error', 'User not found.');
    redirect('login.php');
}

$errors = [];
$success_msg = '';

// Update Profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $name = clean_input($_POST['name'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $city = clean_input($_POST['city'] ?? '');

    if (empty($name)) $errors[] = 'Name is required.';

    if (empty($errors)) {
        $result = DB::update('users', ['name' => $name, 'phone' => $phone, 'city' => $city], 'id = ?', [$_SESSION['user_id']]);
        if ($result !== false) {
            $_SESSION['user_name'] = $name;
            $success_msg = 'Profile updated successfully!';
            $user = get_logged_user(); // Refresh
        } else {
            $errors[] = 'Failed to update profile.';
        }
    }
}

// Change Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_new_password = $_POST['confirm_new_password'] ?? '';

    if (empty($current_password) || empty($new_password)) {
        $errors[] = 'All password fields are required.';
    } elseif (!password_verify($current_password, $user['password'])) {
        $errors[] = 'Current password is incorrect.';
    } elseif (strlen($new_password) < 6) {
        $errors[] = 'New password must be at least 6 characters.';
    } elseif ($new_password !== $confirm_new_password) {
        $errors[] = 'New passwords do not match.';
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $result = DB::update('users', ['password' => $hashed], 'id = ?', [$_SESSION['user_id']]);
        if ($result !== false) {
            $success_msg = 'Password changed successfully!';
        } else {
            $errors[] = 'Failed to change password.';
        }
    }
}

$cities = get_all_cities();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1 class="page-title">My Profile</h1>
        <p class="page-subtitle">Manage your account settings</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>
                <?php if ($success_msg): ?>
                    <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo $success_msg; ?></div>
                <?php endif; ?>

                <!-- User Info Card -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <div class="business-avatar" style="width:64px;height:64px;font-size:1.5rem;">
                                <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="mb-0"><?php echo htmlspecialchars($user['name']); ?></h4>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($user['email']); ?></p>
                                <span class="category-badge mt-1"><?php echo ucfirst($user['role']); ?> Account</span>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <a href="../admin/index.php" class="btn btn-sm btn-outline-primary ms-2"><i class="fas fa-cog me-1"></i>Admin Panel</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row" style="font-size:0.9rem;">
                            <div class="col-md-4"><i class="fas fa-phone text-primary me-1"></i> <?php echo htmlspecialchars($user['phone'] ?? 'Not set'); ?></div>
                            <div class="col-md-4"><i class="fas fa-map-marker-alt text-primary me-1"></i> <?php echo htmlspecialchars($user['city'] ?? 'Not set'); ?></div>
                            <div class="col-md-4"><i class="fas fa-calendar text-primary me-1"></i> Joined: <?php echo format_date($user['created_at']); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Edit Profile Form -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="fas fa-user-edit text-primary me-2"></i>Edit Profile</h4>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name *</label>
                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($user['name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email (cannot change)</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City</label>
                                    <select name="city" class="form-select">
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $c): ?>
                                            <option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo $user['city'] === $c['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password Form -->
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="fas fa-lock text-warning me-2"></i>Change Password</h4>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="change_password">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Current Password *</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">New Password *</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Confirm New Password *</label>
                                    <input type="password" name="confirm_new_password" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-warning"><i class="fas fa-key me-2"></i>Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
