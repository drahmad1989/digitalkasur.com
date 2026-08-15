<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$stats = [
    'events' => db()->count('events'),
    'services' => db()->count('digital_services'),
    'businesses' => db()->count('businesses'),
    'jobs' => db()->count('jobs', "status = 'active'"),
    'news' => db()->count('news'),
    'posts' => db()->count('blog_posts'),
    'users' => db()->count('users'),
    'messages' => db()->count('contact_messages', "status = 'unread'"),
];

$recentMessages = db()->fetchAll("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recentUsers = db()->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
?>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
<div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : $flash['type']; ?> alert-dismissible fade show">
    <?php echo clean($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #0d6efd;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['events']; ?></div>
                    <div class="stat-label">Events</div>
                </div>
                <div class="stat-icon" style="background:#0d6efd;"><i class="fas fa-calendar-alt"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #6610f2;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['services']; ?></div>
                    <div class="stat-label">Services</div>
                </div>
                <div class="stat-icon" style="background:#6610f2;"><i class="fas fa-laptop-code"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #198754;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['businesses']; ?></div>
                    <div class="stat-label">Businesses</div>
                </div>
                <div class="stat-icon" style="background:#198754;"><i class="fas fa-store"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #ffc107;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['jobs']; ?></div>
                    <div class="stat-label">Active Jobs</div>
                </div>
                <div class="stat-icon" style="background:#ffc107;"><i class="fas fa-briefcase"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #dc3545;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['news']; ?></div>
                    <div class="stat-label">News Articles</div>
                </div>
                <div class="stat-icon" style="background:#dc3545;"><i class="fas fa-newspaper"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #0dcaf0;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['posts']; ?></div>
                    <div class="stat-label">Blog Posts</div>
                </div>
                <div class="stat-icon" style="background:#0dcaf0;"><i class="fas fa-blog"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #6c757d;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['users']; ?></div>
                    <div class="stat-label">Users</div>
                </div>
                <div class="stat-icon" style="background:#6c757d;"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="stat-card" style="border-left-color: #e83e8c;">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-number"><?php echo $stats['messages']; ?></div>
                    <div class="stat-label">Unread Messages</div>
                </div>
                <div class="stat-icon" style="background:#e83e8c;"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Messages & Users -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h6 class="mb-0"><i class="fas fa-envelope text-primary me-2"></i>Recent Messages</h6>
                <a href="messages.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentMessages)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentMessages as $msg): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong><?php echo clean($msg['name']); ?></strong>
                            <small class="text-muted"><?php echo timeAgo($msg['created_at']); ?></small>
                        </div>
                        <p class="mb-0 small text-muted"><?php echo truncate($msg['message'], 80); ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No messages yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h6 class="mb-0"><i class="fas fa-users text-success me-2"></i>Recent Users</h6>
                <a href="users.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($recentUsers)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($recentUsers as $user): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo clean($user['name']); ?></strong>
                            <br><small class="text-muted"><?php echo clean($user['email']); ?></small>
                        </div>
                        <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'primary' : 'secondary'; ?>"><?php echo $user['role']; ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted p-3 mb-0">No users yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4 mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="events.php?action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Event</a>
                    <a href="services.php?action=add" class="btn btn-purple btn-sm" style="background:#6610f2;color:white;"><i class="fas fa-plus me-1"></i>Add Service</a>
                    <a href="businesses.php?action=add" class="btn btn-success btn-sm"><i class="fas fa-plus me-1"></i>Add Business</a>
                    <a href="jobs.php?action=add" class="btn btn-warning btn-sm"><i class="fas fa-plus me-1"></i>Add Job</a>
                    <a href="news.php?action=add" class="btn btn-danger btn-sm"><i class="fas fa-plus me-1"></i>Add News</a>
                    <a href="blog.php?action=add" class="btn btn-info btn-sm"><i class="fas fa-plus me-1"></i>Add Blog Post</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
