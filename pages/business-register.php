<?php
/**
 * DigitalKasur.com - Business Registration Page
 * Form for businesses to register in the directory
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Register Your Business - ' . SITE_NAME;
$page_description = 'Add your business to the DigitalKasur directory and reach thousands of customers.';

require_once __DIR__ . '/../header.php';

$cities = get_all_cities();
$biz_categories = get_categories_by_type('business');
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean_input($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $city_id = (int)($_POST['city_id'] ?? 0);
    $description = clean_input($_POST['description'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $whatsapp = clean_input($_POST['whatsapp'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $website = clean_input($_POST['website'] ?? '');
    $address = clean_input($_POST['address'] ?? '');

    // Validation
    if (empty($name)) $errors[] = 'Business name is required.';
    if (empty($category_id)) $errors[] = 'Please select a category.';
    if (empty($city_id)) $errors[] = 'Please select a city.';
    if (empty($phone)) $errors[] = 'Phone number is required.';

    if (empty($errors)) {
        $slug = generate_slug($name);
        // Ensure unique slug
        $existing = DB::selectOne("SELECT id FROM businesses WHERE slug = ?", [$slug]);
        if ($existing) $slug .= '-' . time();

        $logo_filename = null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['logo']);
            if ($upload['success']) {
                $logo_filename = $upload['filename'];
            } else {
                $errors[] = $upload['message'];
            }
        }

        if (empty($errors)) {
            $user_id = is_logged_in() ? $_SESSION['user_id'] : 1; // Default to admin if not logged in
            $result = DB::insert('businesses', [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'logo' => $logo_filename,
                'phone' => $phone,
                'whatsapp' => $whatsapp,
                'email' => $email,
                'website' => $website,
                'address' => $address,
                'city_id' => $city_id,
                'category_id' => $category_id,
                'user_id' => $user_id,
                'is_active' => 1,
            ]);

            if ($result) {
                set_flash_message('success', 'Your business has been registered successfully! It will be reviewed and listed soon.');
                redirect('business-directory.php');
            } else {
                $errors[] = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="text-center">
            <span class="section-badge"><i class="fas fa-plus-circle me-1"></i> Register</span>
            <h1 class="page-title"><?php _e('biz_add'); ?></h1>
            <p class="page-subtitle">List your business for free and reach thousands of customers</p>
        </div>
    </div>
</section>

<!-- Registration Form -->
<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Business Name *</label>
                                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Category *</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($biz_categories as $cat): ?>
                                            <option value="<?php echo $cat['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City *</label>
                                    <select name="city_id" class="form-select" required>
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $city): ?>
                                            <option value="<?php echo $city['id']; ?>" <?php echo (isset($_POST['city_id']) && $_POST['city_id'] == $city['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($city['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number *</label>
                                    <input type="tel" name="phone" class="form-control" placeholder="+92-XXX-XXXXXXX" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Tell us about your business..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">WhatsApp Number</label>
                                    <input type="tel" name="whatsapp" class="form-control" placeholder="923XXXXXXXXX" value="<?php echo htmlspecialchars($_POST['whatsapp'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="business@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Website</label>
                                    <input type="url" name="website" class="form-control" placeholder="https://example.com" value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*">
                                    <small class="text-muted">Max 5MB. JPG, PNG, WebP allowed.</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Address</label>
                                    <input type="text" name="address" class="form-control" placeholder="Full business address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-check-circle me-2"></i>Register Business
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

<style>
.page-header { background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); padding: calc(var(--topbar-height) + var(--navbar-height) + 3rem) 0 3rem; color: white; margin-top: calc(var(--topbar-height) + var(--navbar-height) - 6rem); }
.page-title { color: white; font-size: var(--font-size-3xl); margin-bottom: 0.5rem; }
.page-subtitle { color: rgba(255,255,255,0.85); margin-bottom: 0; }
.section-padding { padding: var(--spacer-3xl) 0; }
.form-control, .form-select { border-radius: var(--radius-md); border-color: var(--border-color); padding: 0.6rem 1rem; background: var(--bg-light); color: var(--text-color); }
.form-control:focus, .form-select:focus { border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.15); }
</style>

<?php require_once __DIR__ . '/../footer.php'; ?>
