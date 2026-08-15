<?php
/**
 * DigitalKasur.com - Contact Page
 * Contact form and information
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Contact Us - ' . SITE_NAME;
$page_description = 'Get in touch with DigitalKasur for event management and digital services in Kasur District.';

require_once __DIR__ . '/../header.php';

$cities = get_all_cities();
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = clean_input($_POST['message'] ?? '');

    if (empty($name)) $errors[] = 'Name is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($subject)) $errors[] = 'Subject is required.';
    if (empty($message)) $errors[] = 'Message is required.';

    if (empty($errors)) {
        $result = DB::insert('contact_messages', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'is_read' => 0,
        ]);

        if ($result) {
            set_flash_message('success', __('contact_success'));
            redirect('contact.php');
        } else {
            $errors[] = __('contact_error');
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <span class="section-badge"><i class="fas fa-envelope me-1"></i> Get In Touch</span>
        <h1 class="page-title"><?php _e('contact_title'); ?></h1>
        <p class="page-subtitle"><?php _e('contact_subtitle'); ?></p>
    </div>
</section>

<!-- Contact Content -->
<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Contact Information</h3>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h6 class="mb-1"><?php _e('contact_address'); ?></h6>
                                <p class="text-muted mb-0">Kasur, Punjab, Pakistan</p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                            <div>
                                <h6 class="mb-1"><?php _e('contact_phone'); ?></h6>
                                <p class="mb-0"><a href="tel:<?php echo str_replace([' ', '-'], '', ADMIN_PHONE); ?>"><?php echo ADMIN_PHONE; ?></a></p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon" style="background:rgba(37,211,102,0.1);color:#25D366;"><i class="fab fa-whatsapp"></i></div>
                            <div>
                                <h6 class="mb-1">WhatsApp</h6>
                                <p class="mb-0"><a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>" target="_blank" rel="noopener"><?php echo ADMIN_PHONE; ?></a></p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon" style="background:rgba(var(--secondary-rgb),0.15);color:var(--secondary-dark);"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h6 class="mb-1"><?php _e('contact_email_label'); ?></h6>
                                <p class="mb-0"><a href="mailto:<?php echo ADMIN_EMAIL; ?>"><?php echo ADMIN_EMAIL; ?></a></p>
                            </div>
                        </div>

                        <div class="contact-info-item mb-4">
                            <div class="contact-icon" style="background:rgba(16,185,129,0.1);color:var(--success-color);"><i class="fas fa-clock"></i></div>
                            <div>
                                <h6 class="mb-1"><?php _e('contact_hours'); ?></h6>
                                <p class="text-muted mb-0"><?php _e('contact_hours_value'); ?></p>
                            </div>
                        </div>

                        <!-- Social Links -->
                        <div class="mt-4">
                            <h6 class="mb-3"><?php _e('follow_us'); ?></h6>
                            <div class="d-flex gap-2">
                                <a href="<?php echo SOCIAL_FACEBOOK; ?>" class="btn btn-icon" style="background:rgba(59,89,152,0.1);color:#3b5998;" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
                                <a href="<?php echo SOCIAL_INSTAGRAM; ?>" class="btn btn-icon" style="background:rgba(225,48,108,0.1);color:#e1306c;" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>
                                <a href="<?php echo SOCIAL_YOUTUBE; ?>" class="btn btn-icon" style="background:rgba(255,0,0,0.1);color:#ff0000;" target="_blank" rel="noopener"><i class="fab fa-youtube"></i></a>
                                <a href="<?php echo SOCIAL_TIKTOK; ?>" class="btn btn-icon" style="background:rgba(0,0,0,0.1);color:#000;" target="_blank" rel="noopener"><i class="fab fa-tiktok"></i></a>
                                <a href="https://wa.me/<?php echo ADMIN_WHATSAPP; ?>" class="btn btn-icon" style="background:rgba(37,211,102,0.1);color:#25D366;" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Send Us a Message</h3>

                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Name *</label>
                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address *</label>
                                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Subject *</label>
                                    <input type="text" name="subject" class="form-control" required value="<?php echo htmlspecialchars($_POST['subject'] ?? $_GET['subject'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Message *</label>
                                    <textarea name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i><?php _e('contact_send'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Maps -->
<section class="map-section">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d109740.96489736789!2d74.4044!3d31.1215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39187e2e8e1e8b0d%3A0x7d0e8e0e0e0e0e0e!2sKasur%2C%20Pakistan!5e0!3m2!1sen!2s!4v1650000000000"
            width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<!-- Cities Section -->
<section class="section-padding">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title"><?php _e('cities_title'); ?></h2>
        </div>
        <div class="row g-3 justify-content-center">
            <?php foreach ($cities as $city): ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="cities/<?php echo $city['slug']; ?>.php" class="city-card">
                        <span class="city-card-name"><?php echo htmlspecialchars($city['name']); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.contact-info-item { display: flex; gap: 1rem; }
.contact-icon { width: 48px; height: 48px; border-radius: var(--radius-lg); background: rgba(var(--primary-rgb),0.1); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
.map-section { border-top: 1px solid var(--border-color); }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
