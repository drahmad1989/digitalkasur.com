<?php
/**
 * DigitalKasur.com - Business Directory Page
 * Browse businesses across Kasur District
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Business Directory - ' . SITE_NAME;
$page_description = 'Find local businesses in Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan, and Theng More.';

require_once __DIR__ . '/../header.php';

$city_filter = isset($_GET['city']) ? clean_input($_GET['city']) : '';
$cat_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$featured_only = isset($_GET['featured']) ? true : false;
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $per_page;

$where = ["b.is_active = 1"];
$params = [];

if ($city_filter) {
    $city = get_city_by_slug($city_filter);
    if ($city) { $where[] = "b.city_id = ?"; $params[] = $city['id']; }
}
if ($cat_filter) {
    $cat = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND type = 'business'", [$cat_filter]);
    if ($cat) { $where[] = "b.category_id = ?"; $params[] = $cat['id']; }
}
if ($search_query) {
    $where[] = "(b.name LIKE ? OR b.description LIKE ? OR b.address LIKE ?)";
    $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%";
}
if ($featured_only) {
    $where[] = "b.is_featured = 1";
}

$where_clause = implode(' AND ', $where);
$total_businesses = DB::count("businesses b", $where_clause, $params);

$businesses = DB::select(
    "SELECT b.*, c.name as city_name, cat.name as category_name
     FROM businesses b
     LEFT JOIN cities c ON b.city_id = c.id
     LEFT JOIN categories cat ON b.category_id = cat.id
     WHERE {$where_clause}
     ORDER BY b.is_featured DESC, b.rating DESC, b.created_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

$cities = get_all_cities();
$biz_categories = get_categories_by_type('business');
$total_pages = ceil($total_businesses / $per_page);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-store me-1"></i> Local Businesses</span>
            <h1 class="page-title"><?php _e('biz_title'); ?></h1>
            <p class="page-subtitle"><?php _e('biz_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="filter-section">
    <div class="container">
        <form method="GET" action="" class="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> City</label>
                    <select name="city" class="form-select">
                        <option value=""><?php _e('all_cities'); ?></option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo $c['slug']; ?>" <?php echo $city_filter === $c['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i> Category</label>
                    <select name="category" class="form-select">
                        <option value=""><?php _e('all_categories'); ?></option>
                        <?php foreach ($biz_categories as $bc): ?>
                            <option value="<?php echo $bc['slug']; ?>" <?php echo $cat_filter === $bc['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($bc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Search</label>
                    <input type="text" name="q" class="form-control" placeholder="Search businesses..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="featured" value="1" class="form-check-input" id="featuredCheck" <?php echo $featured_only ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-semibold" for="featuredCheck"><i class="fas fa-star text-warning me-1"></i> Featured Only</label>
                    </div>
                </div>
                <div class="col-lg-3 col-md-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-filter me-1"></i> Filter</button>
                        <a href="business-register.php" class="btn btn-success"><i class="fas fa-plus me-1"></i> <?php _e('biz_add'); ?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Business Grid -->
<section class="section-padding">
    <div class="container">
        <p class="text-muted mb-4"><?php echo __('showing') . ' ' . count($businesses) . ' of ' . $total_businesses . ' businesses'; ?></p>

        <?php if (empty($businesses)): ?>
            <div class="text-center py-5">
                <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
                <h4><?php _e('no_results'); ?></h4>
                <a href="business-directory.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($businesses as $biz): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 business-card-vertical">
                        <div class="card-body">
                            <div class="d-flex gap-3 mb-3">
                                <div class="business-avatar flex-shrink-0">
                                    <?php if (!empty($biz['logo']) && file_exists(UPLOAD_PATH . $biz['logo'])): ?>
                                        <img src="<?php echo $assets_path; ?>../<?php echo $biz['logo']; ?>" alt="<?php echo htmlspecialchars($biz['name']); ?>">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($biz['name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="mb-1" style="font-size:1rem;">
                                        <?php if ($biz['is_verified']): ?><i class="fas fa-check-circle text-primary" title="Verified"></i> <?php endif; ?>
                                        <?php echo htmlspecialchars($biz['name']); ?>
                                    </h5>
                                    <span class="category-badge"><?php echo htmlspecialchars($biz['category_name'] ?? 'Business'); ?></span>
                                    <?php if (!empty($biz['city_name'])): ?>
                                        <small class="text-muted ms-1"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($biz['city_name']); ?></small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="mb-2"><?php echo render_stars($biz['rating'], $biz['review_count']); ?></div>
                            <p class="card-text" style="font-size:0.85rem;"><?php echo truncate_text(strip_tags($biz['description'] ?? ''), 120); ?></p>
                            <div class="business-actions">
                                <?php if (!empty($biz['phone'])): ?>
                                    <a href="tel:<?php echo $biz['phone']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-phone"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($biz['whatsapp'])): ?>
                                    <a href="https://wa.me/<?php echo $biz['whatsapp']; ?>" class="btn btn-sm btn-whatsapp" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($biz['website'])): ?>
                                    <a href="<?php echo htmlspecialchars($biz['website']); ?>" class="btn btn-sm btn-outline-secondary" target="_blank" rel="noopener"><i class="fas fa-globe"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
            <div class="mt-4"><?php echo render_pagination($current_page, $total_pages, 'business-directory.php'); ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3"><?php _e('biz_cta_title'); ?></h2>
        <p class="text-white opacity-75 mb-4"><?php _e('biz_cta'); ?></p>
        <a href="business-register.php" class="btn btn-secondary btn-lg"><i class="fas fa-plus-circle me-2"></i><?php _e('biz_register'); ?></a>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.filter-section { padding: 1.5rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section .form-select, .filter-section .form-control { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.section-padding { padding: var(--spacer-3xl) 0; }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
