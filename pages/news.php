<?php
/**
 * DigitalKasur.com - News Page
 * Browse news articles with breaking news ticker
 */

require_once __DIR__ . '/../config.php';

$page_title = 'News Portal - ' . SITE_NAME;
$page_description = 'Latest news from Kasur District including local, politics, sports, education, and business news.';

require_once __DIR__ . '/../header.php';

$city_filter = isset($_GET['city']) ? clean_input($_GET['city']) : '';
$cat_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $per_page;

$where = ["n.is_active = 1"];
$params = [];

if ($city_filter) {
    $city = get_city_by_slug($city_filter);
    if ($city) { $where[] = "n.city_id = ?"; $params[] = $city['id']; }
}
if ($cat_filter) {
    $cat = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND type = 'news'", [$cat_filter]);
    if ($cat) { $where[] = "n.category_id = ?"; $params[] = $cat['id']; }
}
if ($search_query) {
    $where[] = "(n.title LIKE ? OR n.summary LIKE ? OR n.content LIKE ?)";
    $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%";
}

$where_clause = implode(' AND ', $where);
$total_news = DB::count("news n", $where_clause, $params);

$news = DB::select(
    "SELECT n.*, c.name as city_name, cat.name as category_name, u.name as author_name
     FROM news n
     LEFT JOIN cities c ON n.city_id = c.id
     LEFT JOIN categories cat ON n.category_id = cat.id
     LEFT JOIN users u ON n.user_id = u.id
     WHERE {$where_clause}
     ORDER BY n.is_breaking DESC, n.is_featured DESC, n.created_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

$breaking = get_breaking_news(5);
$cities = get_all_cities();
$news_categories = get_categories_by_type('news');
$total_pages = ceil($total_news / $per_page);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-newspaper me-1"></i> Latest Updates</span>
            <h1 class="page-title"><?php _e('news_title'); ?></h1>
            <p class="page-subtitle"><?php _e('news_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Breaking News Ticker -->
<?php if (!empty($breaking)): ?>
<section class="breaking-ticker">
    <div class="container">
        <div class="d-flex align-items-center">
            <span class="breaking-label"><i class="fas fa-bolt me-1"></i> <?php _e('news_breaking'); ?></span>
            <div class="ticker-content">
                <div class="ticker-scroll">
                    <?php foreach ($breaking as $item): ?>
                        <span class="ticker-item">
                            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                            <small class="opacity-75 ms-2"><?php echo time_ago($item['created_at']); ?></small>
                        </span>
                        <span class="ticker-separator">|</span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Category Quick Links -->
<section class="category-links">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="news.php" class="btn btn-sm <?php echo empty($cat_filter) ? 'btn-primary' : 'btn-outline-primary'; ?>">All News</a>
            <?php foreach ($news_categories as $nc): ?>
                <a href="news.php?category=<?php echo $nc['slug']; ?>" class="btn btn-sm <?php echo $cat_filter === $nc['slug'] ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <i class="fas <?php echo $nc['icon'] ?? 'fa-tag'; ?> me-1"></i><?php echo htmlspecialchars($nc['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="filter-section">
    <div class="container">
        <form method="GET" action="" class="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> City</label>
                    <select name="city" class="form-select">
                        <option value=""><?php _e('all_cities'); ?></option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo $c['slug']; ?>" <?php echo $city_filter === $c['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i> Category</label>
                    <select name="category" class="form-select">
                        <option value=""><?php _e('all_categories'); ?></option>
                        <?php foreach ($news_categories as $nc): ?>
                            <option value="<?php echo $nc['slug']; ?>" <?php echo $cat_filter === $nc['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($nc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-4">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Search</label>
                    <input type="text" name="q" class="form-control" placeholder="Search news..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- News Grid -->
<section class="section-padding">
    <div class="container">
        <?php if (empty($news)): ?>
            <div class="text-center py-5">
                <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                <h4><?php _e('no_results'); ?></h4>
                <a href="news.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($news as $item): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-img-wrapper">
                            <?php if (!empty($item['cover_image']) && file_exists(UPLOAD_PATH . $item['cover_image'])): ?>
                                <img src="<?php echo $assets_path; ?>../<?php echo $item['cover_image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x250/1E40AF/FFFFFF?text=News" alt="News">
                            <?php endif; ?>
                            <?php if ($item['is_breaking']): ?>
                                <span class="featured-badge" style="background:var(--danger-color);color:white;"><i class="fas fa-bolt me-1"></i><?php _e('news_breaking'); ?></span>
                            <?php elseif ($item['is_featured']): ?>
                                <span class="featured-badge"><i class="fas fa-star me-1"></i><?php _e('featured'); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2 mb-2">
                                <span class="category-badge"><?php echo htmlspecialchars($item['category_name'] ?? 'News'); ?></span>
                                <?php if (!empty($item['city_name'])): ?>
                                    <span class="category-badge green"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($item['city_name']); ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title" style="font-size:1.05rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="card-text"><?php echo truncate_text(strip_tags($item['summary'] ?? $item['content'] ?? ''), 120); ?></p>
                            <div class="d-flex justify-content-between align-items-center" style="font-size:0.8rem;">
                                <span class="text-muted"><i class="fas fa-clock me-1"></i><?php echo time_ago($item['created_at']); ?></span>
                                <span class="text-muted"><i class="fas fa-eye me-1"></i><?php echo number_format($item['views']); ?> <?php _e('views'); ?></span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="#" class="service-cta"><?php _e('news_read_more'); ?> <i class="fas fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
            <div class="mt-4"><?php echo render_pagination($current_page, $total_pages, 'news.php'); ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3"><?php _e('news_cta_title'); ?></h2>
        <p class="text-white opacity-75 mb-4">Share news from your area and help keep the community informed.</p>
        <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('News Tip Submission'); ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-paper-plane me-2"></i><?php _e('news_submit'); ?>
        </a>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.breaking-ticker { background: var(--danger-color); color: white; padding: 0.5rem 0; overflow: hidden; }
.breaking-label { background: rgba(0,0,0,0.3); padding: 0.3rem 1rem; border-radius: var(--radius-sm); font-weight: 700; font-size: 0.8rem; white-space: nowrap; flex-shrink: 0; margin-right: 1rem; }
.ticker-content { overflow: hidden; flex: 1; }
.ticker-scroll { display: flex; align-items: center; gap: 1rem; animation: ticker 30s linear infinite; white-space: nowrap; }
.ticker-separator { opacity: 0.5; }
@keyframes ticker { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
.category-links { padding: 1rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section { padding: 1.5rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section .form-select, .filter-section .form-control { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.section-padding { padding: var(--spacer-3xl) 0; }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
