<?php
/**
 * DigitalKasur.com - Jobs Page
 * Browse and filter job listings across Kasur District
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Jobs Portal - ' . SITE_NAME;
$page_description = 'Find jobs in Kasur, Pattoki, Phool Nagar, Chunian, Kot Radha Kishan. Full time, part time, freelance opportunities.';

require_once __DIR__ . '/../header.php';

$city_filter = isset($_GET['city']) ? clean_input($_GET['city']) : '';
$type_filter = isset($_GET['type']) ? clean_input($_GET['type']) : '';
$search_query = isset($_GET['q']) ? clean_input($_GET['q']) : '';
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = ITEMS_PER_PAGE;
$offset = ($current_page - 1) * $per_page;

$where = ["j.is_active = 1", "j.deadline >= CURDATE()"];
$params = [];

if ($city_filter) {
    $city = get_city_by_slug($city_filter);
    if ($city) { $where[] = "j.city_id = ?"; $params[] = $city['id']; }
}
if ($type_filter) {
    $where[] = "j.job_type = ?";
    $params[] = $type_filter;
}
if ($search_query) {
    $where[] = "(j.title LIKE ? OR j.company_name LIKE ? OR j.skills LIKE ?)";
    $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%"; $params[] = "%{$search_query}%";
}

$where_clause = implode(' AND ', $where);
$total_jobs = DB::count("jobs j", $where_clause, $params);

$jobs = DB::select(
    "SELECT j.*, c.name as city_name, cat.name as category_name
     FROM jobs j
     LEFT JOIN cities c ON j.city_id = c.id
     LEFT JOIN categories cat ON j.category_id = cat.id
     WHERE {$where_clause}
     ORDER BY j.is_featured DESC, j.is_urgent DESC, j.created_at DESC
     LIMIT {$per_page} OFFSET {$offset}",
    $params
);

$cities = get_all_cities();
$total_pages = ceil($total_jobs / $per_page);

$job_type_labels = [
    'full-time' => ['label' => 'Full Time', 'class' => 'primary'],
    'part-time' => ['label' => 'Part Time', 'class' => 'info'],
    'contract' => ['label' => 'Contract', 'class' => 'warning'],
    'freelance' => ['label' => 'Freelance', 'class' => 'success'],
];
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-briefcase me-1"></i> Career Opportunities</span>
            <h1 class="page-title"><?php _e('jobs_title'); ?></h1>
            <p class="page-subtitle"><?php _e('jobs_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Filters -->
<section class="filter-section">
    <div class="container">
        <form method="GET" action="" class="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-map-marker-alt me-1"></i> City</label>
                    <select name="city" class="form-select">
                        <option value=""><?php _e('all_cities'); ?></option>
                        <?php foreach ($cities as $c): ?>
                            <option value="<?php echo $c['slug']; ?>" <?php echo $city_filter === $c['slug'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i> Job Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="full-time" <?php echo $type_filter === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                        <option value="part-time" <?php echo $type_filter === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                        <option value="contract" <?php echo $type_filter === 'contract' ? 'selected' : ''; ?>>Contract</option>
                        <option value="freelance" <?php echo $type_filter === 'freelance' ? 'selected' : ''; ?>>Freelance</option>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-search me-1"></i> Search</label>
                    <input type="text" name="q" class="form-control" placeholder="Job title, company, skills..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <div class="col-lg-2 col-md-6">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Jobs List -->
<section class="section-padding">
    <div class="container">
        <p class="text-muted mb-4"><?php echo __('showing') . ' ' . count($jobs) . ' of ' . $total_jobs . ' jobs'; ?></p>

        <?php if (empty($jobs)): ?>
            <div class="text-center py-5">
                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                <h4><?php _e('no_results'); ?></h4>
                <a href="jobs.php" class="btn btn-primary">Clear Filters</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $type_info = $job_type_labels[$job['job_type']] ?? ['label' => ucfirst($job['job_type']), 'class' => 'secondary'];
                    ?>
                    <div class="col-lg-6">
                        <div class="job-card">
                            <?php if ($job['is_featured']): ?>
                                <span class="featured-badge"><i class="fas fa-star me-1"></i><?php _e('featured'); ?></span>
                            <?php endif; ?>
                            <?php if ($job['is_urgent']): ?>
                                <span class="badge bg-danger" style="position:absolute;top:12px;right:12px;">Urgent</span>
                            <?php endif; ?>

                            <div class="d-flex gap-3 mb-3">
                                <div class="business-avatar flex-shrink-0" style="width:50px;height:50px;font-size:1.2rem;">
                                    <?php if (!empty($job['company_logo']) && file_exists(UPLOAD_PATH . $job['company_logo'])): ?>
                                        <img src="<?php echo $assets_path; ?>../<?php echo $job['company_logo']; ?>" alt="">
                                    <?php else: ?>
                                        <?php echo strtoupper(substr($job['company_name'], 0, 1)); ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 style="font-size:1.05rem;margin-bottom:0.2rem;"><?php echo htmlspecialchars($job['title']); ?></h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($job['company_name']); ?></small>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="category-badge <?php echo $type_info['class']; ?>"><?php echo $type_info['label']; ?></span>
                                <?php if (!empty($job['salary'])): ?>
                                    <span class="category-badge gold"><i class="fas fa-money-bill me-1"></i><?php echo htmlspecialchars($job['salary']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($job['city_name'])): ?>
                                    <span class="category-badge green"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($job['city_name']); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="row mb-3" style="font-size:0.85rem;">
                                <?php if (!empty($job['education_level'])): ?>
                                    <div class="col-6"><i class="fas fa-graduation-cap text-primary me-1"></i> <?php echo htmlspecialchars($job['education_level']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($job['experience_level'])): ?>
                                    <div class="col-6"><i class="fas fa-clock text-primary me-1"></i> <?php echo htmlspecialchars($job['experience_level']); ?></div>
                                <?php endif; ?>
                                <?php if (!empty($job['deadline'])): ?>
                                    <div class="col-6"><i class="fas fa-calendar text-danger me-1"></i> <?php _e('deadline'); ?>: <?php echo format_date($job['deadline']); ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($job['skills'])): ?>
                                <div class="mb-3">
                                    <?php foreach (explode(',', $job['skills']) as $skill): ?>
                                        <span class="badge bg-light text-dark border me-1" style="font-size:0.75rem;"><?php echo htmlspecialchars(trim($skill)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex gap-2">
                                <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('Job Application: ' . $job['title'] . ' at ' . $job['company_name']); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-paper-plane me-1"></i><?php _e('jobs_apply'); ?>
                                </a>
                                <button class="btn btn-sm btn-outline-secondary" onclick="bookmarkJob(<?php echo $job['id']; ?>)">
                                    <i class="far fa-bookmark me-1"></i>Save
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($total_pages > 1): ?>
            <div class="mt-4"><?php echo render_pagination($current_page, $total_pages, 'jobs.php'); ?></div>
        <?php endif; ?>
    </div>
</section>

<!-- Job Search Tips -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Job Search Tips</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon"><i class="fas fa-file-alt"></i></div>
                    <h5>Update Your CV</h5>
                    <p class="service-desc">Keep your CV updated with latest skills and experience.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon"><i class="fas fa-search"></i></div>
                    <h5>Search Regularly</h5>
                    <p class="service-desc">New jobs are posted daily. Check often for new opportunities.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon"><i class="fas fa-handshake"></i></div>
                    <h5>Network Locally</h5>
                    <p class="service-desc">Connect with local businesses and professionals in Kasur.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h5>Learn Skills</h5>
                    <p class="service-desc">Develop digital skills to increase your employability.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3"><?php _e('jobs_cta_title'); ?></h2>
        <p class="text-white opacity-75 mb-4"><?php _e('jobs_cta'); ?></p>
        <a href="post-job.php" class="btn btn-secondary btn-lg"><i class="fas fa-plus-circle me-2"></i><?php _e('jobs_post_free'); ?></a>
    </div>
</section>

<script>
function bookmarkJob(jobId) {
    alert('Job saved! (Feature coming soon)');
}
</script>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.filter-section { padding: 1.5rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section .form-select, .filter-section .form-control { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.section-padding { padding: var(--spacer-3xl) 0; }
.bg-light { background: var(--bg-light); }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
