<?php
/**
 * DigitalKasur.com - Blog Page
 * Blog listing with sidebar layout
 * NOTE: No DOCTYPE/html/head/body - those come from header.php/footer.php
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Blog - ' . SITE_NAME;
$page_description = 'Read our blog for event tips, digital marketing strategies, technology news, and business insights for Kasur District.';

require_once __DIR__ . '/../header.php';

$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$cat_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 9;
$offset = ($current_page - 1) * $per_page;

$where = ["b.is_published = 1"];
$params = [];

if ($search_query) {
    $where[] = "(b.title LIKE ? OR b.excerpt LIKE ? OR b.content LIKE ?)";
    $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%";
}
if ($cat_filter) {
    $where[] = "b.category = ?";
    $params[] = $cat_filter;
}

$where_clause = implode(' AND ', $where);
$total_posts = DB::count("blog b", $where_clause, $params);

$posts = DB::select(
    "SELECT b.*, u.name as author_name
     FROM blog b
     LEFT JOIN users u ON b.author_id = u.id
     WHERE {$where_clause}
     ORDER BY b.published_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

$recent_posts = DB::select(
    "SELECT title, slug, created_at FROM blog WHERE is_published = 1 ORDER BY published_at DESC LIMIT 5", []
);

$blog_categories = ['event-tips' => 'Event Tips', 'digital-marketing' => 'Digital Marketing', 'technology' => 'Technology', 'business' => 'Business', 'local-news' => 'Local News'];

$total_pages = ceil($total_posts / $per_page);
?>

<!-- Page Header with Breadcrumb -->
<section class="page-header">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb" style="font-size:0.85rem;">
                <li class="breadcrumb-item"><a href="../index.php" style="color:rgba(255,255,255,0.7);">Home</a></li>
                <li class="breadcrumb-item active" style="color:white;"><?php _e('blog_title'); ?></li>
            </ol>
        </nav>
        <h1 class="page-title"><?php _e('blog_title'); ?></h1>
        <p class="page-subtitle">Insights, tips, and stories from Kasur District</p>
    </div>
</section>

<!-- Blog Content -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Search + Category Filter -->
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <form method="GET" action="" class="d-flex gap-2 flex-grow-1">
                        <input type="text" name="q" class="form-control" placeholder="Search blog..." value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </form>
                    <select onchange="window.location.href='blog.php?category='+this.value" class="form-select" style="max-width:200px;">
                        <option value="">All Categories</option>
                        <?php foreach ($blog_categories as $slug => $name): ?>
                            <option value="<?php echo $slug; ?>" <?php echo $cat_filter === $slug ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (empty($posts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                        <h4><?php _e('no_results'); ?></h4>
                        <a href="blog.php" class="btn btn-primary">View All Posts</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $post): ?>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-img-wrapper">
                                    <?php if (!empty($post['cover_image']) && file_exists(UPLOAD_PATH . $post['cover_image'])): ?>
                                        <img src="<?php echo $assets_path; ?>../<?php echo $post['cover_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://via.placeholder.com/400x250/1E40AF/FFFFFF?text=Blog" alt="Blog">
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <span class="category-badge mb-2"><?php echo $blog_categories[$post['category']] ?? ucfirst($post['category']); ?></span>
                                    <h3 class="card-title" style="font-size:1.05rem;"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <p class="card-text"><?php echo truncate_text(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 100); ?></p>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center" style="font-size:0.8rem;">
                                        <span class="text-muted"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></span>
                                        <span class="text-muted"><i class="fas fa-calendar me-1"></i><?php echo format_date($post['published_at'] ?? $post['created_at']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <div class="mt-4"><?php echo render_pagination($current_page, $total_pages, 'blog.php'); ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search Widget -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-search me-1"></i> Search</h5>
                        <form method="GET" action="" class="d-flex gap-2">
                            <input type="text" name="q" class="form-control" placeholder="Search articles...">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>

                <!-- Categories Widget -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-folder me-1"></i> Categories</h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($blog_categories as $slug => $name): ?>
                                <li class="mb-2">
                                    <a href="blog.php?category=<?php echo $slug; ?>" class="d-flex justify-content-between">
                                        <span><i class="fas fa-chevron-right text-primary me-1" style="font-size:0.7rem;"></i><?php echo $name; ?></span>
                                        <span class="badge bg-primary rounded-pill" style="font-size:0.7rem;">
                                            <?php echo DB::count("blog", "category = ? AND is_published = 1", [$slug]); ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Recent Posts Widget -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-clock me-1"></i> Recent Posts</h5>
                        <?php if (empty($recent_posts)): ?>
                            <p class="text-muted">No posts yet.</p>
                        <?php else: ?>
                            <?php foreach ($recent_posts as $rp): ?>
                                <div class="d-flex gap-2 mb-3 pb-3 border-bottom">
                                    <div>
                                        <a href="#" style="font-size:0.9rem;font-weight:500;"><?php echo htmlspecialchars($rp['title']); ?></a>
                                        <small class="d-block text-muted"><?php echo format_date($rp['created_at']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Newsletter Widget -->
                <div class="card" style="background:linear-gradient(135deg, var(--primary-color), var(--primary-dark));color:white;">
                    <div class="card-body">
                        <h5 class="text-white mb-2"><i class="fas fa-envelope me-1"></i> Newsletter</h5>
                        <p style="font-size:0.9rem;opacity:0.9;"><?php _e('blog_newsletter'); ?></p>
                        <form action="newsletter.php" method="POST">
                            <input type="email" name="email" class="form-control mb-2" placeholder="Your email address" required>
                            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-paper-plane me-1"></i><?php _e('blog_subscribe'); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Read Our Blog -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Why Read Our Blog?</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon"><i class="fas fa-lightbulb"></i></div>
                    <h5>Expert Insights</h5>
                    <p class="service-desc">Get expert advice on event planning, digital marketing, and business growth.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon" style="background:rgba(var(--secondary-rgb),0.15);color:var(--secondary-dark);"><i class="fas fa-map-marked-alt"></i></div>
                    <h5>Local Focus</h5>
                    <p class="service-desc">Content specifically tailored for businesses and residents of Kasur District.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon" style="background:rgba(16,185,129,0.1);color:var(--success-color);"><i class="fas fa-chart-line"></i></div>
                    <h5>Practical Tips</h5>
                    <p class="service-desc">Actionable tips you can implement immediately to grow your business.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3">Want to Write for Us?</h2>
        <p class="text-white opacity-75 mb-4">Share your expertise with the Kasur District community.</p>
        <a href="contact.php" class="btn btn-secondary btn-lg"><i class="fas fa-pen me-2"></i>Contact Us</a>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.bg-light { background: var(--bg-light); }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
