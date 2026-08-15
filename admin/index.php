<?php
/**
 * Admin Dashboard - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Dashboard';
$breadcrumb = [];

// Get stats
$events_count = DB::count("events");
$active_events = DB::count("events", "is_active = 1");
$businesses_count = DB::count("businesses");
$active_businesses = DB::count("businesses", "is_active = 1");
$jobs_count = DB::count("jobs");
$active_jobs = DB::count("jobs", "is_active = 1");
$news_count = DB::count("news");
$active_news = DB::count("news", "is_active = 1");
$users_count = DB::count("users");
$active_users = DB::count("users", "is_active = 1");
$messages_count = DB::count("messages");
$unread_messages = DB::count("messages", "is_read = 0");
$payments_count = DB::count("payments");
$blog_count = DB::count("blog");

// Recent activity (last 10 items across tables)
$recent_events = DB::select("SELECT 'event' as type, title, created_at FROM events ORDER BY created_at DESC LIMIT 3");
$recent_news = DB::select("SELECT 'news' as type, title, created_at FROM news ORDER BY created_at DESC LIMIT 3");
$recent_jobs = DB::select("SELECT 'job' as type, title, created_at FROM jobs ORDER BY created_at DESC LIMIT 2");
$recent_users = DB::select("SELECT 'user' as type, name as title, created_at FROM users ORDER BY created_at DESC LIMIT 2");

$recent_activity = array_merge($recent_events, $recent_news, $recent_jobs, $recent_users);
usort($recent_activity, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$recent_activity = array_slice($recent_activity, 0, 8);

// Monthly data for chart (last 6 months)
$chart_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('M', strtotime("-$i months"));
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));

    $chart_data[$month] = [
        'events' => DB::count("events", "created_at BETWEEN ? AND ?", [$month_start, $month_end]),
        'news' => DB::count("news", "created_at BETWEEN ? AND ?", [$month_start, $month_end]),
        'jobs' => DB::count("jobs", "created_at BETWEEN ? AND ?", [$month_start, $month_end]),
    ];
}

$max_chart = max(1, max(array_map(function($d) { return max($d['events'], $d['news'], $d['jobs']); }, $chart_data)));

include 'includes/header.php';
?>

<!-- Welcome -->
<div class="mb-4">
    <h4 style="font-weight:700;color:var(--text-primary);">
        Welcome back, <?php echo clean_input($_SESSION['user_name']); ?>! <span style="font-size:24px;">&#128075;</span>
    </h4>
    <p class="text-muted mb-0">Here's what's happening with your platform today.</p>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-calendar-alt"></i></div>
            <div class="stat-info">
                <h3><?php echo $events_count; ?></h3>
                <p>Events <small class="text-success">(<?php echo $active_events; ?> active)</small></p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-store"></i></div>
            <div class="stat-info">
                <h3><?php echo $businesses_count; ?></h3>
                <p>Businesses <small class="text-success">(<?php echo $active_businesses; ?> active)</small></p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-briefcase"></i></div>
            <div class="stat-info">
                <h3><?php echo $jobs_count; ?></h3>
                <p>Jobs <small class="text-success">(<?php echo $active_jobs; ?> active)</small></p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-newspaper"></i></div>
            <div class="stat-info">
                <h3><?php echo $news_count; ?></h3>
                <p>News <small class="text-success">(<?php echo $active_news; ?> active)</small></p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon purple"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3><?php echo $users_count; ?></h3>
                <p>Users <small class="text-success">(<?php echo $active_users; ?> active)</small></p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon cyan"><i class="fas fa-envelope"></i></div>
            <div class="stat-info">
                <h3><?php echo $messages_count; ?></h3>
                <p>Messages <small class="text-danger">(<?php echo $unread_messages; ?> unread)</small></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Quick Actions Row -->
<div class="row g-4 mb-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-chart-bar me-2 text-primary"></i>Content Overview (Last 6 Months)</span>
            </div>
            <div class="card-body">
                <div class="chart-bar-container">
                    <?php foreach ($chart_data as $month => $data): ?>
                        <div class="chart-bar-item">
                            <div class="d-flex gap-1 w-100 justify-content-center" style="height:100%;align-items:flex-end;">
                                <div class="chart-bar" style="height:<?php echo $max_chart > 0 ? ($data['events'] / $max_chart) * 100 : 0; ?>%;background:var(--accent-blue);max-width:16px;" title="Events: <?php echo $data['events']; ?>"></div>
                                <div class="chart-bar" style="height:<?php echo $max_chart > 0 ? ($data['news'] / $max_chart) * 100 : 0; ?>%;background:var(--accent-amber);max-width:16px;" title="News: <?php echo $data['news']; ?>"></div>
                                <div class="chart-bar" style="height:<?php echo $max_chart > 0 ? ($data['jobs'] / $max_chart) * 100 : 0; ?>%;background:var(--accent-green);max-width:16px;" title="Jobs: <?php echo $data['jobs']; ?>"></div>
                            </div>
                            <div class="chart-bar-label"><?php echo $month; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex justify-content-center gap-4 mt-3">
                    <span><i class="fas fa-square me-1" style="color:var(--accent-blue);font-size:10px;"></i> Events</span>
                    <span><i class="fas fa-square me-1" style="color:var(--accent-amber);font-size:10px;"></i> News</span>
                    <span><i class="fas fa-square me-1" style="color:var(--accent-green);font-size:10px;"></i> Jobs</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card" style="height:100%;">
            <div class="card-header">
                <span><i class="fas fa-bolt me-2 text-warning"></i>Quick Actions</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="events.php?action=add" class="quick-action">
                            <i class="fas fa-calendar-plus"></i>
                            <span>Add Event</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="businesses.php?action=add" class="quick-action">
                            <i class="fas fa-store-alt"></i>
                            <span>Add Business</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="jobs.php?action=add" class="quick-action">
                            <i class="fas fa-briefcase"></i>
                            <span>Add Job</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="news.php?action=add" class="quick-action">
                            <i class="fas fa-newspaper"></i>
                            <span>Add News</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="blog.php?action=add" class="quick-action">
                            <i class="fas fa-blog"></i>
                            <span>Add Blog Post</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="messages.php" class="quick-action">
                            <i class="fas fa-envelope"></i>
                            <span>View Messages</span>
                            <?php if ($unread_messages > 0): ?>
                                <span class="badge bg-danger" style="font-size:10px;"><?php echo $unread_messages; ?> new</span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row g-4">
    <!-- Recent Activity -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-clock me-2 text-primary"></i>Recent Activity</span>
                <a href="#" class="text-primary" style="font-size:13px;text-decoration:none;">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_activity)): ?>
                    <div class="empty-state py-4">
                        <p class="text-muted mb-0">No recent activity yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($recent_activity as $activity): ?>
                        <div class="activity-item" style="padding:12px 20px;">
                            <?php
                            $icon_map = ['event' => 'calendar-alt', 'news' => 'newspaper', 'job' => 'briefcase', 'user' => 'user'];
                            $color_map = ['event' => 'blue', 'news' => 'amber', 'job' => 'green', 'user' => 'purple'];
                            $icon = $icon_map[$activity['type']] ?? 'circle';
                            $color = $color_map[$activity['type']] ?? 'blue';
                            ?>
                            <div class="activity-icon stat-icon <?php echo $color; ?>" style="width:36px;height:36px;font-size:14px;">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                            </div>
                            <div>
                                <div class="activity-text">
                                    New <strong><?php echo ucfirst($activity['type']); ?></strong>: <?php echo clean_input($activity['title']); ?>
                                </div>
                                <div class="activity-time"><?php echo time_ago($activity['created_at']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Summary -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <span><i class="fas fa-info-circle me-2 text-info"></i>Platform Summary</span>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border-color);">
                    <span class="text-muted"><i class="fas fa-city me-2"></i>Cities</span>
                    <strong><?php echo DB::count("cities", "is_active = 1"); ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border-color);">
                    <span class="text-muted"><i class="fas fa-tags me-2"></i>Categories</span>
                    <strong><?php echo DB::count("categories", "is_active = 1"); ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border-color);">
                    <span class="text-muted"><i class="fas fa-blog me-2"></i>Blog Posts</span>
                    <strong><?php echo $blog_count; ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3" style="border-bottom:1px solid var(--border-color);">
                    <span class="text-muted"><i class="fas fa-credit-card me-2"></i>Payments</span>
                    <strong><?php echo $payments_count; ?></strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted"><i class="fas fa-star me-2"></i>Featured</span>
                    <strong>
                        <?php echo DB::count("events", "is_featured = 1") + DB::count("businesses", "is_featured = 1") + DB::count("jobs", "is_featured = 1") + DB::count("news", "is_featured = 1"); ?>
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
