<?php
/**
 * Authentication Functions - DigitalKasur.com
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get logged-in user data
 */
function get_logged_user() {
    if (!is_logged_in()) return null;
    return DB::selectOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
}

/**
 * Login user
 */
function login_user($email, $password) {
    $user = DB::selectOne("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['avatar'] ?? null;

        // Update last login
        DB::update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);

        return true;
    }
    return false;
}

/**
 * Register new user
 */
function register_user($data) {
    // Check if email exists
    $existing = DB::selectOne("SELECT id FROM users WHERE email = ?", [$data['email']]);
    if ($existing) {
        return ['success' => false, 'message' => 'Email already registered.'];
    }

    // Hash password
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $data['created_at'] = date('Y-m-d H:i:s');

    $user_id = DB::insert('users', $data);

    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['user_email'] = $data['email'];
        $_SESSION['user_role'] = $data['role'] ?? 'user';

        return ['success' => true, 'user_id' => $user_id];
    }

    return ['success' => false, 'message' => 'Registration failed.'];
}

/**
 * Logout user
 */
function logout_user() {
    session_unset();
    session_destroy();
    session_start();
}

/**
 * Require login - redirect if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        $base_path = get_base_path();
        header("Location: {$base_path}login.php");
        exit();
    }
}

/**
 * Require admin - redirect if not admin
 */
function require_admin() {
    if (!is_admin()) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Get base path based on current location
 */
function get_base_path() {
    $current_dir = dirname($_SERVER['PHP_SELF']);

    if (strpos($current_dir, '/pages/cities') !== false) {
        return '../../pages/';
    } elseif (strpos($current_dir, '/pages') !== false) {
        return '';
    } elseif (strpos($current_dir, '/admin') !== false) {
        return '../pages/';
    } else {
        return 'pages/';
    }
}
?>
