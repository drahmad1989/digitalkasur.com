<?php
/**
 * DigitalKasur.com - Header Include
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/language.php';
require_once __DIR__ . '/auth.php';

$home = getHomePath();
$settings = getSettings();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));

// Determine active page for nav highlighting
$activePage = '';
if ($currentDir === 'pages') {
    $activePage = $currentPage;
} elseif ($currentPage === 'index' && $currentDir === 'public_html') {
    $activePage = 'home';
}

// Dark mode preference
$darkMode = isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'true' ? 'dark' : '';
?>
<!DOCTYPE html>
<html lang="<?php echo currentLang() === 'ur' ? 'ur' : 'en'; ?>" dir="ltr" class="<?php echo $darkMode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($pageDescription) ? clean($pageDescription) : 'Digital Kasur - Your Digital Gateway to Kasur City'; ?>">
    <meta name="keywords" content="Kasur, Digital Kasur, Kasur Events, Kasur Jobs, Kasur News, Kasur Business, Kasur Services">
    <meta name="author" content="Digital Kasur">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo isset($pageTitle) ? clean($pageTitle) : 'Digital Kasur'; ?>">
    <meta property="og:description" content="<?php echo isset($pageDescription) ? clean($pageDescription) : 'Your Digital Gateway to Kasur City'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo SITE_URL . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:site_name" content="Digital Kasur">

    <!-- PWA -->
    <meta name="theme-color" content="#0d6efd">
    <link rel="manifest" href="<?php echo $home; ?>manifest.json">

    <title><?php echo isset($pageTitle) ? clean($pageTitle) . ' | ' : ''; ?>Digital Kasur</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $home; ?>assets/css/style.css" rel="stylesheet">

    <?php if (isset($extraCSS)): ?>
    <link href="<?php echo $home; ?>assets/css/<?php echo $extraCSS; ?>" rel="stylesheet">
    <?php endif; ?>

    <!-- Schema.org JSON-LD -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Digital Kasur",
        "url": "<?php echo SITE_URL; ?>",
        "description": "Your Digital Gateway to Kasur City",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo SITE_URL; ?>/pages/search.php?q={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body class="<?php echo $darkMode; ?>">

<!-- Top Bar -->
<div class="top-bar bg-dark text-light py-1 d-none d-md-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex gap-3">
                <small><i class="fas fa-phone-alt me-1"></i> <a href="tel:<?php echo WHATSAPP_DISPLAY; ?>" class="text-light text-decoration-none"><?php echo WHATSAPP_DISPLAY; ?></a></small>
                <small><i class="fas fa-envelope me-1"></i> <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-light text-decoration-none"><?php echo SITE_EMAIL; ?></a></small>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="?lang=<?php echo altLang(); ?>" class="btn btn-sm btn-outline-light btn-lang"><?php echo langLabel(); ?></a>
                <button id="darkModeToggle" class="btn btn-sm btn-outline-light" title="<?php echo t('dark_mode'); ?>">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="d-flex gap-2">
                    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="text-light"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" class="text-light"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank" class="text-light"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo TIKTOK_URL; ?>" target="_blank" class="text-light"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?php echo $home; ?>index.php">
            <img src="<?php echo $home; ?>assets/images/logo.webp" alt="Digital Kasur" height="45" class="me-2" onerror="this.style.display='none'">
            <span class="brand-text">Digital <span class="text-primary">Kasur</span></span>
        </a>

        <div class="d-flex d-lg-none align-items-center gap-2">
            <button id="darkModeToggleMobile" class="btn btn-sm btn-outline-dark" title="<?php echo t('dark_mode'); ?>">
                <i class="fas fa-moon"></i>
            </button>
            <a href="?lang=<?php echo altLang(); ?>" class="btn btn-sm btn-outline-primary btn-lang"><?php echo langLabel(); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'home' ? 'active' : ''; ?>" href="<?php echo $home; ?>index.php">
                        <i class="fas fa-home d-lg-none me-2"></i><?php echo t('nav_home'); ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php echo in_array($activePage, ['events','digital-services','business-directory','jobs','news','blog','city-guide']) ? 'active' : ''; ?>" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-compass d-lg-none me-2"></i>Explore
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/events.php"><i class="fas fa-calendar-alt me-2"></i><?php echo t('nav_events'); ?></a></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/digital-services.php"><i class="fas fa-laptop-code me-2"></i><?php echo t('nav_services'); ?></a></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/business-directory.php"><i class="fas fa-store me-2"></i><?php echo t('nav_directory'); ?></a></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/jobs.php"><i class="fas fa-briefcase me-2"></i><?php echo t('nav_jobs'); ?></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/news.php"><i class="fas fa-newspaper me-2"></i><?php echo t('nav_news'); ?></a></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/blog.php"><i class="fas fa-blog me-2"></i><?php echo t('nav_blog'); ?></a></li>
                        <li><a class="dropdown-item" href="<?php echo $home; ?>pages/city-guide.php"><i class="fas fa-map-marked-alt me-2"></i><?php echo t('nav_city'); ?></a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'about' ? 'active' : ''; ?>" href="<?php echo $home; ?>pages/about.php">
                        <i class="fas fa-info-circle d-lg-none me-2"></i><?php echo t('nav_about'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'contact' ? 'active' : ''; ?>" href="<?php echo $home; ?>pages/contact.php">
                        <i class="fas fa-envelope d-lg-none me-2"></i><?php echo t('nav_contact'); ?>
                    </a>
                </li>
            </ul>

            <div class="d-flex gap-2">
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <a href="<?php echo $home; ?>admin/dashboard.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-tachometer-alt me-1"></i><?php echo t('nav_dashboard'); ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $home; ?>pages/logout.php" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i><?php echo t('nav_logout'); ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $home; ?>pages/login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i><?php echo t('nav_login'); ?>
                    </a>
                    <a href="<?php echo $home; ?>pages/register.php" class="btn btn-outline-primary btn-sm d-none d-md-inline-block">
                        <i class="fas fa-user-plus me-1"></i><?php echo t('nav_register'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="container mt-3">
    <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show" role="alert">
        <?php echo clean($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php endif; ?>
