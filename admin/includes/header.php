<?php
/**
 * Admin Header - DigitalKasur.com
 * Includes sidebar, top bar, and all CSS/JS includes
 */

if (!isset($page_title)) $page_title = 'Dashboard';
if (!isset($breadcrumb)) $breadcrumb = [];

// Get unread messages count for notification
$unread_count = 0;
try {
    $unread_count = DB::count("messages", "is_read = 0");
} catch (Exception $e) {
    $unread_count = 0;
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo clean_input($page_title); ?> - DigitalKasur Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="fas fa-bolt"></i></div>
            <h4>DigitalKasur</h4>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">Main</div>
            <a href="index.php" class="sidebar-link <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="nav-section-title">Content</div>
            <a href="events.php" class="sidebar-link <?php echo $current_page === 'events.php' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="businesses.php" class="sidebar-link <?php echo $current_page === 'businesses.php' ? 'active' : ''; ?>">
                <i class="fas fa-store"></i> Businesses
            </a>
            <a href="jobs.php" class="sidebar-link <?php echo $current_page === 'jobs.php' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i> Jobs
            </a>
            <a href="news.php" class="sidebar-link <?php echo $current_page === 'news.php' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i> News
            </a>
            <a href="blog.php" class="sidebar-link <?php echo $current_page === 'blog.php' ? 'active' : ''; ?>">
                <i class="fas fa-blog"></i> Blog
            </a>

            <div class="nav-section-title">Management</div>
            <a href="users.php" class="sidebar-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Users
            </a>
            <a href="cities.php" class="sidebar-link <?php echo $current_page === 'cities.php' ? 'active' : ''; ?>">
                <i class="fas fa-city"></i> Cities
            </a>
            <a href="categories.php" class="sidebar-link <?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Categories
            </a>
            <a href="messages.php" class="sidebar-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i> Messages
                <?php if ($unread_count > 0): ?>
                    <span class="badge bg-danger"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-section-title">Finance</div>
            <a href="payments.php" class="sidebar-link <?php echo $current_page === 'payments.php' ? 'active' : ''; ?>">
                <i class="fas fa-credit-card"></i> Payments
            </a>

            <div class="nav-section-title">System</div>
            <a href="settings.php" class="sidebar-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
            <a href="logout.php" class="sidebar-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i></a></li>
                        <?php foreach ($breadcrumb as $key => $value): ?>
                            <?php if ($value === end($breadcrumb)): ?>
                                <li class="breadcrumb-item active"><?php echo clean_input($key); ?></li>
                            <?php else: ?>
                                <li class="breadcrumb-item"><a href="<?php echo clean_input($value); ?>"><?php echo clean_input($key); ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if (empty($breadcrumb)): ?>
                            <li class="breadcrumb-item active"><?php echo clean_input($page_title); ?></li>
                        <?php endif; ?>
                    </ol>
                </nav>
            </div>
            <div class="topbar-right">
                <a href="messages.php" class="topbar-icon" title="Messages">
                    <i class="fas fa-envelope"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="notif-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <div class="admin-profile dropdown">
                    <div class="d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                        <div class="admin-avatar">
                            <?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div>
                            <div class="admin-name"><?php echo clean_input($_SESSION['user_name'] ?? 'Admin'); ?></div>
                            <div class="admin-role"><?php echo ucfirst($_SESSION['user_role'] ?? 'admin'); ?></div>
                        </div>
                        <i class="fas fa-chevron-down ms-1" style="font-size:11px;color:var(--text-muted);"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            <?php display_flash_message(); ?>
