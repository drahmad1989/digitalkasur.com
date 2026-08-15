<?php
/**
 * DigitalKasur.com - Homepage
 * Main landing page with hero, services, cities, stats
 */

require_once __DIR__ . '/config.php';

$page_title = SITE_TITLE;
$page_description = SITE_DESCRIPTION;
$page_keywords = SITE_KEYWORDS;

require_once __DIR__ . '/header.php';

// Get data
$stats = get_site_stats();
$cities = get_all_cities();
$featured_events = get_featured_events(6);
$featured_businesses = get_featured_businesses(8);
$latest_news = get_latest_news(3);
$event_categories = get_categories_by_type('event');
$digital_categories = get_categories_by_type('digital');
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-bg">
        <img src="<?php echo $assets_path; ?>images/hero-bg.jpg" alt="Kasur District"
             onerror="this.style.display='none';">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge"><i class="fas fa-star me-1"></i> #1 Platform in Kasur District</span>
        <h1 class="hero-title">
            <?php _e('hero_title'); ?>
        </h1>
        <p class="hero-subtitle">
            <?php _e('hero_subtitle'); ?>
        </p>
        <div class="hero-buttons">
            <a href="pages/events.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-calendar-alt"></i> <?php _e('hero_btn_events'); ?>
            </a>
            <a href="pages/digital-services.php" class="btn btn-outline-white btn-lg">
                <i class="fas fa-laptop-code"></i> <?php _e('hero_btn_digital'); ?>
            </a>
        </div>

        <!-- Hero Stats -->
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-number" data-count="500">0</span>
                <span class="hero-stat-label"><?php _e('stat_events'); ?></span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-number" data-count="200">0</span>
                <span class="hero-stat-label"><?php _e('stat_businesses'); ?></span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-number" data-count="50">0</span>
                <span class="hero-stat-label"><?php _e('stat_projects'); ?></span>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-number">5<i class="fas fa-star" style="font-size:0.7em;color:var(--secondary-color)"></i></span>
                <span class="hero-stat-label"><?php _e('stat_rating'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Cities We Serve -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><?php _e('nav_cities'); ?></span>
            <h2 class="section-title"><?php _e('cities_title'); ?></h2>
            <p class="section-subtitle"><?php _e('cities_subtitle'); ?></p>
        </div>
        <div class="cities-grid">
            <a href="pages/cities/kasur.php" class="city-card">
                <span class="city-card-icon">🏛️</span>
                <h3 class="city-card-name">Kasur</h3>
                <span class="city-card-count">District HQ</span>
            </a>
            <a href="pages/cities/pattoki.php" class="city-card">
                <span class="city-card-icon">🏪</span>
                <h3 class="city-card-name">Pattoki</h3>
                <span class="city-card-count">City of Flowers</span>
            </a>
            <a href="pages/cities/phool-nagar.php" class="city-card">
                <span class="city-card-icon">🌸</span>
                <h3 class="city-card-name">Phool Nagar</h3>
                <span class="city-card-count">Flower Market</span>
            </a>
            <a href="pages/cities/kot-radha-kishan.php" class="city-card">
                <span class="city-card-icon">🏠</span>
                <h3 class="city-card-name">Kot Radha Kishan</h3>
                <span class="city-card-count">Tehsil Town</span>
            </a>
            <a href="pages/cities/chunian.php" class="city-card">
                <span class="city-card-icon">🌾</span>
                <h3 class="city-card-name">Chunian</h3>
                <span class="city-card-count">Agricultural Hub</span>
            </a>
            <a href="pages/cities/theng-more.php" class="city-card">
                <span class="city-card-icon">🏗️</span>
                <h3 class="city-card-name">Theng More</h3>
                <span class="city-card-count">Growing Area</span>
            </a>
        </div>
    </div>
</section>

<!-- Event Management Services -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fas fa-calendar-alt me-1"></i> Event Services</span>
            <h2 class="section-title"><?php _e('events_title'); ?></h2>
            <p class="section-subtitle"><?php _e('events_subtitle'); ?></p>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-ring"></i></div>
                <h3 class="service-title"><?php _e('events_wedding'); ?></h3>
                <p class="service-desc"><?php _e('events_wedding_desc'); ?></p>
                <a href="pages/events.php?type=wedding" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-birthday-cake"></i></div>
                <h3 class="service-title"><?php _e('events_birthday'); ?></h3>
                <p class="service-desc"><?php _e('events_birthday_desc'); ?></p>
                <a href="pages/events.php?type=birthday" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                <h3 class="service-title"><?php _e('events_corporate'); ?></h3>
                <p class="service-desc"><?php _e('events_corporate_desc'); ?></p>
                <a href="pages/events.php?type=corporate" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3 class="service-title"><?php _e('events_seminar'); ?></h3>
                <p class="service-desc">Professional seminars and workshops for learning and development</p>
                <a href="pages/events.php?type=seminar" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-campground"></i></div>
                <h3 class="service-title"><?php _e('events_festival'); ?></h3>
                <p class="service-desc"><?php _e('events_festival_desc'); ?></p>
                <a href="pages/events.php?type=festival" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-music"></i></div>
                <h3 class="service-title"><?php _e('events_concert'); ?></h3>
                <p class="service-desc">Live concerts, musical nights, and entertainment shows</p>
                <a href="pages/events.php?type=concert" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-award"></i></div>
                <h3 class="service-title"><?php _e('events_ceremony'); ?></h3>
                <p class="service-desc">Award ceremonies, graduations, and formal events</p>
                <a href="pages/events.php?type=ceremony" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-flag"></i></div>
                <h3 class="service-title"><?php _e('events_rally'); ?></h3>
                <p class="service-desc">Public rallies, processions, and community gatherings</p>
                <a href="pages/events.php?type=rally" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="pages/events.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th-list me-2"></i><?php _e('events_view_all'); ?>
            </a>
        </div>
    </div>
</section>

<!-- Digital Services -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fas fa-laptop-code me-1"></i> Digital Solutions</span>
            <h2 class="section-title"><?php _e('digital_title'); ?></h2>
            <p class="section-subtitle"><?php _e('digital_subtitle'); ?></p>
        </div>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-code"></i></div>
                <h3 class="service-title"><?php _e('digital_web'); ?></h3>
                <p class="service-desc"><?php _e('digital_web_desc'); ?></p>
                <a href="pages/digital-services.php?type=website" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-shopping-cart"></i></div>
                <h3 class="service-title"><?php _e('digital_ecommerce'); ?></h3>
                <p class="service-desc"><?php _e('digital_ecommerce_desc'); ?></p>
                <a href="pages/digital-services.php?type=ecommerce" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
                <h3 class="service-title"><?php _e('digital_mobile'); ?></h3>
                <p class="service-desc">Android and iOS mobile application development</p>
                <a href="pages/digital-services.php?type=mobile" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-paint-brush"></i></div>
                <h3 class="service-title"><?php _e('digital_design'); ?></h3>
                <p class="service-desc"><?php _e('digital_design_desc'); ?></p>
                <a href="pages/digital-services.php?type=design" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-search"></i></div>
                <h3 class="service-title"><?php _e('digital_seo'); ?></h3>
                <p class="service-desc"><?php _e('digital_seo_desc'); ?></p>
                <a href="pages/digital-services.php?type=seo" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-share-alt"></i></div>
                <h3 class="service-title"><?php _e('digital_social'); ?></h3>
                <p class="service-desc">Social media marketing campaigns and digital advertising</p>
                <a href="pages/digital-services.php?type=social" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-th-list"></i></div>
                <h3 class="service-title"><?php _e('digital_page'); ?></h3>
                <p class="service-desc">Social media page setup, management, and growth</p>
                <a href="pages/digital-services.php?type=page" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-pen-fancy"></i></div>
                <h3 class="service-title"><?php _e('digital_content'); ?></h3>
                <p class="service-desc">Professional content writing, copywriting, and blogging</p>
                <a href="pages/digital-services.php?type=content" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="service-card">
                <div class="service-icon"><i class="fas fa-video"></i></div>
                <h3 class="service-title"><?php _e('digital_video'); ?></h3>
                <p class="service-desc">Professional video editing, motion graphics, and animation</p>
                <a href="pages/digital-services.php?type=video" class="service-cta"><?php _e('learn_more'); ?> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="pages/digital-services.php" class="btn btn-primary btn-lg">
                <i class="fas fa-th-list me-2"></i><?php _e('digital_view_all'); ?>
            </a>
        </div>
    </div>
</section>

<!-- Featured Businesses -->
<?php if (!empty($featured_businesses)): ?>
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fas fa-store me-1"></i> Local Businesses</span>
            <h2 class="section-title"><?php _e('biz_title'); ?></h2>
            <p class="section-subtitle"><?php _e('biz_subtitle'); ?></p>
        </div>
        <div class="business-grid">
            <?php foreach ($featured_businesses as $biz): ?>
            <div class="business-card">
                <div class="business-avatar">
                    <?php if (!empty($biz['logo']) && file_exists(UPLOAD_PATH . $biz['logo'])): ?>
                        <img src="<?php echo $assets_path; ?>../<?php echo $biz['logo']; ?>" alt="<?php echo htmlspecialchars($biz['name']); ?>">
                    <?php else: ?>
                        <?php echo strtoupper(substr($biz['name'], 0, 1)); ?>
                    <?php endif; ?>
                </div>
                <div class="business-info">
                    <h3 class="business-name">
                        <?php if ($biz['is_verified']): ?>
                            <i class="fas fa-check-circle text-primary" style="font-size:0.85em" title="Verified"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($biz['name']); ?>
                    </h3>
                    <div class="business-category">
                        <span class="category-badge"><?php echo htmlspecialchars($biz['category_name'] ?? 'Business'); ?></span>
                        <?php if (!empty($biz['city_name'])): ?>
                            <span class="ms-1"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($biz['city_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="star-rating">
                        <?php echo render_stars($biz['rating'], $biz['review_count']); ?>
                    </div>
                    <p class="business-desc" style="font-size:0.85rem;color:var(--text-muted);margin-top:0.5rem;">
                        <?php echo truncate_text(strip_tags($biz['description'] ?? ''), 80); ?>
                    </p>
                    <div class="business-actions">
                        <?php if (!empty($biz['phone'])): ?>
                            <a href="tel:<?php echo $biz['phone']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-phone"></i> Call</a>
                        <?php endif; ?>
                        <a href="pages/business-directory.php" class="btn btn-sm btn-primary"><i class="fas fa-info-circle"></i> Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="pages/business-directory.php" class="btn btn-primary btn-lg"><i class="fas fa-store me-2"></i><?php _e('biz_browse'); ?></a>
            <a href="pages/business-register.php" class="btn btn-outline-primary btn-lg ms-2"><i class="fas fa-plus-circle me-2"></i><?php _e('biz_add'); ?></a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest News -->
<?php if (!empty($latest_news)): ?>
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge"><i class="fas fa-newspaper me-1"></i> Latest Updates</span>
            <h2 class="section-title"><?php _e('news_title'); ?></h2>
            <p class="section-subtitle"><?php _e('news_subtitle'); ?></p>
        </div>
        <div class="row g-4">
            <?php foreach ($latest_news as $item): ?>
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
                        <h3 class="card-title" style="font-size:1.1rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p class="card-text"><?php echo truncate_text(strip_tags($item['summary'] ?? $item['content'] ?? ''), 100); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-clock me-1"></i><?php echo time_ago($item['created_at']); ?></small>
                            <small class="text-muted"><i class="fas fa-eye me-1"></i><?php echo number_format($item['views']); ?> <?php _e('views'); ?></small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="pages/news.php" class="service-cta"><?php _e('news_read_more'); ?> <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="pages/news.php" class="btn btn-primary btn-lg"><i class="fas fa-newspaper me-2"></i>View All News</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Why Choose DigitalKasur -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">Why Us</span>
            <h2 class="section-title"><?php _e('why_title'); ?></h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100">
                    <div class="service-icon" style="background:rgba(var(--primary-rgb),0.1);"><i class="fas fa-map-marked-alt"></i></div>
                    <h3 class="service-title"><?php _e('why_local'); ?></h3>
                    <p class="service-desc"><?php _e('why_local_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100">
                    <div class="service-icon" style="background:rgba(var(--secondary-rgb),0.15);color:var(--secondary-dark);"><i class="fas fa-bolt"></i></div>
                    <h3 class="service-title"><?php _e('why_fast'); ?></h3>
                    <p class="service-desc"><?php _e('why_fast_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100">
                    <div class="service-icon" style="background:rgba(16,185,129,0.1);color:var(--success-color);"><i class="fas fa-tags"></i></div>
                    <h3 class="service-title"><?php _e('why_affordable'); ?></h3>
                    <p class="service-desc"><?php _e('why_affordable_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Process -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-5">
            <span class="section-badge">How It Works</span>
            <h2 class="section-title"><?php _e('process_title'); ?></h2>
            <p class="section-subtitle"><?php _e('process_subtitle'); ?></p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-step">
                        <div class="process-number">1</div>
                        <div class="process-icon"><i class="fas fa-comments"></i></div>
                    </div>
                    <h4 class="mt-3"><?php _e('process_consultation'); ?></h4>
                    <p class="text-muted"><?php _e('process_consultation_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-step">
                        <div class="process-number">2</div>
                        <div class="process-icon"><i class="fas fa-clipboard-list"></i></div>
                    </div>
                    <h4 class="mt-3"><?php _e('process_planning'); ?></h4>
                    <p class="text-muted"><?php _e('process_planning_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <div class="process-icon"><i class="fas fa-cogs"></i></div>
                    </div>
                    <h4 class="mt-3"><?php _e('process_execution'); ?></h4>
                    <p class="text-muted"><?php _e('process_execution_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <div class="process-icon"><i class="fas fa-check-double"></i></div>
                    </div>
                    <h4 class="mt-3"><?php _e('process_delivery'); ?></h4>
                    <p class="text-muted"><?php _e('process_delivery_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="text-center">
            <h2 class="text-white mb-3" style="font-size:var(--font-size-3xl);"><?php _e('cta_ready'); ?></h2>
            <p class="text-white opacity-75 mb-4" style="font-size:var(--font-size-md);max-width:600px;margin:0 auto;"><?php _e('cta_contact'); ?></p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="pages/contact.php" class="btn btn-secondary btn-lg"><i class="fas fa-envelope me-2"></i><?php _e('cta_contact_btn'); ?></a>
                <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>?text=<?php echo urlencode(__('whatsapp_message')); ?>" class="btn btn-whatsapp btn-lg" target="_blank" rel="noopener"><i class="fab fa-whatsapp me-2"></i><?php _e('whatsapp_chat'); ?></a>
            </div>
        </div>
    </div>
</section>

<style>
.section-padding { padding: var(--spacer-4xl) 0; }
.bg-light { background: var(--bg-light); }
.bg-light[data-theme="dark"] { background: var(--gray-800); }
.process-step { position: relative; display: inline-block; }
.process-number {
    width: 32px; height: 32px; background: var(--primary-color); color: white;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: 0.9rem; margin: 0 auto 1rem;
}
.process-icon {
    width: 80px; height: 80px; border-radius: 50%; background: rgba(var(--primary-rgb),0.1);
    display: flex; align-items: center; justify-content: center; margin: 0 auto;
    font-size: 2rem; color: var(--primary-color);
}
.cta-section {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    padding: var(--spacer-4xl) 0;
}
</style>

<script>
// Counter Animation
document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('[data-count]');
    const animateCounter = (el) => {
        const target = parseInt(el.getAttribute('data-count'));
        const suffix = target >= 100 ? '+' : '+';
        let current = 0;
        const increment = target / 60;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current) + suffix;
        }, 25);
    };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
