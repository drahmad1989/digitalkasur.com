<?php
$pageTitle = 'About Us';
$pageDescription = 'Learn about Digital Kasur - your digital gateway to Kasur city.';
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-info-circle me-2"></i><?php echo t('nav_about'); ?></h1>
        <p>Know more about Digital Kasur</p>
        <div class="breadcrumb-custom">
            <a href="<?php echo $home; ?>index.php">Home</a>
            <span class="separator">/</span>
            <span>About</span>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="card-placeholder-img bg-gradient-1" style="height:300px;"><i class="fas fa-city" style="font-size:5rem;opacity:0.5;"></i></div>
            </div>
            <div class="col-lg-6">
                <h2>About <span class="text-gradient">Digital Kasur</span></h2>
                <p>Digital Kasur is your comprehensive online platform dedicated to the beautiful city of Kasur, Punjab, Pakistan. Our mission is to bridge the gap between Kasur's rich heritage and the digital world, making it easier for residents, visitors, and businesses to connect, discover, and grow together in this historic city.</p>
                <p>Founded with a vision to digitize Kasur's ecosystem, we provide a one-stop platform where you can find everything the city has to offer - from upcoming events and job opportunities to business directories and digital services. Whether you are a local resident looking for services, a business owner wanting to reach more customers, or a visitor exploring the city, Digital Kasur is designed to serve your needs.</p>
                <p>We believe that every city deserves a strong digital presence, and Kasur - with its thousands of years of history, its legendary Sufi heritage, and its vibrant community - certainly deserves the best. Our team works tirelessly to ensure that Kasur's voice is heard in the digital space, bringing the city's unique character and opportunities to screens across Pakistan and the world.</p>
            </div>
        </div>

        <!-- Mission, Vision, Values -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-1"><i class="fas fa-bullseye"></i></div>
                    <h5>Our Mission</h5>
                    <p>To create the most comprehensive digital platform for Kasur city that empowers businesses, connects communities, and promotes the city's rich cultural heritage to the world through innovative technology solutions.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-2"><i class="fas fa-eye"></i></div>
                    <h5>Our Vision</h5>
                    <p>To make Kasur a model digital city in Pakistan where every business has an online presence, every resident has access to digital services, and the city's heritage is preserved and celebrated in the digital age for future generations.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-3"><i class="fas fa-heart"></i></div>
                    <h5>Our Values</h5>
                    <p>Community first, innovation always, integrity in all dealings, and cultural respect. We are committed to serving Kasur with honesty, transparency, and a genuine passion for seeing our city thrive in the digital era.</p>
                </div>
            </div>
        </div>

        <!-- What We Offer -->
        <div class="text-center mb-4">
            <h2>What We <span class="text-gradient">Offer</span></h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-4"><i class="fas fa-calendar-alt"></i></div>
                    <h6>Events Coverage</h6>
                    <p>Complete coverage of all events happening in Kasur - from cultural festivals and religious gatherings to business seminars and educational workshops.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-5"><i class="fas fa-store"></i></div>
                    <h6>Business Directory</h6>
                    <p>A comprehensive listing of businesses in Kasur making it easy for residents and visitors to find trusted services, shops, restaurants, and professional service providers.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-1"><i class="fas fa-briefcase"></i></div>
                    <h6>Job Portal</h6>
                    <p>Connecting job seekers with employers in Kasur and surrounding areas. Find local opportunities, apply directly, and take the next step in your career journey.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="feature-card h-100">
                    <div class="feature-icon bg-gradient-6"><i class="fas fa-laptop-code"></i></div>
                    <h6>Digital Services</h6>
                    <p>Professional web development, graphic design, video editing, social media marketing, and other digital services tailored for Kasur's businesses and individuals.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 text-white" style="background:linear-gradient(135deg,#0d6efd,#6610f2);">
    <div class="container text-center">
        <h2 class="mb-3">Join the Digital Kasur Community</h2>
        <p class="lead mb-4">Have questions or want to collaborate? We would love to hear from you!</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?php echo $home; ?>pages/contact.php" class="btn btn-light btn-lg"><i class="fas fa-envelope me-2"></i>Contact Us</a>
            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="btn btn-outline-light btn-lg"><i class="fab fa-whatsapp me-2"></i>WhatsApp</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
