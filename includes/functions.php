<?php
/**
 * Common Functions - DigitalKasur.com
 * Enhanced with all helper functions
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

// ==================== INPUT HELPERS ====================

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generate_slug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

function format_date($date, $format = 'F j, Y') {
    if (empty($date) || $date == '0000-00-00') return '';
    return date($format, strtotime($date));
}

function time_ago($datetime) {
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'Just now';
}

function truncate_text($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

function format_price($price) {
    if ($price <= 0) return 'Free';
    return 'PKR ' . number_format($price);
}

// ==================== DATABASE HELPERS ====================

function get_all_cities() {
    return DB::select("SELECT * FROM cities WHERE is_active = 1 ORDER BY sort_order ASC, name ASC");
}

function get_city_by_slug($slug) {
    return DB::selectOne("SELECT * FROM cities WHERE slug = ? AND is_active = 1", [$slug]);
}

function get_categories_by_type($type) {
    return DB::select("SELECT * FROM categories WHERE type = ? AND is_active = 1 ORDER BY sort_order ASC, name ASC", [$type]);
}

function get_setting($key) {
    $result = DB::selectOne("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    return $result ? $result['setting_value'] : null;
}

function set_setting($key, $value) {
    $existing = DB::selectOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
    if ($existing) {
        return DB::update('settings', ['setting_value' => $value], 'setting_key = ?', [$key]);
    } else {
        return DB::insert('settings', ['setting_key' => $key, 'setting_value' => $value]);
    }
}

// ==================== EVENT HELPERS ====================

function get_featured_events($limit = 6) {
    return DB::select(
        "SELECT e.*, c.name as city_name, cat.name as category_name, cat.icon
         FROM events e
         LEFT JOIN cities c ON e.city_id = c.id
         LEFT JOIN categories cat ON e.category_id = cat.id
         WHERE e.is_featured = 1 AND e.is_active = 1 AND e.event_date >= CURDATE()
         ORDER BY e.event_date ASC
         LIMIT ?",
        [$limit]
    );
}

function get_events_by_city($city_slug, $limit = 10) {
    $city = get_city_by_slug($city_slug);
    if (!$city) return [];
    return DB::select(
        "SELECT e.*, cat.name as category_name, cat.icon
         FROM events e
         LEFT JOIN categories cat ON e.category_id = cat.id
         WHERE e.city_id = ? AND e.is_active = 1 AND e.event_date >= CURDATE()
         ORDER BY e.event_date ASC
         LIMIT ?",
        [$city['id'], $limit]
    );
}

function get_upcoming_events($limit = 6) {
    return DB::select(
        "SELECT e.*, c.name as city_name, cat.name as category_name, cat.icon
         FROM events e
         LEFT JOIN cities c ON e.city_id = c.id
         LEFT JOIN categories cat ON e.category_id = cat.id
         WHERE e.is_active = 1 AND e.event_date >= CURDATE()
         ORDER BY e.event_date ASC
         LIMIT ?",
        [$limit]
    );
}

// ==================== BUSINESS HELPERS ====================

function get_featured_businesses($limit = 8) {
    return DB::select(
        "SELECT b.*, c.name as city_name, cat.name as category_name
         FROM businesses b
         LEFT JOIN cities c ON b.city_id = c.id
         LEFT JOIN categories cat ON b.category_id = cat.id
         WHERE b.is_featured = 1 AND b.is_active = 1
         ORDER BY b.rating DESC, b.created_at DESC
         LIMIT ?",
        [$limit]
    );
}

function get_businesses_by_city($city_slug, $limit = 10) {
    $city = get_city_by_slug($city_slug);
    if (!$city) return [];
    return DB::select(
        "SELECT b.*, cat.name as category_name
         FROM businesses b
         LEFT JOIN categories cat ON b.category_id = cat.id
         WHERE b.city_id = ? AND b.is_active = 1
         ORDER BY b.is_featured DESC, b.rating DESC, b.created_at DESC
         LIMIT ?",
        [$city['id'], $limit]
    );
}

// ==================== NEWS HELPERS ====================

function get_latest_news($limit = 6) {
    return DB::select(
        "SELECT n.*, c.name as city_name, cat.name as category_name, u.name as author_name
         FROM news n
         LEFT JOIN cities c ON n.city_id = c.id
         LEFT JOIN categories cat ON n.category_id = cat.id
         LEFT JOIN users u ON n.user_id = u.id
         WHERE n.is_active = 1
         ORDER BY n.is_breaking DESC, n.is_featured DESC, n.created_at DESC
         LIMIT ?",
        [$limit]
    );
}

function get_news_by_city($city_slug, $limit = 10) {
    $city = get_city_by_slug($city_slug);
    if (!$city) return [];
    return DB::select(
        "SELECT n.*, cat.name as category_name
         FROM news n
         LEFT JOIN categories cat ON n.category_id = cat.id
         WHERE n.city_id = ? AND n.is_active = 1
         ORDER BY n.is_breaking DESC, n.created_at DESC
         LIMIT ?",
        [$city['id'], $limit]
    );
}

function get_breaking_news($limit = 5) {
    return DB::select(
        "SELECT * FROM news WHERE is_breaking = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?",
        [$limit]
    );
}

// ==================== JOB HELPERS ====================

function get_latest_jobs($limit = 6) {
    return DB::select(
        "SELECT j.*, c.name as city_name
         FROM jobs j
         LEFT JOIN cities c ON j.city_id = c.id
         WHERE j.is_active = 1 AND j.deadline >= CURDATE()
         ORDER BY j.is_featured DESC, j.created_at DESC
         LIMIT ?",
        [$limit]
    );
}

function get_jobs_by_city($city_slug, $limit = 10) {
    $city = get_city_by_slug($city_slug);
    if (!$city) return [];
    return DB::select(
        "SELECT j.*
         FROM jobs j
         WHERE j.city_id = ? AND j.is_active = 1 AND j.deadline >= CURDATE()
         ORDER BY j.is_featured DESC, j.created_at DESC
         LIMIT ?",
        [$city['id'], $limit]
    );
}

// ==================== BLOG HELPERS ====================

function get_latest_blog_posts($limit = 6) {
    return DB::select(
        "SELECT b.*, u.name as author_name
         FROM blog b
         LEFT JOIN users u ON b.author_id = u.id
         WHERE b.is_published = 1
         ORDER BY b.published_at DESC
         LIMIT ?",
        [$limit]
    );
}

// ==================== STATS HELPERS ====================

function get_site_stats() {
    return [
        'events' => DB::count("events", "is_active = 1"),
        'businesses' => DB::count("businesses", "is_active = 1"),
        'jobs' => DB::count("jobs", "is_active = 1 AND deadline >= CURDATE()"),
        'news' => DB::count("news", "is_active = 1"),
        'users' => DB::count("users", "is_active = 1"),
        'cities' => DB::count("cities", "is_active = 1"),
    ];
}

// ==================== FILE UPLOAD ====================

function upload_image($file, $destination = 'uploads/') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File too large. Maximum size is 5MB'];
    }

    if (!in_array($file['type'], ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and WebP allowed'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = UPLOAD_PATH . $filename;

    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => SITE_URL . '/uploads/' . $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file'];
    }
}

// ==================== PAGINATION ====================

function paginate($total_items, $items_per_page = ITEMS_PER_PAGE) {
    $total_pages = ceil($total_items / $items_per_page);
    $current_page = isset($_GET['page']) ? max(1, min($total_pages, (int)$_GET['page'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;

    return [
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'items_per_page' => $items_per_page,
        'offset' => $offset,
        'total_items' => $total_items
    ];
}

function render_pagination($current_page, $total_pages, $base_url) {
    if ($total_pages <= 1) return '';

    // Preserve existing query parameters
    $query_params = $_GET;
    unset($query_params['page']);
    $query_string = http_build_query($query_params);
    $separator = $query_string ? '&' : '?';
    $base = $base_url . ($query_string ? '?' . $query_string : '');

    $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';

    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base . $separator . 'page=' . ($current_page - 1) . '"><i class="fas fa-chevron-left"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>';
    }

    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);

    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base . $separator . 'page=1">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $current_page) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="' . $base . $separator . 'page=' . $i . '">' . $i . '</a></li>';
        }
    }

    if ($end < $total_pages) {
        if ($end < $total_pages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $base . $separator . 'page=' . $total_pages . '">' . $total_pages . '</a></li>';
    }

    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $base . $separator . 'page=' . ($current_page + 1) . '"><i class="fas fa-chevron-right"></i></a></li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>';
    }

    $html .= '</ul></nav>';
    return $html;
}

// ==================== FLASH MESSAGES ====================

function set_flash_message($type, $message) {
    $_SESSION['flash_message'] = ['type' => $type, 'message' => $message];
}

function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

function display_flash_message() {
    $flash = get_flash_message();
    if ($flash) {
        $icon = $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle');
        $alert_class = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo '<div class="alert alert-' . $alert_class . ' alert-dismissible fade show" role="alert">';
        echo '<i class="fas fa-' . $icon . ' me-2"></i>' . htmlspecialchars($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

// ==================== EMAIL ====================

function send_email($to, $subject, $message, $headers = '') {
    $headers .= 'From: ' . ADMIN_EMAIL . "\r\n";
    $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'Reply-To: ' . ADMIN_EMAIL . "\r\n";
    return mail($to, $subject, $message, $headers);
}

// ==================== REDIRECT ====================

function redirect($url) {
    header("Location: $url");
    exit();
}

// ==================== SEO HELPERS ====================

function generate_meta_tags($title = '', $description = '', $keywords = '', $image = '') {
    $title = $title ?: SITE_TITLE;
    $description = $description ?: SITE_DESCRIPTION;
    $keywords = $keywords ?: SITE_KEYWORDS;
    $image = $image ?: SITE_URL . '/assets/images/logo.jpg';

    return '
    <meta name="description" content="' . htmlspecialchars($description) . '">
    <meta name="keywords" content="' . htmlspecialchars($keywords) . '">
    <meta name="author" content="DigitalKasur">
    <meta name="robots" content="index, follow">

    <!-- Open Graph -->
    <meta property="og:title" content="' . htmlspecialchars($title) . '">
    <meta property="og:description" content="' . htmlspecialchars($description) . '">
    <meta property="og:image" content="' . htmlspecialchars($image) . '">
    <meta property="og:url" content="' . SITE_URL . $_SERVER['REQUEST_URI'] . '">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="DigitalKasur">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="' . htmlspecialchars($title) . '">
    <meta name="twitter:description" content="' . htmlspecialchars($description) . '">
    <meta name="twitter:image" content="' . htmlspecialchars($image) . '">

    <!-- Canonical URL -->
    <link rel="canonical" href="' . SITE_URL . $_SERVER['REQUEST_URI'] . '">
    ';
}

function generate_schema_markup($type, $data = []) {
    $schema = ['@context' => 'https://schema.org', '@type' => $type];

    switch ($type) {
        case 'LocalBusiness':
            $schema = array_merge($schema, [
                'name' => 'DigitalKasur',
                'description' => SITE_DESCRIPTION,
                'url' => SITE_URL,
                'telephone' => ADMIN_PHONE,
                'email' => ADMIN_EMAIL,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Kasur',
                    'addressRegion' => 'Punjab',
                    'addressCountry' => 'PK'
                ],
                'image' => SITE_URL . '/assets/images/logo.jpg',
                'priceRange' => '$$',
                'sameAs' => [SOCIAL_FACEBOOK, SOCIAL_INSTAGRAM, SOCIAL_YOUTUBE]
            ]);
            break;

        case 'Event':
            $schema = array_merge($schema, [
                'name' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'startDate' => $data['event_date'] ?? '',
                'location' => [
                    '@type' => 'Place',
                    'name' => $data['venue'] ?? '',
                    'address' => ['@type' => 'PostalAddress', 'addressLocality' => $data['city_name'] ?? 'Kasur', 'addressCountry' => 'PK']
                ],
                'organizer' => ['@type' => 'Organization', 'name' => 'DigitalKasur', 'url' => SITE_URL]
            ]);
            break;

        case 'JobPosting':
            $schema = array_merge($schema, [
                'title' => $data['title'] ?? '',
                'description' => $data['description'] ?? '',
                'hiringOrganization' => ['@type' => 'Organization', 'name' => $data['company_name'] ?? ''],
                'jobLocation' => ['@type' => 'Place', 'address' => ['@type' => 'PostalAddress', 'addressLocality' => $data['location'] ?? 'Kasur', 'addressCountry' => 'PK']],
                'employmentType' => $data['job_type'] ?? '',
                'validThrough' => $data['deadline'] ?? ''
            ]);
            break;
    }

    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
}

// ==================== ACTIVE PAGE HELPER ====================

function is_active($path, $base_path) {
    global $current_file, $current_query;
    $relative_path = str_replace($base_path, '', $path);
    $path_parts = explode('?', $relative_path);
    $file_name = $path_parts[0];
    $query_string = $path_parts[1] ?? '';

    if ($current_file != $file_name) return false;
    if (!empty($query_string)) return $current_query == $query_string;
    return empty($current_query);
}

// ==================== STAR RATING ====================

function render_stars($rating, $count = 0) {
    $html = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<i class="fas fa-star ' . ($i <= round($rating) ? 'text-warning' : 'text-muted') . '"></i>';
    }
    if ($count > 0) {
        $html .= '<small class="text-muted ms-1">(' . $count . ')</small>';
    }
    $html .= '</div>';
    return $html;
}
?>
