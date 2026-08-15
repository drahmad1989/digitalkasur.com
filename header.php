<?php
/**
 * Header File - DigitalKasur.com
 * Modern responsive header with dark mode, bilingual, WhatsApp
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/language.php';

// Get current directory path
$current_dir = dirname($_SERVER['PHP_SELF']);
$current_file = basename($_SERVER['PHP_SELF']);
$current_query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';

// Determine base path based on current location
if (strpos($current_dir, '/pages/cities') !== false) {
    $base_path = '../../pages/';
    $assets_path = '../../assets/';
    $city_path = '';
    $is_city = true;
    $home_path = '../../index.php';
} elseif ($current_dir == '/pages' || $current_dir == '\\pages' || $current_dir == '.' . DIRECTORY_SEPARATOR . 'pages') {
    $base_path = '';
    $assets_path = '../assets/';
    $city_path = 'cities/';
    $is_city = false;
    $home_path = '../index.php';
} else {
    $base_path = 'pages/';
    $assets_path = 'assets/';
    $city_path = 'pages/cities/';
    $is_city = false;
    $home_path = 'index.php';
}

// Dark mode preference
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';
if (isset($_GET['theme'])) {
    $theme = $_GET['theme'] === 'dark' ? 'dark' : 'light';
    setcookie('theme', $theme, time() + (86400 * 365), '/');
}

// Get page-specific meta
$page_title = $page_title ?? SITE_TITLE;
$page_description = $page_description ?? SITE_DESCRIPTION;
$page_keywords = $page_keywords ?? SITE_KEYWORDS;
$page_image = $page_image ?? SITE_URL . '/assets/images/logo.jpg';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_name = $is_logged_in ? ($_SESSION['user_name'] ?? 'User') : '';
$user_role = $is_logged_in ? ($_SESSION['user_role'] ?? 'user') : '';
?>
<!DOCTYPE html>
<html lang="<?php echo get_lang() === 'ur' ? 'ur' : 'en'; ?>" dir="<?php echo get_lang() === 'ur' ? 'rtl' : 'ltr'; ?>" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generate_meta_tags($page_title, $page_description, $page_keywords, $page_image); ?>
    <title><?php echo htmlspecialchars($page_title); ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="<?php echo $assets_path; ?>images/logo.jpg">
    <link rel="apple-touch-icon" href="<?php echo $assets_path; ?>images/logo.jpg">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $assets_path; ?>css/style.css">

    <?php echo generate_schema_markup('LocalBusiness'); ?>

    <?php if (isset($schema_markup)) echo $schema_markup; ?>
</head>
<body>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <!-- Left: Phone & Email -->
            <div class="top-bar-left">
                <a href="tel:<?php echo str_replace([' ', '-'], '', ADMIN_PHONE); ?>" class="top-bar-link">
                    <i class="fas fa-phone-alt"></i><span><?php echo ADMIN_PHONE; ?></span>
                </a>
                <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="top-bar-link d-none d-md-inline-flex">
                    <i class="fas fa-envelope"></i><span><?php echo ADMIN_EMAIL; ?></span>
                </a>
            </div>
            <!-- Right: Language, Theme, Social -->
            <div class="top-bar-right">
                <a href="<?php echo lang_toggle_url(); ?>" class="top-bar-link lang-toggle" title="<?php echo lang_toggle_label(); ?>">
                    <i class="fas fa-language"></i><span class="d-none d-sm-inline"><?php echo lang_toggle_label(); ?></span>
                </a>
                <a href="?theme=<?php echo $theme === 'dark' ? 'light' : 'dark'; ?>" class="top-bar-link theme-toggle" title="Toggle Dark Mode">
                    <i class="fas fa-<?php echo $theme === 'dark' ? 'sun' : 'moon'; ?>"></i><span class="d-none d-sm-inline"><?php echo $theme === 'dark' ? 'Light' : 'Dark'; ?></span>
                </a>
                <span class="top-bar-divider d-none d-md-inline"></span>
                <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener" class="top-bar-social"><i class="fab fa-facebook-f"></i></a>
                <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener" class="top-bar-social"><i class="fab fa-instagram"></i></a>
                <a href="<?php echo SOCIAL_YOUTUBE; ?>" target="_blank" rel="noopener" class="top-bar-social"><i class="fab fa-youtube"></i></a>
                <a href="<?php echo SOCIAL_TIKTOK; ?>" target="_blank" rel="noopener" class="top-bar-social"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="<?php echo $home_path; ?>">
                <img src="<?php echo $assets_path; ?>images/logo.jpg" alt="DigitalKasur Logo" height="36"
                     onerror="this.onerror=null; this.style.display='none'; document.getElementById('brandText').classList.remove('d-none');">
                <span class="brand-text d-none" id="brandText">Digital<span>Kasur</span></span>
            </a>

            <!-- Mobile Search + Toggle -->
            <div class="d-flex align-items-center gap-2">
                <a href="<?php echo $base_path; ?>search.php" class="btn mobile-search-btn d-lg-none" aria-label="Search">
                    <i class="fas fa-search"></i>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Nav Links -->
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_file == 'index.php' && !$is_city ? 'active' : ''; ?>" href="<?php echo $home_path; ?>">
                            <i class="fas fa-home me-1 d-none d-lg-inline"></i><?php _e('nav_home'); ?>
                        </a>
                    </li>

                    <!-- Event Services Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_file == 'events.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-calendar-alt me-1 d-none d-lg-inline"></i><?php _e('nav_events'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=wedding"><i class="fas fa-ring me-2 text-primary"></i><?php _e('events_wedding'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=birthday"><i class="fas fa-birthday-cake me-2 text-warning"></i><?php _e('events_birthday'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=corporate"><i class="fas fa-briefcase me-2 text-info"></i><?php _e('events_corporate'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=seminar"><i class="fas fa-chalkboard-teacher me-2 text-success"></i><?php _e('events_seminar'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=festival"><i class="fas fa-campground me-2 text-danger"></i><?php _e('events_festival'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=concert"><i class="fas fa-music me-2 text-purple"></i><?php _e('events_concert'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=ceremony"><i class="fas fa-award me-2 text-secondary"></i><?php _e('events_ceremony'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>events.php?type=rally"><i class="fas fa-flag me-2 text-dark"></i><?php _e('events_rally'); ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-bold" href="<?php echo $base_path; ?>events.php"><i class="fas fa-th-list me-2"></i><?php _e('events_view_all'); ?></a></li>
                        </ul>
                    </li>

                    <!-- Digital Services Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_file == 'digital-services.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-laptop-code me-1 d-none d-lg-inline"></i><?php _e('nav_digital'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=website"><i class="fas fa-globe me-2 text-primary"></i><?php _e('digital_web'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=ecommerce"><i class="fas fa-shopping-cart me-2 text-success"></i><?php _e('digital_ecommerce'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=mobile"><i class="fas fa-mobile-alt me-2 text-info"></i><?php _e('digital_mobile'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=design"><i class="fas fa-paint-brush me-2 text-warning"></i><?php _e('digital_design'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=seo"><i class="fas fa-search me-2 text-danger"></i><?php _e('digital_seo'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=social"><i class="fas fa-share-alt me-2 text-purple"></i><?php _e('digital_social'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=page"><i class="fas fa-file-alt me-2 text-secondary"></i><?php _e('digital_page'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=content"><i class="fas fa-pen-fancy me-2 text-dark"></i><?php _e('digital_content'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>digital-services.php?type=video"><i class="fas fa-video me-2 text-danger"></i><?php _e('digital_video'); ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-bold" href="<?php echo $base_path; ?>digital-services.php"><i class="fas fa-th-list me-2"></i><?php _e('digital_view_all'); ?></a></li>
                        </ul>
                    </li>

                    <!-- Business Directory -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo ($current_file == 'business-directory.php') ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-store me-1 d-none d-lg-inline"></i><?php _e('nav_business'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>business-directory.php"><i class="fas fa-list me-2"></i><?php _e('biz_browse'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path; ?>business-directory.php?featured=1"><i class="fas fa-star me-2 text-warning"></i><?php _e('biz_featured'); ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item fw-bold" href="<?php echo $base_path; ?>business-register.php"><i class="fas fa-plus-circle me-2 text-success"></i><?php _e('biz_add'); ?></a></li>
                        </ul>
                    </li>

                    <!-- Jobs -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_file == 'jobs.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>jobs.php">
                            <i class="fas fa-briefcase me-1 d-none d-lg-inline"></i><?php _e('nav_jobs'); ?>
                        </a>
                    </li>

                    <!-- News -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_file == 'news.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>news.php">
                            <i class="fas fa-newspaper me-1 d-none d-lg-inline"></i><?php _e('nav_news'); ?>
                        </a>
                    </li>

                    <!-- Cities Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-map-marker-alt me-1 d-none d-lg-inline"></i><?php _e('nav_cities'); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>kasur.php"><i class="fas fa-landmark me-2 text-primary"></i>Kasur (District HQ)</a></li>
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>pattoki.php"><i class="fas fa-store me-2 text-success"></i>Pattoki (Tehsil)</a></li>
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>phool-nagar.php"><i class="fas fa-seedling me-2 text-warning"></i>Phool Nagar</a></li>
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>kot-radha-kishan.php"><i class="fas fa-home me-2 text-info"></i>Kot Radha Kishan</a></li>
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>chunian.php"><i class="fas fa-wheat-awn me-2 text-warning"></i>Chunian (Tehsil)</a></li>
                            <li><a class="dropdown-item" href="<?php echo $city_path; ?>theng-more.php"><i class="fas fa-building me-2 text-secondary"></i>Theng More (Ellah Abad)</a></li>
                        </ul>
                    </li>

                    <!-- Blog -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_file == 'blog.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>blog.php">
                            <i class="fas fa-blog me-1 d-none d-lg-inline"></i><?php _e('nav_blog'); ?>
                        </a>
                    </li>

                    <!-- Contact -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_file == 'contact.php') ? 'active' : ''; ?>" href="<?php echo $base_path; ?>contact.php">
                            <i class="fas fa-envelope me-1 d-none d-lg-inline"></i><?php _e('nav_contact'); ?>
                        </a>
                    </li>
                </ul>

                <!-- Right Side: Search + Auth -->
                <div class="d-flex align-items-center gap-2 nav-right">
                    <!-- Search -->
                    <div class="search-box">
                        <input type="text" class="form-control search-input" placeholder="<?php _e('nav_search'); ?>" id="searchInput" autocomplete="off">
                        <button class="btn search-btn" onclick="searchSite()" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <?php if ($is_logged_in): ?>
                        <!-- User Menu -->
                        <div class="dropdown">
                            <button class="btn user-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i>
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($user_name); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted"><?php echo ucfirst($user_role); ?> Account</span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <?php if ($user_role === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?php echo $base_path; ?>../admin/index.php"><i class="fas fa-cog me-2"></i>Admin Panel</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo $base_path; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <!-- Login Button -->
                        <a href="<?php echo $base_path; ?>login.php" class="btn login-btn">
                            <i class="fas fa-sign-in-alt me-1"></i><span class="d-none d-sm-inline"><?php _e('nav_login'); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php display_flash_message(); ?>
    </div>

    <script>
        function searchSite() {
            const query = document.getElementById('searchInput').value.trim();
            if (query) {
                window.location.href = '<?php echo $base_path; ?>search.php?q=' + encodeURIComponent(query);
            }
        }

        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') searchSite();
        });
    </script>
