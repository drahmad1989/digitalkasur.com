<?php
/**
 * Footer File - DigitalKasur.com
 * Modern footer with WhatsApp, dark mode, social links
 */
?>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <!-- About Us -->
                <div class="col-lg-3 col-md-6">
                    <div class="footer-brand mb-3">
                        <img src="<?php echo $assets_path ?? 'assets/'; ?>images/logo.webp" alt="DigitalKasur" height="40"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<h5 class=\'text-warning mb-3\'>DigitalKasur</h5>';">
                    </div>
                    <p class="footer-about">
                        <?php _e('hero_subtitle'); ?>
                    </p>
                    <div class="footer-social">
                        <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo SOCIAL_YOUTUBE; ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="<?php echo SOCIAL_TIKTOK; ?>" target="_blank" rel="noopener" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="<?php echo SOCIAL_TWITTER; ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="<?php echo SOCIAL_WHATSAPP_CHANNEL; ?>" target="_blank" rel="noopener" aria-label="WhatsApp Channel"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading"><?php _e('quick_links'); ?></h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo $is_city ? '../../index.php' : 'index.php'; ?>"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_home'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>events.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_events'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_digital'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>business-directory.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_business'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>jobs.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_jobs'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>news.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_news'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>contact.php"><i class="fas fa-chevron-right me-1"></i><?php _e('nav_contact'); ?></a></li>
                    </ul>
                </div>

                <!-- Our Services -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading"><?php _e('our_services'); ?></h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>events.php"><i class="fas fa-chevron-right me-1"></i><?php _e('events_wedding'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php?type=website"><i class="fas fa-chevron-right me-1"></i><?php _e('digital_web'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php?type=ecommerce"><i class="fas fa-chevron-right me-1"></i><?php _e('digital_ecommerce'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php?type=design"><i class="fas fa-chevron-right me-1"></i><?php _e('digital_design'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php?type=seo"><i class="fas fa-chevron-right me-1"></i><?php _e('digital_seo'); ?></a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>digital-services.php?type=social"><i class="fas fa-chevron-right me-1"></i><?php _e('digital_social'); ?></a></li>
                    </ul>
                </div>

                <!-- Blog & More -->
                <div class="col-lg-2 col-md-6">
                    <h6 class="footer-heading">Blog</h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>blog.php"><i class="fas fa-chevron-right me-1"></i>All Blogs</a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>blog.php?category=event-tips"><i class="fas fa-chevron-right me-1"></i>Event Tips</a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>blog.php?category=digital-marketing"><i class="fas fa-chevron-right me-1"></i>Digital Marketing</a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>blog.php?category=technology"><i class="fas fa-chevron-right me-1"></i>Technology</a></li>
                        <li><a href="<?php echo $base_path ?? 'pages/'; ?>blog.php?category=business"><i class="fas fa-chevron-right me-1"></i>Business</a></li>
                    </ul>
                    <h6 class="footer-heading mt-3"><?php _e('nav_cities'); ?></h6>
                    <ul class="footer-links">
                        <li><a href="<?php echo $city_path ?? 'pages/cities/'; ?>kasur.php"><i class="fas fa-chevron-right me-1"></i>Kasur</a></li>
                        <li><a href="<?php echo $city_path ?? 'pages/cities/'; ?>pattoki.php"><i class="fas fa-chevron-right me-1"></i>Pattoki</a></li>
                        <li><a href="<?php echo $city_path ?? 'pages/cities/'; ?>chunian.php"><i class="fas fa-chevron-right me-1"></i>Chunian</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6">
                    <h6 class="footer-heading"><?php _e('contact_us'); ?></h6>
                    <ul class="footer-contact">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Kasur, Punjab, Pakistan</span>
                        </li>
                        <li>
                            <i class="fas fa-phone-alt"></i>
                            <a href="tel:<?php echo str_replace([' ', '-'], '', ADMIN_PHONE); ?>"><?php echo ADMIN_PHONE; ?></a>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>" target="_blank" rel="noopener">WhatsApp: <?php echo ADMIN_PHONE; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <span><?php _e('contact_hours_value'); ?></span>
                        </li>
                    </ul>

                    <!-- Newsletter -->
                    <div class="footer-newsletter mt-3">
                        <h6 class="footer-heading"><?php _e('blog_subscribe'); ?></h6>
                        <form class="newsletter-form" action="<?php echo $base_path ?? 'pages/'; ?>newsletter.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <div style="display:none;"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Your email" name="email" required>
                                <button class="btn btn-warning" type="submit"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">
                            &copy; <?php echo date('Y'); ?> <strong>DigitalKasur.com</strong>. <?php _e('all_rights'); ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="<?php echo SITE_URL; ?>/privacy-policy.php" class="footer-bottom-link"><?php _e('privacy_policy'); ?></a>
                        <span class="mx-2 opacity-50">|</span>
                        <a href="<?php echo SITE_URL; ?>/terms.php" class="footer-bottom-link"><?php _e('terms'); ?></a>
                        <span class="mx-2 opacity-50">|</span>
                        <a href="<?php echo SITE_URL; ?>/data-deletion.php" class="footer-bottom-link">Data Deletion</a>
                        <span class="mx-2 opacity-50">|</span>
                        <a href="sitemap.php" class="footer-bottom-link">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- AI Chatbot Button -->
    <a href="#" class="ai-chatbot-btn" id="aiChatbotBtn" aria-label="AI Assistant" title="AI Assistant">
        <i class="fas fa-robot"></i>
    </a>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>?text=<?php echo urlencode(__('whatsapp_message')); ?>"
       class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
       title="<?php _e('whatsapp_chat'); ?>">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" aria-label="<?php _e('back_to_top'); ?>" title="<?php _e('back_to_top'); ?>">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo $assets_path ?? 'assets/'; ?>js/main.js"></script>

</body>
</html>
