<?php
/**
 * DigitalKasur.com - Kasur City Page
 * District headquarters city page
 */

require_once __DIR__ . '/../../config.php';

$page_title = 'Kasur - DigitalKasur | Event Management & Digital Services';
$page_description = 'Explore events, businesses, jobs, and news in Kasur city - the district headquarters of Kasur District, Punjab, Pakistan.';

require_once __DIR__ . '/../../header.php';

$city = get_city_by_slug('kasur');
$events = get_events_by_city('kasur', 6);
$businesses = get_businesses_by_city('kasur', 6);
$news = get_news_by_city('kasur', 6);
$jobs = get_jobs_by_city('kasur', 6);
$stats = get_site_stats();
?>

<!-- Hero Section -->
<section class="city-hero">
    <div class="city-hero-overlay"></div>
    <div class="container">
        <div class="city-hero-content">
            <span class="city-hero-badge">🏛️ District Headquarters</span>
            <h1 class="city-hero-title">Kasur</h1>
            <p class="city-hero-desc">
                Kasur is the district headquarters and largest city of Kasur District in Punjab, Pakistan. Known for its rich cultural heritage, Sufi shrines, and as the birthplace of legendary singer Nusrat Fateh Ali Khan.
            </p>
            <div class="city-hero-stats">
                <div class="city-stat"><span>350,000+</span><small>Population</small></div>
                <div class="city-stat"><span><?php echo count($events); ?></span><small>Events</small></div>
                <div class="city-stat"><span><?php echo count($businesses); ?></span><small>Businesses</small></div>
            </div>
            <div class="d-flex gap-3 mt-3 flex-wrap">
                <a href="../events.php?city=kasur" class="btn btn-secondary"><i class="fas fa-calendar-alt me-1"></i>View Events</a>
                <a href="../../pages/contact.php?subject=<?php echo urlencode('Services in Kasur'); ?>" class="btn btn-primary"><i class="fas fa-envelope me-1"></i>Contact Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Our Services in Kasur</h2>
        </div>
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <a href="../events.php?city=kasur" class="service-card text-center text-decoration-none d-block">
                    <div class="service-icon"><i class="fas fa-calendar-alt"></i></div>
                    <h5>Event Management</h5>
                    <p class="service-desc">Weddings, parties, corporate events in Kasur</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="../digital-services.php" class="service-card text-center text-decoration-none d-block">
                    <div class="service-icon"><i class="fas fa-laptop-code"></i></div>
                    <h5>Digital Services</h5>
                    <p class="service-desc">Web development, SEO, social media in Kasur</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="../business-directory.php?city=kasur" class="service-card text-center text-decoration-none d-block">
                    <div class="service-icon"><i class="fas fa-store"></i></div>
                    <h5>Business Directory</h5>
                    <p class="service-desc">Find local businesses in Kasur</p>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <a href="../jobs.php?city=kasur" class="service-card text-center text-decoration-none d-block">
                    <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                    <h5>Jobs Portal</h5>
                    <p class="service-desc">Find employment opportunities in Kasur</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Events Section -->
<?php if (!empty($events)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Events in Kasur</h2>
            <a href="../events.php?city=kasur" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            <?php foreach ($events as $event): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-img-wrapper">
                        <img src="https://via.placeholder.com/400x250/1E40AF/FFFFFF?text=Event" alt="Event">
                        <?php if ($event['is_featured']): ?><span class="featured-badge"><i class="fas fa-star me-1"></i>Featured</span><?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title" style="font-size:1rem;"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <small class="text-muted d-block"><i class="fas fa-calendar me-1"></i><?php echo format_date($event['event_date']); ?></small>
                        <?php if (!empty($event['venue'])): ?><small class="text-muted d-block"><i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($event['venue']); ?></small><?php endif; ?>
                        <p class="card-text mt-2"><?php echo truncate_text(strip_tags($event['description'] ?? ''), 80); ?></p>
                    </div>
                    <div class="card-footer">
                        <span class="fw-bold <?php echo $event['price'] > 0 ? 'text-primary' : 'text-success'; ?>"><?php echo format_price($event['price']); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Businesses Section -->
<?php if (!empty($businesses)): ?>
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Businesses in Kasur</h2>
            <a href="../business-directory.php?city=kasur" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            <?php foreach ($businesses as $biz): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-2">
                            <div class="business-avatar flex-shrink-0" style="width:44px;height:44px;font-size:1rem;">
                                <?php echo strtoupper(substr($biz['name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($biz['name']); ?></h6>
                                <span class="category-badge" style="font-size:0.7rem;"><?php echo htmlspecialchars($biz['category_name'] ?? 'Business'); ?></span>
                            </div>
                        </div>
                        <div class="star-rating mb-2"><?php echo render_stars($biz['rating']); ?></div>
                        <p class="card-text" style="font-size:0.85rem;"><?php echo truncate_text(strip_tags($biz['description'] ?? ''), 80); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- News Section -->
<?php if (!empty($news)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">News from Kasur</h2>
            <a href="../news.php?city=kasur" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-4">
            <?php foreach ($news as $item): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-2">
                            <span class="category-badge"><?php echo htmlspecialchars($item['category_name'] ?? 'News'); ?></span>
                            <?php if ($item['is_breaking']): ?><span class="badge bg-danger">Breaking</span><?php endif; ?>
                        </div>
                        <h5 class="card-title" style="font-size:1rem;"><?php echo htmlspecialchars($item['title']); ?></h5>
                        <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo time_ago($item['created_at']); ?></small>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Jobs Section -->
<?php if (!empty($jobs)): ?>
<section class="section-padding">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title mb-0">Jobs in Kasur</h2>
            <a href="../jobs.php?city=kasur" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row g-3">
            <?php foreach ($jobs as $job): ?>
            <div class="col-lg-6">
                <div class="job-card">
                    <h5 style="font-size:1rem;"><?php echo htmlspecialchars($job['title']); ?></h5>
                    <small class="text-muted"><?php echo htmlspecialchars($job['company_name']); ?></small>
                    <div class="d-flex gap-2 mt-2">
                        <span class="category-badge"><?php echo ucfirst($job['job_type']); ?></span>
                        <?php if (!empty($job['salary'])): ?><span class="category-badge gold"><?php echo htmlspecialchars($job['salary']); ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3">Need Services in Kasur?</h2>
        <p class="text-white opacity-75 mb-4">Contact us for event management, digital services, or business listings in Kasur.</p>
        <a href="../../pages/contact.php?subject=<?php echo urlencode('Services Inquiry - Kasur'); ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-envelope me-2"></i>Contact Us
        </a>
    </div>
</section>

<style>
.city-hero { position: relative; padding: calc(var(--topbar-height) + var(--navbar-height) + 4rem) 0 4rem; background: linear-gradient(135deg, #1E40AF 0%, #1E3A8A 50%, #0F172A 100%); overflow: hidden; }
.city-hero-overlay { position: absolute; inset: 0; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.03)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L0,320Z"></path></svg>') no-repeat bottom; background-size: cover; }
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
