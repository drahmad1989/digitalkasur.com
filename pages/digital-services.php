<?php
/**
 * DigitalKasur.com - Digital Services Page
 * Browse digital services with city filter
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Digital Services - ' . SITE_NAME;
$page_description = 'Web development, SEO, social media marketing, graphic design, and more digital services in Kasur District.';

require_once __DIR__ . '/../header.php';

$city_filter = isset($_GET['city']) ? clean_input($_GET['city']) : '';
$type_filter = isset($_GET['type']) ? clean_input($_GET['type']) : '';

$cities = get_all_cities();
$digital_categories = get_categories_by_type('digital');

// Digital service details
$digital_services = [
    'website-development' => [
        'icon' => 'fa-code', 'title' => __('digital_web'), 'desc' => __('digital_web_desc'),
        'features' => ['Custom Website Design', 'Responsive & Mobile-Ready', 'CMS Integration (WordPress)', 'E-Commerce Ready', 'SSL & Security', 'Performance Optimization']
    ],
    'e-commerce' => [
        'icon' => 'fa-shopping-cart', 'title' => __('digital_ecommerce'), 'desc' => __('digital_ecommerce_desc'),
        'features' => ['Online Store Setup', 'Payment Gateway Integration (JazzCash/EasyPaisa)', 'Product Management', 'Order Tracking', 'Inventory System', 'Mobile-Friendly Design']
    ],
    'mobile-apps' => [
        'icon' => 'fa-mobile-alt', 'title' => __('digital_mobile'), 'desc' => 'Android and iOS mobile application development',
        'features' => ['Android App Development', 'iOS App Development', 'Cross-Platform Apps', 'UI/UX Design', 'API Integration', 'App Store Publishing']
    ],
    'graphic-designing' => [
        'icon' => 'fa-paint-brush', 'title' => __('digital_design'), 'desc' => __('digital_design_desc'),
        'features' => ['Logo Design', 'Business Cards & Stationery', 'Social Media Graphics', 'Brochures & Flyers', 'Banners & Posters', 'Brand Identity Kit']
    ],
    'seo-services' => [
        'icon' => 'fa-search', 'title' => __('digital_seo'), 'desc' => __('digital_seo_desc'),
        'features' => ['On-Page SEO', 'Off-Page SEO', 'Keyword Research', 'Google My Business', 'Local SEO for Kasur', 'Monthly Reporting']
    ],
    'social-media-marketing' => [
        'icon' => 'fa-share-alt', 'title' => __('digital_social'), 'desc' => 'Social media marketing campaigns and digital advertising',
        'features' => ['Facebook Marketing', 'Instagram Growth', 'YouTube Optimization', 'Paid Ad Campaigns', 'Influencer Outreach', 'Analytics & Reports']
    ],
    'page-management' => [
        'icon' => 'fa-th-list', 'title' => __('digital_page'), 'desc' => 'Social media page setup, management, and growth',
        'features' => ['Page Setup & Optimization', 'Content Calendar', 'Daily Posts & Stories', 'Community Management', 'Audience Growth', 'Performance Tracking']
    ],
    'content-writing' => [
        'icon' => 'fa-pen-fancy', 'title' => __('digital_content'), 'desc' => 'Professional content writing, copywriting, and blogging',
        'features' => ['Website Content', 'Blog Posts', 'Product Descriptions', 'Social Media Captions', 'Email Newsletters', 'SEO Content']
    ],
    'video-editing' => [
        'icon' => 'fa-video', 'title' => __('digital_video'), 'desc' => 'Professional video editing, motion graphics, and animation',
        'features' => ['Video Editing', 'Motion Graphics', 'YouTube Thumbnails', 'Intro/Outro Animations', 'Color Grading', 'Subtitles & Captions']
    ],
];

// Filter services if type is selected
if ($type_filter && isset($digital_services[$type_filter])) {
    $digital_services = [$type_filter => $digital_services[$type_filter]];
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-laptop-code me-1"></i> Digital Solutions</span>
            <h1 class="page-title"><?php _e('digital_title'); ?></h1>
            <p class="page-subtitle"><?php _e('digital_subtitle'); ?></p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="filter-section">
    <div class="container">
        <form method="GET" action="" class="filter-form">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4 col-md-6">
                    <label class="form-label fw-semibold"><i class="fas fa-tag me-1"></i> Service Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Services</option>
                        <?php foreach ($digital_categories as $cat): ?>
                            <option value="<?php echo $cat['slug']; ?>" <?php echo $type_filter === $cat['slug'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-4 col-md-6">
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
                <div class="col-lg-4 col-md-12">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> <?php _e('filter'); ?></button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Services Grid -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($digital_services as $slug => $service): ?>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100">
                    <div class="service-icon"><i class="fas <?php echo $service['icon']; ?>"></i></div>
                    <h3 class="service-title"><?php echo $service['title']; ?></h3>
                    <p class="service-desc"><?php echo $service['desc']; ?></p>
                    <ul class="service-features">
                        <?php foreach ($service['features'] as $feature): ?>
                            <li><i class="fas fa-check text-success me-2"></i><?php echo $feature; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('Quote: ' . $service['title']); ?>"
                       class="btn btn-primary w-100 mt-3">
                        <i class="fas fa-quote-left me-1"></i> <?php _e('request_quote'); ?>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Why Choose Our Digital Services?</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon" style="background:rgba(var(--primary-rgb),0.1);"><i class="fas fa-map-marked-alt"></i></div>
                    <h3 class="service-title"><?php _e('why_local'); ?></h3>
                    <p class="service-desc"><?php _e('why_local_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
                    <div class="service-icon" style="background:rgba(var(--secondary-rgb),0.15);color:var(--secondary-dark);"><i class="fas fa-bolt"></i></div>
                    <h3 class="service-title"><?php _e('why_fast'); ?></h3>
                    <p class="service-desc"><?php _e('why_fast_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="service-card h-100 text-center">
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
            <h2 class="section-title"><?php _e('process_title'); ?></h2>
            <p class="section-subtitle"><?php _e('process_subtitle'); ?></p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-number">1</div>
                    <div class="process-icon"><i class="fas fa-comments"></i></div>
                    <h4 class="mt-3"><?php _e('process_consultation'); ?></h4>
                    <p class="text-muted"><?php _e('process_consultation_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-number">2</div>
                    <div class="process-icon"><i class="fas fa-clipboard-list"></i></div>
                    <h4 class="mt-3"><?php _e('process_planning'); ?></h4>
                    <p class="text-muted"><?php _e('process_planning_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-number">3</div>
                    <div class="process-icon"><i class="fas fa-cogs"></i></div>
                    <h4 class="mt-3"><?php _e('process_execution'); ?></h4>
                    <p class="text-muted"><?php _e('process_execution_desc'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="text-center">
                    <div class="process-number">4</div>
                    <div class="process-icon"><i class="fas fa-check-double"></i></div>
                    <h4 class="mt-3"><?php _e('process_delivery'); ?></h4>
                    <p class="text-muted"><?php _e('process_delivery_desc'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container text-center">
        <h2 class="text-white mb-3"><?php _e('digital_cta_title'); ?></h2>
        <p class="text-white opacity-75 mb-4"><?php _e('digital_cta'); ?></p>
        <a href="<?php echo $base_path; ?>contact.php?subject=<?php echo urlencode('Digital Project Inquiry'); ?>" class="btn btn-secondary btn-lg">
            <i class="fas fa-rocket me-2"></i><?php _e('digital_start'); ?>
        </a>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.filter-section { padding: 1.5rem 0; background: var(--bg-card); border-bottom: 1px solid var(--border-color); }
.filter-section .form-select, .filter-section .form-control { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.section-padding { padding: var(--spacer-3xl) 0; }
.bg-light { background: var(--bg-light); }
.service-features { list-style: none; padding: 0; margin: 1rem 0 0; }
.service-features li { padding: 0.35rem 0; font-size: var(--font-size-sm); color: var(--text-color); }
.process-number { width: 32px; height: 32px; background: var(--primary-color); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; margin: 0 auto 1rem; }
.process-icon { width: 80px; height: 80px; border-radius: 50%; background: rgba(var(--primary-rgb),0.1); display: flex; align-items: center; justify-content: center; margin: 0 auto; font-size: 2rem; color: var(--primary-color); }
.cta-section { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: var(--spacer-3xl) 0; }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
