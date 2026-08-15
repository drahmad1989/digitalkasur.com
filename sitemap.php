<?php
/**
 * Dynamic XML Sitemap - DigitalKasur.com
 *
 * Generates a sitemap.xml on-the-fly including all pages,
 * cities, events, businesses, jobs, news, and blog posts.
 * Output is proper XML with lastmod, changefreq, and priority.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// ============================================================
// SET XML CONTENT TYPE
// ============================================================

header('Content-Type: application/xml; charset=UTF-8');
header('X-Robots-Tag: ' . 'noindex, follow');

// Enable caching — regenerate every 6 hours
$cache_file = __DIR__ . '/cache/sitemap.xml';
$cache_time = 6 * 60 * 60; // 6 hours

if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    readfile($cache_file);
    exit();
}

// ============================================================
// START BUILDING SITEMAP
// ============================================================

$urls = [];
$now = date('Y-m-d');

// ============================================================
// STATIC PAGES
// ============================================================

$static_pages = [
    ['url' => SITE_URL . '/', 'changefreq' => 'daily', 'priority' => '1.0', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/events.php', 'changefreq' => 'daily', 'priority' => '0.9', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/digital-services.php', 'changefreq' => 'weekly', 'priority' => '0.9', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/business-directory.php', 'changefreq' => 'daily', 'priority' => '0.9', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/jobs.php', 'changefreq' => 'daily', 'priority' => '0.9', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/news.php', 'changefreq' => 'hourly', 'priority' => '0.9', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/blog.php', 'changefreq' => 'daily', 'priority' => '0.8', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/contact.php', 'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/about.php', 'changefreq' => 'monthly', 'priority' => '0.7', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/privacy.php', 'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/terms.php', 'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $now],
    ['url' => SITE_URL . '/privacy-policy.php', 'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $now],
    ['url' => SITE_URL . '/terms.php', 'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $now],
    ['url' => SITE_URL . '/data-deletion.php', 'changefreq' => 'yearly', 'priority' => '0.3', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/search.php', 'changefreq' => 'monthly', 'priority' => '0.5', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/login.php', 'changefreq' => 'yearly', 'priority' => '0.4', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/register.php', 'changefreq' => 'yearly', 'priority' => '0.4', 'lastmod' => $now],
    ['url' => SITE_URL . '/pages/business-register.php', 'changefreq' => 'monthly', 'priority' => '0.6', 'lastmod' => $now],
];

// Event type pages
$event_types = ['wedding', 'birthday', 'corporate', 'seminar', 'festival', 'concert', 'ceremony', 'rally'];
foreach ($event_types as $type) {
    $static_pages[] = [
        'url' => SITE_URL . '/pages/events.php?type=' . $type,
        'changefreq' => 'weekly',
        'priority' => '0.8',
        'lastmod' => $now
    ];
}

// Digital service type pages
$digital_types = ['website', 'ecommerce', 'mobile', 'design', 'seo', 'social', 'page', 'content', 'video'];
foreach ($digital_types as $type) {
    $static_pages[] = [
        'url' => SITE_URL . '/pages/digital-services.php?type=' . $type,
        'changefreq' => 'weekly',
        'priority' => '0.8',
        'lastmod' => $now
    ];
}

$urls = array_merge($urls, $static_pages);

// ============================================================
// CITIES
// ============================================================

try {
    $cities = DB::select("SELECT slug, name, updated_at FROM cities WHERE is_active = 1 ORDER BY sort_order ASC");

    foreach ($cities as $city) {
        $city_slug = htmlspecialchars($city['slug']);
        $lastmod = !empty($city['updated_at']) && $city['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($city['updated_at']))
            : $now;

        // City main page
        $urls[] = [
            'url' => SITE_URL . '/pages/cities/' . $city_slug . '.php',
            'changefreq' => 'weekly',
            'priority' => '0.8',
            'lastmod' => $lastmod
        ];

        // City-specific listings
        $urls[] = [
            'url' => SITE_URL . '/pages/events.php?city=' . $city_slug,
            'changefreq' => 'daily',
            'priority' => '0.7',
            'lastmod' => $lastmod
        ];
        $urls[] = [
            'url' => SITE_URL . '/pages/business-directory.php?city=' . $city_slug,
            'changefreq' => 'weekly',
            'priority' => '0.7',
            'lastmod' => $lastmod
        ];
        $urls[] = [
            'url' => SITE_URL . '/pages/jobs.php?city=' . $city_slug,
            'changefreq' => 'daily',
            'priority' => '0.7',
            'lastmod' => $lastmod
        ];
        $urls[] = [
            'url' => SITE_URL . '/pages/news.php?city=' . $city_slug,
            'changefreq' => 'daily',
            'priority' => '0.7',
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap Cities Error: " . $e->getMessage());
}

// ============================================================
// EVENTS
// ============================================================

try {
    $events = DB::select(
        "SELECT e.slug, e.updated_at, e.event_date, c.slug as city_slug
         FROM events e
         LEFT JOIN cities c ON e.city_id = c.id
         WHERE e.is_active = 1
         ORDER BY e.event_date DESC"
    );

    foreach ($events as $event) {
        $lastmod = !empty($event['updated_at']) && $event['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($event['updated_at']))
            : date('Y-m-d', strtotime($event['event_date']));

        $urls[] = [
            'url' => SITE_URL . '/pages/event-detail.php?slug=' . htmlspecialchars($event['slug']),
            'changefreq' => 'weekly',
            'priority' => '0.8',
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap Events Error: " . $e->getMessage());
}

// ============================================================
// BUSINESSES
// ============================================================

try {
    $businesses = DB::select(
        "SELECT slug, updated_at FROM businesses WHERE is_active = 1 ORDER BY updated_at DESC"
    );

    foreach ($businesses as $business) {
        $lastmod = !empty($business['updated_at']) && $business['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($business['updated_at']))
            : $now;

        $urls[] = [
            'url' => SITE_URL . '/pages/business-detail.php?slug=' . htmlspecialchars($business['slug']),
            'changefreq' => 'monthly',
            'priority' => '0.7',
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap Businesses Error: " . $e->getMessage());
}

// ============================================================
// JOBS
// ============================================================

try {
    $jobs = DB::select(
        "SELECT slug, updated_at, deadline FROM jobs WHERE is_active = 1 ORDER BY created_at DESC"
    );

    foreach ($jobs as $job) {
        $lastmod = !empty($job['updated_at']) && $job['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($job['updated_at']))
            : $now;

        // Lower priority for expired jobs
        $is_expired = !empty($job['deadline']) && strtotime($job['deadline']) < time();
        $priority = $is_expired ? '0.4' : '0.7';

        $urls[] = [
            'url' => SITE_URL . '/pages/job-detail.php?slug=' . htmlspecialchars($job['slug']),
            'changefreq' => $is_expired ? 'yearly' : 'weekly',
            'priority' => $priority,
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap Jobs Error: " . $e->getMessage());
}

// ============================================================
// NEWS
// ============================================================

try {
    $news = DB::select(
        "SELECT slug, updated_at, published_at FROM news WHERE is_active = 1 ORDER BY created_at DESC LIMIT 500"
    );

    foreach ($news as $item) {
        $lastmod = !empty($item['updated_at']) && $item['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($item['updated_at']))
            : (!empty($item['published_at']) ? date('Y-m-d', strtotime($item['published_at'])) : $now);

        $urls[] = [
            'url' => SITE_URL . '/pages/news-detail.php?slug=' . htmlspecialchars($item['slug']),
            'changefreq' => 'monthly',
            'priority' => '0.6',
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap News Error: " . $e->getMessage());
}

// ============================================================
// BLOG POSTS
// ============================================================

try {
    $posts = DB::select(
        "SELECT slug, updated_at, published_at FROM blog WHERE is_published = 1 ORDER BY published_at DESC LIMIT 500"
    );

    foreach ($posts as $post) {
        $lastmod = !empty($post['updated_at']) && $post['updated_at'] !== '0000-00-00 00:00:00'
            ? date('Y-m-d', strtotime($post['updated_at']))
            : (!empty($post['published_at']) ? date('Y-m-d', strtotime($post['published_at'])) : $now);

        $urls[] = [
            'url' => SITE_URL . '/pages/blog-detail.php?slug=' . htmlspecialchars($post['slug']),
            'changefreq' => 'monthly',
            'priority' => '0.6',
            'lastmod' => $lastmod
        ];
    }
} catch (Exception $e) {
    error_log("Sitemap Blog Error: " . $e->getMessage());
}

// ============================================================
// GENERATE XML OUTPUT
// ============================================================

$xml_output = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml_output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
$xml_output .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
$xml_output .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
$xml_output .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

foreach ($urls as $url_data) {
    $xml_output .= "  <url>\n";
    $xml_output .= "    <loc>" . htmlspecialchars($url_data['url'], ENT_XML1, 'UTF-8') . "</loc>\n";
    $xml_output .= "    <lastmod>" . $url_data['lastmod'] . "</lastmod>\n";
    $xml_output .= "    <changefreq>" . $url_data['changefreq'] . "</changefreq>\n";
    $xml_output .= "    <priority>" . $url_data['priority'] . "</priority>\n";
    $xml_output .= "  </url>\n";
}

$xml_output .= '</urlset>';

// ============================================================
// CACHE THE OUTPUT
// ============================================================

$cache_dir = __DIR__ . '/cache';
if (!is_dir($cache_dir)) {
    @mkdir($cache_dir, 0755, true);
}
@file_put_contents($cache_dir . '/sitemap.xml', $xml_output, LOCK_EX);

// ============================================================
// OUTPUT
// ============================================================

echo $xml_output;
exit();
?>
