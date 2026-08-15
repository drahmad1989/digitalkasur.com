<?php
$pageTitle = 'City Guide';
$pageDescription = 'Complete guide to Kasur city - history, culture, food, places to visit, and more.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-map-marked-alt me-2"></i><?php echo t('city_title'); ?></h1>
        <p>Discover the beauty and heritage of Kasur</p>
        <div class="breadcrumb-custom">
            <a href="<?php echo $home; ?>index.php">Home</a>
            <span class="separator">/</span>
            <span>City Guide</span>
        </div>
    </div>
</section>

<!-- Quick Nav -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-2">
            <div class="col-6 col-md-3"><a href="#history" class="feature-card d-block text-decoration-none"><div class="feature-icon bg-gradient-1"><i class="fas fa-landmark"></i></div><h6><?php echo t('city_history'); ?></h6></a></div>
            <div class="col-6 col-md-3"><a href="#culture" class="feature-card d-block text-decoration-none"><div class="feature-icon bg-gradient-2"><i class="fas fa-theater-masks"></i></div><h6><?php echo t('city_culture'); ?></h6></a></div>
            <div class="col-6 col-md-3"><a href="#food" class="feature-card d-block text-decoration-none"><div class="feature-icon bg-gradient-4"><i class="fas fa-utensils"></i></div><h6><?php echo t('city_food'); ?></h6></a></div>
            <div class="col-6 col-md-3"><a href="#places" class="feature-card d-block text-decoration-none"><div class="feature-icon bg-gradient-3"><i class="fas fa-mosque"></i></div><h6><?php echo t('city_places'); ?></h6></a></div>
        </div>
    </div>
</section>

<!-- History -->
<section id="history" class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card-placeholder-img bg-gradient-1" style="height:300px;"><i class="fas fa-landmark" style="font-size:5rem;opacity:0.5;"></i></div>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-3"><i class="fas fa-landmark text-primary me-2"></i><?php echo t('city_history'); ?></h2>
                <p>Kasur is one of the oldest cities in Punjab, Pakistan, with a history that stretches back thousands of years. The city is named after Kasur, a son of Rama from the Hindu epic Ramayana. Over the centuries, Kasur has been a melting pot of cultures, influenced by Hindu, Muslim, and Sikh traditions. The city gained prominence during the Mughal era when it became an important administrative center, and many historic buildings and gardens from that period still stand today as testament to its glorious past.</p>
                <p>During the medieval period, Kasur was known as a center of learning and poetry. It was the birthplace of the legendary Sufi poet Bulleh Shah, whose shrine remains one of the most visited sites in the region. The city has also been home to numerous other scholars, poets, and saints who have contributed significantly to the cultural heritage of South Asia. The influence of these spiritual leaders can still be felt in the city's traditions and daily life.</p>
                <p>After the creation of Pakistan in 1947, Kasur underwent significant transformation as migrants from India settled here, bringing with them new skills, trades, and cultural practices. Today, Kasur is a thriving city that honors its rich heritage while embracing modern development. The city is known for its agricultural produce, particularly its famous Kasuri methi (fenugreek), and its leather industry which supplies products across Pakistan and beyond.</p>
            </div>
        </div>
    </div>
</section>

<!-- Culture -->
<section id="culture" class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                <div class="card-placeholder-img bg-gradient-2" style="height:300px;"><i class="fas fa-theater-masks" style="font-size:5rem;opacity:0.5;"></i></div>
            </div>
            <div class="col-lg-6">
                <h2 class="mb-3"><i class="fas fa-theater-masks text-primary me-2"></i><?php echo t('city_culture'); ?></h2>
                <p>The culture of Kasur is a vibrant tapestry woven from Sufi traditions, Punjabi folk heritage, and modern Pakistani life. The city is renowned for its Sufi shrines, particularly that of Bulleh Shah, which attracts devotees from all over the country. The annual Urs (death anniversary celebration) of Bulleh Shah is one of the largest cultural events in the region, featuring qawwali music, poetry recitations, and communal feasting that bring together people from all walks of life.</p>
                <p>Punjabi folk music and dance are integral parts of Kasur's cultural identity. The city has produced many notable musicians and singers who have made their mark on Pakistan's music industry. Traditional instruments like the dhol, algoza, and chimta are still commonly played at weddings, festivals, and other celebrations. The people of Kasur are known for their warm hospitality, love of good food, and strong sense of community.</p>
                <p>The city celebrates all major Pakistani festivals with great enthusiasm, including Eid ul-Fitr, Eid ul-Adha, Shab-e-Qadr, and Independence Day. Local fairs and festivals are also common, providing opportunities for people to come together, enjoy traditional entertainment, and celebrate their shared heritage. The local crafts, particularly leatherwork and embroidery, are also important cultural traditions that continue to thrive in the modern era.</p>
            </div>
        </div>
    </div>
</section>

<!-- Famous Food -->
<section id="food" class="py-5">
    <div class="container">
        <h2 class="mb-4 text-center"><i class="fas fa-utensils text-primary me-2"></i><?php echo t('city_food'); ?></h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-placeholder-img bg-gradient-4"><i class="fas fa-leaf"></i></div>
                    <div class="card-body">
                        <h6>Kasuri Methi</h6>
                        <p class="small">World-famous dried fenugreek leaves that add unique flavor to dishes across South Asia and beyond.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-placeholder-img bg-gradient-5"><i class="fas fa-drumstick-bite"></i></div>
                    <div class="card-body">
                        <h6>Kasuri Falooda</h6>
                        <p class="small">A refreshing sweet dessert drink made with vermicelli, rose syrup, milk, and ice cream, perfect for hot summers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-placeholder-img bg-gradient-2"><i class="fas fa-pepper-hot"></i></div>
                    <div class="card-body">
                        <h6>Kasuri Lassi</h6>
                        <p class="small">Traditional Punjabi yogurt drink, both sweet and salty varieties, served fresh at dhabas across the city.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-placeholder-img bg-gradient-1"><i class="fas fa-bread-slice"></i></div>
                    <div class="card-body">
                        <h6>Changa Manga Roti</h6>
                        <p class="small">Traditional thick bread served with desi ghee and lassi, a staple breakfast for Kasur locals.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Places to Visit -->
<section id="places" class="py-5 bg-light">
    <div class="container">
        <h2 class="mb-4 text-center"><i class="fas fa-map-marked-alt text-primary me-2"></i><?php echo t('city_places'); ?></h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-3"><i class="fas fa-mosque"></i></div>
                    <div class="card-body">
                        <h5>Shrine of Bulleh Shah</h5>
                        <p>The most famous landmark of Kasur, this beautiful shrine of the legendary Sufi poet attracts thousands of visitors annually. The annual Urs celebration is a must-visit cultural experience.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-4"><i class="fas fa-tree"></i></div>
                    <div class="card-body">
                        <h5>Changa Manga Forest</h5>
                        <p>One of the largest planted forests in the world, Changa Manga is a popular recreational spot with a wildlife park, railway, and picnic areas for families and nature lovers.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-6"><i class="fas fa-monument"></i></div>
                    <div class="card-body">
                        <h5>Kasur Museum</h5>
                        <p>Housing artifacts from the Mughal era and beyond, the museum showcases the rich historical heritage of the Kasur region including manuscripts, coins, and traditional crafts.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-1"><i class="fas fa-archway"></i></div>
                    <div class="card-body">
                        <h5>Kot Radha Kishen</h5>
                        <p>A historic town near Kasur known for its ancient temples and traditional craftsmanship, particularly its leather goods which have been famous for centuries.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-5"><i class="fas fa-water"></i></div>
                    <div class="card-body">
                        <h5>Sutlej River Bank</h5>
                        <p>The eastern border of Kasur is marked by the Sutlej River, offering scenic views and a peaceful escape from city life, especially during the monsoon season.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-placeholder-img bg-gradient-2"><i class="fas fa-place-of-worship"></i></div>
                    <div class="card-body">
                        <h5>Ganda Singh Border</h5>
                        <p>The Pakistan-India border crossing near Kasur, similar to Wagah, features a daily flag-lowering ceremony that is a patriotic and thrilling experience for visitors.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
