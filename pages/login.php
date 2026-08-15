<?php
/**
 * DigitalKasur.com - Login Page
 * User authentication - no demo credentials visible (security)
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Login - ' . SITE_NAME;
$page_description = 'Login to your DigitalKasur account.';

require_once __DIR__ . '/../header.php';

// Redirect if already logged in
if (is_logged_in()) {
    redirect('../index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? ''; // Don't clean password

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        if (login_user($email, $password)) {
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <h1 class="page-title"><?php _e('login_title'); ?></h1>
        <p class="page-subtitle"><?php _e('login_welcome'); ?></p>
    </div>
</section>

<!-- Login Form -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div style="width:64px;height:64px;border-radius:50%;background:rgba(var(--primary-rgb),0.1);display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--primary-color);">
                                <i class="fas fa-user-lock"></i>
                            </div>
                        </div>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><?php _e('login_email'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="email" class="form-control" required placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"><?php _e('login_password'); ?></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" required placeholder="Enter your password" id="passwordField">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                                    <label class="form-check-label" for="rememberMe">Remember me</label>
                                </div>
                                <a href="#" class="text-decoration-none" style="font-size:0.9rem;"><?php _e('login_forgot'); ?></a>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-sign-in-alt me-2"></i><?php _e('login_btn'); ?>
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                <?php _e('login_no_account'); ?>
                                <a href="register.php" class="fw-semibold"><?php _e('login_register'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function togglePassword() {
    const field = document.getElementById('passwordField');
    const icon = document.getElementById('toggleIcon');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.form-control, .form-select, .input-group-text { border-color: var(--border-color); background: var(--bg-light); color: var(--text-color); }
.form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
