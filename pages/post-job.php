<?php
/**
 * DigitalKasur.com - Post Job Page
 * Form for employers to post job listings
 */

require_once __DIR__ . '/../config.php';

$page_title = 'Post a Job - ' . SITE_NAME;
$page_description = 'Post your job opening for free on DigitalKasur and reach thousands of job seekers.';

require_once __DIR__ . '/../header.php';

$cities = get_all_cities();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title'] ?? '');
    $company_name = clean_input($_POST['company_name'] ?? '');
    $description = clean_input($_POST['description'] ?? '');
    $skills = clean_input($_POST['skills'] ?? '');
    $salary = clean_input($_POST['salary'] ?? '');
    $salary_min = !empty($_POST['salary_min']) ? (float)$_POST['salary_min'] : null;
    $salary_max = !empty($_POST['salary_max']) ? (float)$_POST['salary_max'] : null;
    $location = clean_input($_POST['location'] ?? '');
    $city_id = (int)($_POST['city_id'] ?? 0);
    $job_type = clean_input($_POST['job_type'] ?? 'full-time');
    $education_level = clean_input($_POST['education_level'] ?? '');
    $experience_level = clean_input($_POST['experience_level'] ?? '');
    $deadline = clean_input($_POST['deadline'] ?? '');

    if (empty($title)) $errors[] = 'Job title is required.';
    if (empty($company_name)) $errors[] = 'Company name is required.';
    if (empty($city_id)) $errors[] = 'Please select a city.';
    if (empty($deadline)) $errors[] = 'Application deadline is required.';

    if (empty($errors)) {
        $slug = generate_slug($title . '-' . $company_name);
        $existing = DB::selectOne("SELECT id FROM jobs WHERE slug = ?", [$slug]);
        if ($existing) $slug .= '-' . time();

        $user_id = is_logged_in() ? $_SESSION['user_id'] : 1;
        $result = DB::insert('jobs', [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'company_name' => $company_name,
            'skills' => $skills,
            'salary' => $salary,
            'salary_min' => $salary_min,
            'salary_max' => $salary_max,
            'location' => $location,
            'city_id' => $city_id,
            'category_id' => 1, // Default category
            'user_id' => $user_id,
            'job_type' => $job_type,
            'education_level' => $education_level,
            'experience_level' => $experience_level,
            'deadline' => $deadline,
            'is_active' => 1,
        ]);

        if ($result) {
            set_flash_message('success', 'Job posted successfully! It will be reviewed and published soon.');
            redirect('jobs.php');
        } else {
            $errors[] = 'Failed to post job. Please try again.';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container text-center">
        <span class="section-badge"><i class="fas fa-plus-circle me-1"></i> Post Job</span>
        <h1 class="page-title"><?php _e('jobs_post'); ?></h1>
        <p class="page-subtitle">Post your job opening for free</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Job Title *</label>
                                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Company Name *</label>
                                    <input type="text" name="company_name" class="form-control" required value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Job Description</label>
                                    <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Skills Required</label>
                                    <input type="text" name="skills" class="form-control" placeholder="e.g., PHP, MySQL, SEO (comma separated)" value="<?php echo htmlspecialchars($_POST['skills'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Salary Range</label>
                                    <input type="text" name="salary" class="form-control" placeholder="e.g., 20,000 - 40,000 PKR" value="<?php echo htmlspecialchars($_POST['salary'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Min Salary (PKR)</label>
                                    <input type="number" name="salary_min" class="form-control" value="<?php echo htmlspecialchars($_POST['salary_min'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Max Salary (PKR)</label>
                                    <input type="number" name="salary_max" class="form-control" value="<?php echo htmlspecialchars($_POST['salary_max'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Location</label>
                                    <input type="text" name="location" class="form-control" placeholder="e.g., GT Road, Kasur" value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">City *</label>
                                    <select name="city_id" class="form-select" required>
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $city): ?>
                                            <option value="<?php echo $city['id']; ?>" <?php echo (isset($_POST['city_id']) && $_POST['city_id'] == $city['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($city['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Job Type</label>
                                    <select name="job_type" class="form-select">
                                        <option value="full-time" <?php echo ($_POST['job_type'] ?? '') === 'full-time' ? 'selected' : ''; ?>>Full Time</option>
                                        <option value="part-time" <?php echo ($_POST['job_type'] ?? '') === 'part-time' ? 'selected' : ''; ?>>Part Time</option>
                                        <option value="contract" <?php echo ($_POST['job_type'] ?? '') === 'contract' ? 'selected' : ''; ?>>Contract</option>
                                        <option value="freelance" <?php echo ($_POST['job_type'] ?? '') === 'freelance' ? 'selected' : ''; ?>>Freelance</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Education Level</label>
                                    <input type="text" name="education_level" class="form-control" placeholder="e.g., Matric, Intermediate, Bachelor" value="<?php echo htmlspecialchars($_POST['education_level'] ?? ''); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Experience Level</label>
                                    <input type="text" name="experience_level" class="form-control" placeholder="e.g., 2-3 years, Fresh" value="<?php echo htmlspecialchars($_POST['experience_level'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Application Deadline *</label>
                                    <input type="date" name="deadline" class="form-control" required value="<?php echo htmlspecialchars($_POST['deadline'] ?? ''); ?>">
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Post Job - Free
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
