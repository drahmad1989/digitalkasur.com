<?php
/**
 * DigitalKasur.com - Phool Nagar City Page
 * Flower Market Town
 */

require_once __DIR__ . '/../../config.php';

$page_title = 'Phool Nagar - DigitalKasur | Event Management & Digital Services';
$page_description = 'Explore events, businesses, jobs, and news in Phool Nagar - the flower market town of Kasur District, Punjab, Pakistan.';

require_once __DIR__ . '/../../header.php';

$events = get_events_by_city('phool-nagar', 6);
$businesses = get_businesses_by_city('phool-nagar', 6);
$news = get_news_by_city('phool-nagar', 6);
$jobs = get_jobs_by_city('phool-nagar', 6);
?>

<!-- Hero Section -->
<section class="city-hero">
    <div class="city-hero-overlay"></div>
    <div class="container">
        <div class="city-hero-content">
            <span class="city-hero-badge">🌸 Flower Market Town</span>
            <h1 class="city-hero-title">Phool Nagar</h1>
            <p class="city-hero-desc">
                Phool Nagar (formerly Bhai Pheru) is a growing town in Kasur District known for its flower markets and agricultural trade. An important commercial center in the region with a vibrant community.
            </p>
            <div class="city-hero-stats">
                <div class="city-stat"><span>80,000+</span><small>Population</small></div>
                <div class="city-stat"><span><?php echo count($events); ?></span><small>Events</small></div>
                <div class="city-stat"><span><?php echo count($businesses); ?></span><small>Businesses</small></div>
            </div>
            <div class="d-flex gap-3 mt-3 flex-wrap">
                <a href="../events.php?city=phool-nagar" class="btn btn-secondary"><i class="fas fa-calendar-alt me-1"></i>View Events</a>
                <a href="../../pages/contact.php?subject=<?php echo urlencode('Services in Phool Nagar'); ?>" class="btn btn-primary"><i class="fas fa-envelope me-1"></i>Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-4"><h2 class="section-title">Our Services in Phool Nagar</h2></div>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6"><a href="../events.php?city=phool-nagar" class="service-card text-center text-decoration-none d-block"><div class="service-icon"><i class="fas fa-calendar-alt"></i></div><h5>Event Management</h5><p class="service-desc">Events and celebrations in Phool Nagar</p></a></div>
            <div class="col-lg-3 col-md-6"><a href="../digital-services.php" class="service-card text-center text-decoration-none d-block"><div class="service-icon"><i class="fas fa-laptop-code"></i></div><h5>Digital Services</h5><p class="service-desc">Web, SEO, marketing in Phool Nagar</p></a></div>
            <div class="col-lg-3 col-md-6"><a href="../business-directory.php?city=phool-nagar" class="service-card text-center text-decoration-none d-block"><div class="service-icon"><i class="fas fa-store"></i></div><h5>Business Directory</h5><p class="service-desc">Local businesses in Phool Nagar</p></a></div>
            <div class="col-lg-3 col-md-6"><a href="../jobs.php?city=phool-nagar" class="service-card text-center text-decoration-none d-block"><div class="service-icon"><i class="fas fa-briefcase"></i></div><h5>Jobs Portal</h5><p class="service-desc">Jobs in Phool Nagar</p></a></div>
        </div>
    </div>
</section>

<!-- Events -->
<?php if (!empty($events)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="section-title mb-0">Events in Phool Nagar</h2><a href="../events.php?city=phool-nagar" class="btn btn-outline-primary">View All</a></div>
        <div class="row g-4">
            <?php foreach ($events as $event): ?>
            <div class="col-lg-4 col-md-6"><div class="card h-100"><div class="card-body">
                <h5 style="font-size:1rem;"><?php echo htmlspecialchars($event['title']); ?></h5>
                <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i><?php echo format_date($event['event_date']); ?></small>
                <p class="card-text mt-2"><?php echo truncate_text(strip_tags($event['description'] ?? ''), 80); ?></p>
            </div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Businesses -->
<?php if (!empty($businesses)): ?>
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="section-title mb-0">Businesses in Phool Nagar</h2><a href="../business-directory.php?city=phool-nagar" class="btn btn-outline-primary">View All</a></div>
        <div class="row g-4">
            <?php foreach ($businesses as $biz): ?>
            <div class="col-lg-4 col-md-6"><div class="card h-100"><div class="card-body">
                <h6><?php echo htmlspecialchars($biz['name']); ?></h6>
                <span class="category-badge" style="font-size:0.7rem;"><?php echo htmlspecialchars($biz['category_name'] ?? 'Business'); ?></span>
                <div class="star-rating mt-1"><?php echo render_stars($biz['rating']); ?></div>
            </div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- News -->
<?php if (!empty($news)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="section-title mb-0">News from Phool Nagar</h2><a href="../news.php?city=phool-nagar" class="btn btn-outline-primary">View All</a></div>
        <div class="row g-4">
            <?php foreach ($news as $item): ?>
            <div class="col-lg-4 col-md-6"><div class="card h-100"><div class="card-body">
                <span class="category-badge mb-1"><?php echo htmlspecialchars($item['category_name'] ?? 'News'); ?></span>
                <h5 style="font-size:1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo time_ago($item['created_at']); ?></small>
            </div></div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Jobs -->
<?php if (!empty($jobs)): ?>
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="section-title mb-0">Jobs in Phool Nagar</h2><a href="../jobs.php?city=phool-nagar" class="btn btn-outline-primary">View All</a></div>
        <div class="row g-3">
            <?php foreach ($jobs as $job): ?>
            <div class="col-lg-6"><div class="job-card">
                <h5 style="font-size:1rem;"><?php echo htmlspecialchars($job['title']); ?></h5>
                <small class="text-muted"><?php echo htmlspecialchars($job['company_name']); ?></small>
                <div class="mt-2"><span class="category-badge"><?php echo ucfirst($job['job_type']); ?></span></div>
            </div></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3">Need Services in Phool Nagar?</h2>
        <p class="text-white opacity-75 mb-4">Contact us for event management, digital services, or business listings.</p>
        <a href="../../pages/contact.php?subject=<?php echo urlencode('Services Inquiry - Phool Nagar'); ?>" class="btn btn-secondary btn-lg"><i class="fas fa-envelope me-2"></i>Contact Us</a>
    </div>
</section>

<style>
.city-hero { position: relative; padding: calc(var(--topbar-height) + var(--navbar-height) + 4rem) 0 4rem; background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 50%, #0F172A 100%); overflow: hidden; }
.city-hero-overlay { position: absolute; inset: 0; }
.city-hero-content { position: relative; z-index: 1; color: white; text-align: center; max-width: 700px; margin: 0 auto; }
.city-hero-badge { display: inline-block; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); padding: 0.4rem 1.2rem; border-radius: 50px; font-size: 0.9rem; margin-bottom: 1rem; border: 1px solid rgba(255,255,255,0.2); }
.city-hero-title { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 800; color: white; margin-bottom: 1rem; }
.city-hero-desc { font-size: 1.05rem; color: rgba(255,255,255,0.85); line-height: 1.7; margin-bottom: 1.5rem; }
.city-hero-stats { display: flex; gap: 2rem; justify-content: center; flex-wrap: wrap; }
.city-stat { text-align: center; }
.city-stat span { display: block; font-size: 1.5rem; font-weight: 700; color: var(--secondary-color); }
.city-stat small { font-size: 0.8rem; color: rgba(255,255,255,0.7); text-transform: uppercase; letter-spacing: 1px; }
.section-padding { padding: var(--spacer-3xl) 0; }
.bg-light { background: var(--bg-light); }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../../footer.php'; ?>
