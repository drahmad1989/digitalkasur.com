<?php
/**
 * Admin Jobs Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Jobs';
$breadcrumb = ['Jobs' => 'jobs.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false]); exit;
    }
    $field = clean_input($_POST['field']);
    $id = (int)$_POST['id'];
    $value = (int)$_POST['value'];
    if (!in_array($field, ['is_active', 'is_featured', 'is_urgent'])) { echo json_encode(['success' => false]); exit; }
    $result = DB::update('jobs', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_job'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('jobs.php');
    }
    $title = clean_input($_POST['title']);
    $slug = generate_slug($title);
    $description = $_POST['description'] ?? '';
    $company_name = clean_input($_POST['company_name'] ?? '');
    $company_email = clean_input($_POST['company_email'] ?? '');
    $company_phone = clean_input($_POST['company_phone'] ?? '');
    $salary_range = clean_input($_POST['salary_range'] ?? '');
    $job_type = clean_input($_POST['job_type'] ?? 'full-time');
    $location = clean_input($_POST['location'] ?? '');
    $city_id = (int)($_POST['city_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $deadline = clean_input($_POST['deadline'] ?? '');
    $requirements = $_POST['requirements'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($title) || empty($company_name)) {
        set_flash_message('error', 'Job title and company name are required.');
    } else {
        if ($id > 0) {
            DB::update('jobs', [
                'title'=>$title,'slug'=>$slug,'description'=>$description,'company_name'=>$company_name,
                'company_email'=>$company_email,'company_phone'=>$company_phone,'salary_range'=>$salary_range,
                'job_type'=>$job_type,'location'=>$location,'city_id'=>$city_id,'category_id'=>$category_id,
                'deadline'=>$deadline,'requirements'=>$requirements,'is_featured'=>$is_featured,
                'is_urgent'=>$is_urgent,'is_active'=>$is_active,'updated_at'=>date('Y-m-d H:i:s')
            ], 'id = ?', [$id]);
            set_flash_message('success', 'Job updated successfully.');
        } else {
            DB::insert('jobs', [
                'title'=>$title,'slug'=>$slug,'description'=>$description,'company_name'=>$company_name,
                'company_email'=>$company_email,'company_phone'=>$company_phone,'salary_range'=>$salary_range,
                'job_type'=>$job_type,'location'=>$location,'city_id'=>$city_id,'category_id'=>$category_id,
                'deadline'=>$deadline,'requirements'=>$requirements,'is_featured'=>$is_featured,
                'is_urgent'=>$is_urgent,'is_active'=>$is_active,'user_id'=>$_SESSION['user_id'],
                'created_at'=>date('Y-m-d H:i:s')
            ]);
            set_flash_message('success', 'Job created successfully.');
        }
        redirect('jobs.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('jobs.php');
    }
    DB::update('jobs', ['is_active' => 0, 'deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'Job deleted.'); redirect('jobs.php');
}

$cities = DB::select("SELECT * FROM cities WHERE is_active = 1 ORDER BY name ASC");
$categories = DB::select("SELECT * FROM categories WHERE type = 'job' AND is_active = 1 ORDER BY name ASC");
$job_types = ['full-time' => 'Full Time', 'part-time' => 'Part Time', 'contract' => 'Contract', 'internship' => 'Internship', 'freelance' => 'Freelance'];

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $job = [];
    if ($action === 'edit' && $id > 0) {
        $job = DB::selectOne("SELECT * FROM jobs WHERE id = ?", [$id]);
        if (!$job) { set_flash_message('error', 'Job not found.'); redirect('jobs.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' Job';
    $breadcrumb = ['Jobs' => 'jobs.php', $page_title => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add New Job':'Edit Job'; ?></h1></div>
        <a href="jobs.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_job" value="1">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3"><label class="form-label">Job Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" required value="<?php echo clean_input($job['title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5"><?php echo $job['description']??''; ?></textarea></div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Company Name <span class="text-danger">*</span></label><input type="text" name="company_name" class="form-control" required value="<?php echo clean_input($job['company_name']??''); ?>"></div>
                        <div class="col-md-6"><label class="form-label">Salary Range</label><input type="text" name="salary_range" class="form-control" placeholder="e.g. 30,000 - 50,000" value="<?php echo clean_input($job['salary_range']??''); ?>"></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4"><label class="form-label">Company Email</label><input type="email" name="company_email" class="form-control" value="<?php echo clean_input($job['company_email']??''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Company Phone</label><input type="text" name="company_phone" class="form-control" value="<?php echo clean_input($job['company_phone']??''); ?>"></div>
                        <div class="col-md-4"><label class="form-label">Job Type</label><select name="job_type" class="form-select"><?php foreach($job_types as $val=>$label): ?><option value="<?php echo $val; ?>" <?php echo ($job['job_type']??'full-time')===$val?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select></div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><label class="form-label">Location</label><input type="text" name="location" class="form-control" value="<?php echo clean_input($job['location']??''); ?>"></div>
                        <div class="col-md-6"><label class="form-label">Deadline</label><input type="date" name="deadline" class="form-control" value="<?php echo clean_input($job['deadline']??''); ?>"></div>
                    </div>
                    <div class="mb-3"><label class="form-label">Requirements</label><textarea name="requirements" class="form-control" rows="4"><?php echo $job['requirements']??''; ?></textarea></div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3"><label class="form-label">City</label><select name="city_id" class="form-select"><option value="0">-- Select City --</option><?php foreach($cities as $city): ?><option value="<?php echo $city['id']; ?>" <?php echo ($job['city_id']??0)==$city['id']?'selected':''; ?>><?php echo clean_input($city['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Category</label><select name="category_id" class="form-select"><option value="0">-- Select --</option><?php foreach($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php echo ($job['category_id']??0)==$cat['id']?'selected':''; ?>><?php echo clean_input($cat['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo !empty($job['is_featured'])?'checked':''; ?>><label class="form-check-label" for="is_featured">Featured</label></div></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_urgent" id="is_urgent" <?php echo !empty($job['is_urgent'])?'checked':''; ?>><label class="form-check-label" for="is_urgent">Urgent</label></div></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($job['is_active'])||!empty($job['is_active']))?'checked':''; ?>><label class="form-check-label" for="is_active">Active</label></div></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Job</button><a href="jobs.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <?php include 'includes/footer.php'; exit;
}

// View applications
if ($action === 'applications' && $id > 0) {
    $job = DB::selectOne("SELECT * FROM jobs WHERE id = ?", [$id]);
    if (!$job) { set_flash_message('error', 'Job not found.'); redirect('jobs.php'); }
    $applications = DB::select("SELECT * FROM job_applications WHERE job_id = ? ORDER BY created_at DESC", [$id]);
    $page_title = 'Applications - ' . $job['title'];
    $breadcrumb = ['Jobs' => 'jobs.php', 'Applications' => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title">Applications: <?php echo clean_input($job['title']); ?></h1></div>
        <a href="jobs.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover mb-0"><thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Cover Letter</th><th>Applied</th></tr></thead><tbody>
        <?php if(empty($applications)): ?><tr><td colspan="6"><div class="empty-state"><i class="fas fa-inbox"></i><h5>No Applications Yet</h5></div></td></tr>
        <?php else: foreach($applications as $i=>$app): ?>
        <tr><td><?php echo $i+1; ?></td><td><?php echo clean_input($app['name']); ?></td><td><?php echo clean_input($app['email']); ?></td><td><?php echo clean_input($app['phone']??'-'); ?></td><td><?php echo truncate_text(clean_input($app['cover_letter']??''),80); ?></td><td><?php echo time_ago($app['created_at']); ?></td></tr>
        <?php endforeach; endif; ?>
        </tbody></table>
    </div></div></div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_city = (int)($_GET['city'] ?? 0);
$filter_type = clean_input($_GET['type'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (j.title LIKE ? OR j.company_name LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_city) { $where .= " AND j.city_id = ?"; $params[]=$filter_city; }
if ($filter_type) { $where .= " AND j.job_type = ?"; $params[]=$filter_type; }
if ($filter_status==='active') { $where .= " AND j.is_active = 1"; }
elseif ($filter_status==='inactive') { $where .= " AND j.is_active = 0"; }
elseif ($filter_status==='featured') { $where .= " AND j.is_featured = 1"; }
elseif ($filter_status==='urgent') { $where .= " AND j.is_urgent = 1"; }

$total = DB::count("jobs j", $where, $params);
$pagination = paginate($total, 15);

$jobs = DB::select(
    "SELECT j.*, c.name as city_name FROM jobs j LEFT JOIN cities c ON j.city_id = c.id WHERE {$where} ORDER BY j.created_at DESC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>

<div class="page-header">
    <div><h1 class="page-title">Jobs</h1><p class="page-subtitle">Manage all job listings</p></div>
    <a href="jobs.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Job</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search jobs..." value="<?php echo clean_input($search); ?>">
    <select name="city" class="form-select filter-auto-submit"><option value="">All Cities</option><?php foreach($cities as $city): ?><option value="<?php echo $city['id']; ?>" <?php echo $filter_city==$city['id']?'selected':''; ?>><?php echo clean_input($city['name']); ?></option><?php endforeach; ?></select>
    <select name="type" class="form-select filter-auto-submit"><option value="">All Types</option><?php foreach($job_types as $val=>$label): ?><option value="<?php echo $val; ?>" <?php echo $filter_type===$val?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select>
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="active" <?php echo $filter_status==='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo $filter_status==='inactive'?'selected':''; ?>>Inactive</option><option value="featured" <?php echo $filter_status==='featured'?'selected':''; ?>>Featured</option><option value="urgent" <?php echo $filter_status==='urgent'?'selected':''; ?>>Urgent</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="jobs.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>Title</th><th>Company</th><th>City</th><th>Type</th><th>Salary</th><th>Deadline</th><th>Featured</th><th>Urgent</th><th>Active</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($jobs)): ?><tr><td colspan="11"><div class="empty-state"><i class="fas fa-briefcase"></i><h5>No Jobs Found</h5></div></td></tr>
    <?php else: foreach($jobs as $i=>$job): ?>
    <tr>
        <td><?php echo $pagination['offset']+$i+1; ?></td>
        <td><strong><?php echo clean_input($job['title']); ?></strong></td>
        <td><?php echo clean_input($job['company_name']); ?></td>
        <td><?php echo clean_input($job['city_name']??'-'); ?></td>
        <td><span class="badge bg-info"><?php echo ucfirst($job['job_type']??''); ?></span></td>
        <td><?php echo clean_input($job['salary_range']??'-'); ?></td>
        <td><?php echo format_date($job['deadline']??'','M j, Y'); ?></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="jobs.php" data-field="is_featured" data-id="<?php echo $job['id']; ?>" <?php echo $job['is_featured']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="jobs.php" data-field="is_urgent" data-id="<?php echo $job['id']; ?>" <?php echo $job['is_urgent']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="jobs.php" data-field="is_active" data-id="<?php echo $job['id']; ?>" <?php echo $job['is_active']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td>
            <a href="jobs.php?action=edit&id=<?php echo $job['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <a href="jobs.php?action=applications&id=<?php echo $job['id']; ?>" class="action-btn action-btn-view" title="Applications"><i class="fas fa-users"></i></a>
            <form method="POST" style="display:inline;"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $job['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete" onclick="return confirm('Delete this job?')"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'jobs.php'); ?></div>

<?php include 'includes/footer.php'; ?>
