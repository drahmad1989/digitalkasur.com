<?php
$pageTitle = 'Sitemap';
$pageDescription = 'Digital Kasur - Complete sitemap of all pages';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="page-header">
    <div class="container">
        <h1><i class="fas fa-sitemap me-2"></i>Sitemap</h1>
        <div class="breadcrumb-custom">
            <a href="<?php echo $home; ?>index.php">Home</a>
            <span class="separator">/</span>
            <span>Sitemap</span>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white"><h6 class="mb-0"><i class="fas fa-home me-2"></i>Main Pages</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $home; ?>index.php"><i class="fas fa-angle-right me-2"></i>Home</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/about.php"><i class="fas fa-angle-right me-2"></i>About Us</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/contact.php"><i class="fas fa-angle-right me-2"></i>Contact Us</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/privacy.php"><i class="fas fa-angle-right me-2"></i>Privacy Policy</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/terms.php"><i class="fas fa-angle-right me-2"></i>Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white"><h6 class="mb-0"><i class="fas fa-compass me-2"></i>Explore</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/events.php"><i class="fas fa-angle-right me-2"></i>Events</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/digital-services.php"><i class="fas fa-angle-right me-2"></i>Digital Services</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/business-directory.php"><i class="fas fa-angle-right me-2"></i>Business Directory</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/jobs.php"><i class="fas fa-angle-right me-2"></i>Jobs</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/news.php"><i class="fas fa-angle-right me-2"></i>News</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/blog.php"><i class="fas fa-angle-right me-2"></i>Blog</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/city-guide.php"><i class="fas fa-angle-right me-2"></i>City Guide</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header bg-warning text-dark"><h6 class="mb-0"><i class="fas fa-user me-2"></i>Account</h6></div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/login.php"><i class="fas fa-angle-right me-2"></i>Login</a></li>
                            <li class="mb-2"><a href="<?php echo $home; ?>pages/register.php"><i class="fas fa-angle-right me-2"></i>Register</a></li>
                        </ul>
                        <hr>
                        <h6 class="mt-3">Contact</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank"><i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp</a></li>
                            <li class="mb-2"><a href="mailto:<?php echo SITE_EMAIL; ?>"><i class="fas fa-envelope me-2 text-primary"></i>Email</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
