<?php
/**
 * DigitalKasur.com - Register Page
 * User registration form
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Register - ' . SITE_NAME;
$page_description = 'Create your DigitalKasur account today.';

require_once __DIR__ . '/../header.php';

if (is_logged_in()) {
    redirect('../index.php');
}

$cities = get_all_cities();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $city = clean_input($_POST['city'] ?? '');
    $account_type = clean_input($_POST['account_type'] ?? 'user');
    $terms = isset($_POST['terms']);

    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
    if (!$terms) $errors[] = 'You must agree to the Terms of Service.';

    // Check email exists
    $existing = DB::selectOne("SELECT id FROM users WHERE email = ?", [$email]);
    if ($existing) $errors[] = 'This email is already registered.';

    if (empty($errors)) {
        $result = register_user([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'city' => $city,
            'role' => $account_type === 'business' ? 'business' : 'user',
            'is_active' => 1,
            'password' => $password, // Will be hashed by register_user()
        ]);

        if ($result['success']) {
            set_flash_message('success', 'Registration successful! Welcome to DigitalKasur.');
            redirect('../index.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1 class="page-title"><?php _e('register_title'); ?></h1>
        <p class="page-subtitle"><?php _e('register_subtitle'); ?></p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold"><?php _e('register_name'); ?> *</label>
                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('login_email'); ?> *</label>
                                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('register_phone'); ?></label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('login_password'); ?> *</label>
                                    <input type="password" name="password" class="form-control" required minlength="6">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('register_confirm_password'); ?> *</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('register_city'); ?></label>
                                    <select name="city" class="form-select">
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $c): ?>
                                            <option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo ($_POST['city'] ?? '') === $c['name'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"><?php _e('register_account_type'); ?></label>
                                    <select name="account_type" class="form-select">
                                        <option value="user" <?php echo ($_POST['account_type'] ?? 'user') === 'user' ? 'selected' : ''; ?>><?php _e('register_personal'); ?></option>
                                        <option value="business" <?php echo ($_POST['account_type'] ?? '') === 'business' ? 'selected' : ''; ?>><?php _e('register_business'); ?></option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input type="checkbox" name="terms" class="form-check-input" id="termsCheck" required <?php echo isset($_POST['terms']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="termsCheck">
                                            <?php _e('register_terms'); ?>
                                            <a href="terms.php" target="_blank">Terms</a> & <a href="privacy.php" target="_blank">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-user-plus me-2"></i><?php _e('register_btn'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                <?php _e('register_have_account'); ?>
                                <a href="login.php" class="fw-semibold"><?php _e('login_btn'); ?></a>
                            </p>
                        </div>
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
