<?php
/**
 * DigitalKasur.com - Events Page
 * Browse and filter events across Kasur District
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Event Services - ' . SITE_NAME;
$page_description = 'Browse upcoming events, weddings, corporate events, festivals, and more in Kasur District.';

require_once __DIR__ . '/../header.php';

// Get filter parameters
$city_filter = isset($_GET['city']) ? clean_input($_GET['city']) : '';
$type_filter = isset($_GET['type']) ? clean_input($_GET['type']) : '';
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $per_page;

// Build query
$where = ["e.is_active = 1", "e.event_date >= CURDATE()"];
$params = [];

if ($city_filter) {
    $city = get_city_by_slug($city_filter);
    if ($city) {
        $where[] = "e.city_id = ?";
        $params[] = $city['id'];
    }
}

if ($type_filter) {
    $cat = DB::selectOne("SELECT id FROM categories WHERE slug = ? AND type = 'event'", [$type_filter]);
    if ($cat) {
        $where[] = "e.category_id = ?";
        $params[] = $cat['id'];
    }
}

if ($search_query) {
    $where[] = "(e.title LIKE ? OR e.venue LIKE ? OR e.description LIKE ?)";
    $params[] = "%{$search_query}%";
    $params[] = "%{$search_query}%";
    $params[] = "%{$search_query}%";
}

$where_clause = implode(' AND ', $where);

// Get total count
$total_events = DB::count("events e", $where_clause, $params);

// Get events
$events = DB::select(
    "SELECT e.*, c.name as city_name, cat.name as category_name, cat.icon, cat.slug as category_slug
     FROM events e
     LEFT JOIN cities c ON e.city_id = c.id
     LEFT JOIN categories cat ON e.category_id = cat.id
     WHERE {$where_clause}
     ORDER BY e.is_featured DESC, e.event_date ASC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

// Get cities and categories for filters
$cities = get_all_cities();
$event_categories = get_categories_by_type('event');

// Pagination
$total_pages = ceil($total_events / $per_page);
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-calendar-alt me-1"></i> Event Services</span>
            <h1 class="page-title"><?php _e('events_title'); ?></h1>
            <p class="page-subtitle"><?php _e('events_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section">
    <div class="container">
        <form method="GET" action="" class="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> <?php _e('all_cities'); ?></label>
                    <select name="city" class="form-select">
                        <option value=""><?php _e('all_cities'); ?></option>
                        <?php foreach ($cities as $city): ?>
                            <option value="<?php echo $city['slug']; ?>" <?php echo $city_filter === $city['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($city['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i> <?php _e('type'); ?></label>
                    <select name="type" class="form-select">
                        <option value=""><?php _e('all_types'); ?></option>
                        <?php foreach ($event_categories as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>" <?php echo $type_filter === $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> <?php _e('search'); ?></label>
                    <input type="text" name="q" class="form-control" placeholder="Search events..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> <?php _e('filter'); ?></button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Events Grid -->
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="mb-0 text-muted">
                <?php echo __('showing') . ' ' . count($events) . ' of ' . $total_events . ' events'; ?>
            </p>
        </div>

        <?php if (empty($events)): ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4><?php _e('no_results'); ?></h4>
                <p class="text-muted">Try adjusting your filters or search terms.</p>
                <a href="events.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100">
                        <div class="card-img-wrapper">
                            <?php if (!empty($event['cover_image']) && file_exists(UPLOAD_PATH . $event['cover_image'])): ?>
                                <img src="<?php echo $assets_path; ?>../<?php echo $event['cover_image']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x250/1E40AF/FFFFFF?text=Event" alt="Event">
                            <?php endif; ?>
                            <?php if ($event['is_featured']): ?>
                                <span class="featured-badge"><i class="fas fa-star me-1"></i><?php _e('featured'); ?></span>
                            <?php endif; ?>
                            <span class="category-badge" style="position:absolute;top:12px;right:12px;">
                                <?php echo htmlspecialchars($event['category_name'] ?? 'Event'); ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title" style="font-size:1.1rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="mb-2">
                                <?php if (!empty($event['venue'])): ?>
                                    <small class="d-block text-muted"><i class="fas fa-map-marker-alt me-1 text-primary"></i> <?php echo htmlspecialchars($event['venue']); ?></small>
                                <?php endif; ?>
                                <small class="d-block text-muted"><i class="fas fa-calendar me-1 text-primary"></i> <?php echo format_date($event['event_date']); ?></small>
                                <?php if (!empty($event['event_time'])): ?>
                                    <small class="d-block text-muted"><i class="fas fa-clock me-1 text-primary"></i> <?php echo date('g:i A', strtotime($event['event_time'])); ?></small>
                                <?php endif; ?>
                                <?php if (!empty($event['city_name'])): ?>
                                    <small class="d-block text-muted"><i class="fas fa-city me-1 text-primary"></i> <?php echo htmlspecialchars($event['city_name']); ?></small>
                                <?php endif; ?>
                            </div>
                            <p class="card-text"><?php echo truncate_text(strip_tags($event['description'] ?? ''), 100); ?></p>
                        </div>
                        <div class="card-footer">
                            <span class="fw-bold <?php echo $event['price'] > 0 ? 'text-primary' : 'text-success'; ?>">
                                <?php echo format_price($event['price']); ?>
                            </span>
                            <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('Booking: ' . $event['title']); ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-calendar-check me-1"></i>Book Now
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-4">
                <?php echo render_pagination($current_page, $total_pages, 'events.php'); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3"><?php _e('events_planning'); ?></h2>
        <p class="text-white opacity-75 mb-4"><?php _e('events_cta'); ?></p>
        <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('Event Planning Quote'); ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-paper-plane me-2"></i><?php _e('events_get_quote'); ?>
        </a>
    </div>
</section>

<style>
.page-header {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem;
    color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem);
}
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.filter-section { padding: 1.5rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section .form-select, .filter-section .form-control {
    border-radius: var(--radius-md); border-color: var(--border-color);
    padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color);
}
.section-padding { padding: var(--spacer-3xl) 0; }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
