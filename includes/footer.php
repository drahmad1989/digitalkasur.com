<?php
/**
 * DigitalKasur.com - Footer Include
 */
$home = getHomePath();
$currentYear = date('Y');
?>

<!-- WhatsApp Floating Button -->
<a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" class="whatsapp-float" id="whatsappFloat" title="Chat on WhatsApp">
    <i class="fab fa-whatsapp"></i>
</a>

<!-- Chatbot Widget -->
<div class="chatbot-widget" id="chatbotWidget">
    <button class="chatbot-toggle" id="chatbotToggle" title="<?php echo t('chatbot_title'); ?>">
        <i class="fas fa-comment-dots"></i>
        <span class="chatbot-badge" id="chatbotBadge" style="display:none;">1</span>
    </button>
    <div class="chatbot-window" id="chatbotWindow" style="display:none;">
        <div class="chatbot-header">
            <div class="d-flex align-items-center">
                <div class="chatbot-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div>
                    <h6 class="mb-0"><?php echo t('chatbot_title'); ?></h6>
                    <small class="text-light opacity-75"><?php echo t('chatbot_welcome'); ?></small>
                </div>
            </div>
            <button class="chatbot-close" id="chatbotClose">&times;</button>
        </div>
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-msg bot-msg">
                <p><?php echo t('chatbot_welcome'); ?></p>
            </div>
            <div class="chatbot-quick-replies">
                <button class="quick-reply" data-query="events">📅 Events</button>
                <button class="quick-reply" data-query="jobs">💼 Jobs</button>
                <button class="quick-reply" data-query="services">💻 Services</button>
                <button class="quick-reply" data-query="contact">📞 Contact</button>
                <button class="quick-reply" data-query="business">🏪 Business</button>
                <button class="quick-reply" data-query="news">📰 News</button>
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbotInput" placeholder="<?php echo t('chatbot_placeholder'); ?>" autocomplete="off">
            <button id="chatbotSend"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="site-footer bg-dark text-light pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <!-- About -->
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo $home; ?>assets/images/logo.webp" alt="Digital Kasur" height="40" class="me-2" onerror="this.style.display='none'">
                    <h5 class="mb-0">Digital <span class="text-primary">Kasur</span></h5>
                </div>
                <p class="text-muted"><?php echo t('footer_desc'); ?></p>
                <div class="social-links d-flex gap-3">
                    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" class="text-light fs-5"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank" class="text-light fs-5"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank" class="text-light fs-5"><i class="fab fa-youtube"></i></a>
                    <a href="<?php echo TWITTER_URL; ?>" target="_blank" class="text-light fs-5"><i class="fab fa-twitter"></i></a>
                    <a href="<?php echo TIKTOK_URL; ?>" target="_blank" class="text-light fs-5"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-primary mb-3"><?php echo t('footer_quick_links'); ?></h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo $home; ?>index.php"><?php echo t('nav_home'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/events.php"><?php echo t('nav_events'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/digital-services.php"><?php echo t('nav_services'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/business-directory.php"><?php echo t('nav_directory'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/jobs.php"><?php echo t('nav_jobs'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/news.php"><?php echo t('nav_news'); ?></a></li>
                </ul>
            </div>

            <!-- More Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-primary mb-3">&nbsp;</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo $home; ?>pages/blog.php"><?php echo t('nav_blog'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/city-guide.php"><?php echo t('nav_city'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/about.php"><?php echo t('nav_about'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/contact.php"><?php echo t('nav_contact'); ?></a></li>
                    <li><a href="<?php echo $home; ?>pages/sitemap.php">Sitemap</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6">
                <h6 class="text-primary mb-3"><?php echo t('footer_contact'); ?></h6>
                <ul class="list-unstyled footer-contact">
                    <li><i class="fas fa-map-marker-alt me-2 text-primary"></i><?php echo t('contact_address'); ?></li>
                    <li><i class="fab fa-whatsapp me-2 text-success"></i><a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>"><?php echo WHATSAPP_DISPLAY; ?></a></li>
                    <li><i class="fas fa-envelope me-2 text-primary"></i><a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a></li>
                </ul>
                <div class="mt-3">
                    <h6 class="text-primary mb-2">Payment Methods</h6>
                    <div class="d-flex gap-3">
                        <span class="badge bg-success"><i class="fas fa-mobile-alt me-1"></i>JazzCash</span>
                        <span class="badge bg-warning text-dark"><i class="fas fa-mobile-alt me-1"></i>EasyPaisa</span>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-muted">&copy; <?php echo $currentYear; ?> Digital Kasur. <?php echo t('footer_rights'); ?>.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="<?php echo $home; ?>pages/privacy.php" class="text-muted text-decoration-none me-3"><?php echo t('footer_privacy'); ?></a>
                <a href="<?php echo $home; ?>pages/terms.php" class="text-muted text-decoration-none"><?php echo t('footer_terms'); ?></a>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button id="backToTop" class="btn btn-primary btn-back-top" title="Back to Top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?php echo $home; ?>assets/js/main.js"></script>

<?php if (isset($extraJS)): ?>
<script src="<?php echo $home; ?>assets/js/<?php echo $extraJS; ?>"></script>
<?php endif; ?>

</body>
</html>
