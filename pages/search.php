<?php
/**
 * DigitalKasur.com - Search Page
 * Global search across events, businesses, jobs, and news
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Search - ' . SITE_NAME;
$page_description = 'Search for events, businesses, jobs, and news in Kasur District.';

require_once __DIR__ . '/../header.php';

$query = clean_input($_GET['q'] ?? '');
$results = ['events' => [], 'businesses' => [], 'jobs' => [], 'news' => []];
$counts = ['events' => 0, 'businesses' => 0, 'jobs' => 0, 'news' => 0];
$total = 0;

if (!empty($query)) {
    $search_term = "%{$query}%";

    $results['events'] = DB::select(
        "SELECT e.*, c.name as city_name, cat.name as category_name
         FROM events e LEFT JOIN cities c ON e.city_id = c.id LEFT JOIN categories cat ON e.category_id = cat.id
         WHERE e.is_active = 1 AND (e.title LIKE ? OR e.venue LIKE ? OR e.description LIKE ?)
         ORDER BY e.event_date ASC LIMIT 10",
        [$search_term, $search_term, $search_term]
    );
    $counts['events'] = count($results['events']);

    $results['businesses'] = DB::select(
        "SELECT b.*, c.name as city_name, cat.name as category_name
         FROM businesses b LEFT JOIN cities c ON b.city_id = c.id LEFT JOIN categories cat ON b.category_id = cat.id
         WHERE b.is_active = 1 AND (b.name LIKE ? OR b.description LIKE ? OR b.address LIKE ?)
         ORDER BY b.rating DESC LIMIT 10",
        [$search_term, $search_term, $search_term]
    );
    $counts['businesses'] = count($results['businesses']);

    $results['jobs'] = DB::select(
        "SELECT j.*, c.name as city_name
         FROM jobs j LEFT JOIN cities c ON j.city_id = c.id
         WHERE j.is_active = 1 AND (j.title LIKE ? OR j.company_name LIKE ? OR j.skills LIKE ?)
         ORDER BY j.created_at DESC LIMIT 10",
        [$search_term, $search_term, $search_term]
    );
    $counts['jobs'] = count($results['jobs']);

    $results['news'] = DB::select(
        "SELECT n.*, c.name as city_name, cat.name as category_name
         FROM news n LEFT JOIN cities c ON n.city_id = c.id LEFT JOIN categories cat ON n.category_id = cat.id
         WHERE n.is_active = 1 AND (n.title LIKE ? OR n.summary LIKE ? OR n.content LIKE ?)
         ORDER BY n.created_at DESC LIMIT 10",
        [$search_term, $search_term, $search_term]
    );
    $counts['news'] = count($results['news']);

    $total = array_sum($counts);
}
?>

<!-- Search Section -->
<section class="section-padding" style="margin-top:calc(var(--topbar-height) + var(--navbar-height));">
    <div class="container">
        <!-- Search Form -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <h1 class="section-title"><i class="fas fa-search me-2"></i>Search DigitalKasur</h1>
                    <p class="text-muted">Search across events, businesses, jobs, and news in Kasur District</p>
                </div>
                <form method="GET" action="" class="d-flex gap-2">
                    <input type="text" name="q" class="form-control form-control-lg" placeholder="What are you looking for?" value="<?php echo htmlspecialchars($query); ?>" autofocus>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <?php if (!empty($query)): ?>
            <div class="text-center mb-4">
                <p class="text-muted">
                    Found <strong><?php echo $total; ?></strong> results for "<strong><?php echo htmlspecialchars($query); ?></strong>"
                    (Events: <?php echo $counts['events']; ?> | Businesses: <?php echo $counts['businesses']; ?> | Jobs: <?php echo $counts['jobs']; ?> | News: <?php echo $counts['news']; ?>)
                </p>
            </div>

            <?php if ($total === 0): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>No results found</h4>
                    <p class="text-muted">Try different keywords or browse our categories.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="events.php" class="btn btn-outline-primary">Events</a>
                        <a href="business-directory.php" class="btn btn-outline-primary">Businesses</a>
                        <a href="jobs.php" class="btn btn-outline-primary">Jobs</a>
                        <a href="news.php" class="btn btn-outline-primary">News</a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Events Results -->
            <?php if ($counts['events'] > 0): ?>
                <div class="mb-5">
                    <h3 class="mb-3"><i class="fas fa-calendar-alt text-primary me-2"></i>Events (<?php echo $counts['events']; ?>)</h3>
                    <div class="row g-3">
                        <?php foreach ($results['events'] as $item): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <span class="category-badge mb-2"><?php echo htmlspecialchars($item['category_name'] ?? 'Event'); ?></span>
                                        <h5 class="card-title" style="font-size:1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i><?php echo format_date($item['event_date']); ?></small>
                                        <?php if (!empty($item['city_name'])): ?>
                                            <small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($item['city_name']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <a href="events.php" class="service-cta"><?php _e('view_details'); ?> <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Businesses Results -->
            <?php if ($counts['businesses'] > 0): ?>
                <div class="mb-5">
                    <h3 class="mb-3"><i class="fas fa-store text-success me-2"></i>Businesses (<?php echo $counts['businesses']; ?>)</h3>
                    <div class="row g-3">
                        <?php foreach ($results['businesses'] as $item): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title" style="font-size:1rem;"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <span class="category-badge mb-2"><?php echo htmlspecialchars($item['category_name'] ?? 'Business'); ?></span>
                                        <div class="mt-1"><?php echo render_stars($item['rating']); ?></div>
                                        <?php if (!empty($item['city_name'])): ?>
                                            <small class="text-muted d-block mt-1"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($item['city_name']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-footer">
                                        <a href="business-directory.php" class="service-cta"><?php _e('view_details'); ?> <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Jobs Results -->
            <?php if ($counts['jobs'] > 0): ?>
                <div class="mb-5">
                    <h3 class="mb-3"><i class="fas fa-briefcase text-warning me-2"></i>Jobs (<?php echo $counts['jobs']; ?>)</h3>
                    <div class="row g-3">
                        <?php foreach ($results['jobs'] as $item): ?>
                            <div class="col-lg-6">
                                <div class="job-card">
                                    <h5 style="font-size:1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($item['company_name']); ?></small>
                                    <div class="mt-2 d-flex gap-2">
                                        <span class="category-badge"><?php echo ucfirst($item['job_type']); ?></span>
                                        <?php if (!empty($item['city_name'])): ?>
                                            <span class="category-badge green"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($item['city_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="jobs.php" class="service-cta mt-2 d-inline-block"><?php _e('view_details'); ?> <i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- News Results -->
            <?php if ($counts['news'] > 0): ?>
                <div class="mb-5">
                    <h3 class="mb-3"><i class="fas fa-newspaper text-danger me-2"></i>News (<?php echo $counts['news']; ?>)</h3>
                    <div class="row g-3">
                        <?php foreach ($results['news'] as $item): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <span class="category-badge mb-2"><?php echo htmlspecialchars($item['category_name'] ?? 'News'); ?></span>
                                        <h5 class="card-title" style="font-size:1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo time_ago($item['created_at']); ?></small>
                                    </div>
                                    <div class="card-footer">
                                        <a href="news.php" class="service-cta"><?php _e('news_read_more'); ?> <i class="fas fa-arrow-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <!-- Quick Links when no search -->
            <div class="text-center mt-4">
                <h4 class="mb-3">Popular Searches</h4>
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <a href="search.php?q=wedding" class="btn btn-outline-primary">Wedding Events</a>
                    <a href="search.php?q=website" class="btn btn-outline-primary">Website Development</a>
                    <a href="search.php?q=restaurant" class="btn btn-outline-primary">Restaurants</a>
                    <a href="search.php?q=teacher" class="btn btn-outline-primary">Teaching Jobs</a>
                    <a href="search.php?q=SEO" class="btn btn-outline-primary">SEO Services</a>
                    <a href="search.php?q=hospital" class="btn btn-outline-primary">Hospitals</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
.section-padding { padding: var(--spacer-3xl) 0; }
.form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
