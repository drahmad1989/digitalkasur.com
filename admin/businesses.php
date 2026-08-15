<?php
/**
 * Admin Businesses Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Businesses';
$breadcrumb = ['Businesses' => 'businesses.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid token']);
        exit;
    }
    $field = clean_input($_POST['field']);
    $id = (int)$_POST['id'];
    $value = (int)$_POST['value'];
    $allowed_fields = ['is_active', 'is_featured', 'is_verified'];
    if (!in_array($field, $allowed_fields)) {
        echo json_encode(['success' => false, 'message' => 'Invalid field']);
        exit;
    }
    $result = DB::update('businesses', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_business'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request. Please try again.');
        redirect('businesses.php');
    }

    $name = clean_input($_POST['name']);
    $slug = generate_slug($name);
    $description = $_POST['description'] ?? '';
    $phone = clean_input($_POST['phone'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $address = clean_input($_POST['address'] ?? '');
    $website = clean_input($_POST['website'] ?? '');
    $whatsapp = clean_input($_POST['whatsapp'] ?? '');
    $city_id = (int)($_POST['city_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $rating = clean_input($_POST['rating'] ?? '0');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    if (empty($name)) {
        set_flash_message('error', 'Business name is required.');
    } else {
        $logo = '';
        $cover_image = '';

        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['logo']);
            if ($upload['success']) $logo = $upload['filename'];
        }
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['cover_image']);
            if ($upload['success']) $cover_image = $upload['filename'];
        }

        if ($id > 0) {
            $data = [
                'name' => $name, 'slug' => $slug, 'description' => $description,
                'phone' => $phone, 'email' => $email, 'address' => $address,
                'website' => $website, 'whatsapp' => $whatsapp,
                'city_id' => $city_id, 'category_id' => $category_id,
                'rating' => $rating, 'is_featured' => $is_featured,
                'is_verified' => $is_verified, 'is_active' => $is_active,
                'meta_title' => $meta_title, 'meta_description' => $meta_description,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($logo) $data['logo'] = $logo;
            if ($cover_image) $data['cover_image'] = $cover_image;
            DB::update('businesses', $data, 'id = ?', [$id]);
            set_flash_message('success', 'Business updated successfully.');
        } else {
            $data = [
                'name' => $name, 'slug' => $slug, 'description' => $description,
                'phone' => $phone, 'email' => $email, 'address' => $address,
                'website' => $website, 'whatsapp' => $whatsapp,
                'city_id' => $city_id, 'category_id' => $category_id,
                'rating' => $rating, 'is_featured' => $is_featured,
                'is_verified' => $is_verified, 'is_active' => $is_active,
                'meta_title' => $meta_title, 'meta_description' => $meta_description,
                'user_id' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            if ($logo) $data['logo'] = $logo;
            if ($cover_image) $data['cover_image'] = $cover_image;
            DB::insert('businesses', $data);
            set_flash_message('success', 'Business created successfully.');
        }
        redirect('businesses.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.');
        redirect('businesses.php');
    }
    $del_id = (int)$_POST['id'];
    DB::update('businesses', ['is_active' => 0, 'deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$del_id]);
    set_flash_message('success', 'Business deleted successfully.');
    redirect('businesses.php');
}

$cities = DB::select("SELECT * FROM cities WHERE is_active = 1 ORDER BY name ASC");
$categories = DB::select("SELECT * FROM categories WHERE type = 'business' AND is_active = 1 ORDER BY name ASC");

// ADD/EDIT FORM
if ($action === 'add' || $action === 'edit') {
    $business = [];
    if ($action === 'edit' && $id > 0) {
        $business = DB::selectOne("SELECT * FROM businesses WHERE id = ?", [$id]);
        if (!$business) {
            set_flash_message('error', 'Business not found.');
            redirect('businesses.php');
        }
    }

    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' Business';
    $breadcrumb = ['Businesses' => 'businesses.php', $page_title => ''];

    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo $action === 'add' ? 'Add New Business' : 'Edit Business'; ?></h1>
            <p class="page-subtitle">Fill in the business details below.</p>
        </div>
        <a href="businesses.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="save_business" value="1">

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required value="<?php echo clean_input($business['name'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"><?php echo $business['description'] ?? ''; ?></textarea>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo clean_input($business['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo clean_input($business['email'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">WhatsApp</label>
                                <input type="text" name="whatsapp" class="form-control" value="<?php echo clean_input($business['whatsapp'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control" value="<?php echo clean_input($business['website'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo clean_input($business['address'] ?? ''); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <?php if (!empty($business['logo'])): ?>
                                <div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($business['logo']); ?>" class="img-thumbnail-sm" style="width:80px;height:80px;object-fit:cover;" alt=""></div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cover Image</label>
                            <?php if (!empty($business['cover_image'])): ?>
                                <div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($business['cover_image']); ?>" class="img-thumbnail-sm" style="width:100%;max-height:100px;object-fit:cover;" alt=""></div>
                            <?php endif; ?>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <select name="city_id" class="form-select">
                                <option value="0">-- Select City --</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo $city['id']; ?>" <?php echo (isset($business['city_id']) && $business['city_id'] == $city['id']) ? 'selected' : ''; ?>><?php echo clean_input($city['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="0">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($business['category_id']) && $business['category_id'] == $cat['id']) ? 'selected' : ''; ?>><?php echo clean_input($cat['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <input type="number" name="rating" class="form-control" min="0" max="5" step="0.1" value="<?php echo clean_input($business['rating'] ?? '0'); ?>">
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo (!empty($business['is_featured'])) ? 'checked' : ''; ?>><label class="form-check-label" for="is_featured">Featured</label></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_verified" id="is_verified" <?php echo (!empty($business['is_verified'])) ? 'checked' : ''; ?>><label class="form-check-label" for="is_verified">Verified</label></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($business['is_active']) || !empty($business['is_active'])) ? 'checked' : ''; ?>><label class="form-check-label" for="is_active">Active</label></div>
                        </div>
                        <hr>
                        <h6 class="mb-3"><i class="fas fa-search me-1"></i> SEO</h6>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="<?php echo clean_input($business['meta_title'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3"><?php echo clean_input($business['meta_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Business</button>
                    <a href="businesses.php" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_city = (int)($_GET['city'] ?? 0);
$filter_category = (int)($_GET['category'] ?? 0);
$filter_status = clean_input($_GET['status'] ?? '');
$sort = clean_input($_GET['sort'] ?? 'created_at');
$order = clean_input($_GET['order'] ?? 'DESC');

$where = "1=1";
$params = [];
if ($search) { $where .= " AND (b.name LIKE ? OR b.address LIKE ? OR b.phone LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($filter_city) { $where .= " AND b.city_id = ?"; $params[] = $filter_city; }
if ($filter_category) { $where .= " AND b.category_id = ?"; $params[] = $filter_category; }
if ($filter_status === 'active') { $where .= " AND b.is_active = 1"; }
elseif ($filter_status === 'inactive') { $where .= " AND b.is_active = 0"; }
elseif ($filter_status === 'featured') { $where .= " AND b.is_featured = 1"; }
elseif ($filter_status === 'verified') { $where .= " AND b.is_verified = 1"; }

$allowed_sorts = ['name', 'rating', 'created_at']; if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';
$allowed_orders = ['ASC', 'DESC']; if (!in_array($order, $allowed_orders)) $order = 'DESC';

$total = DB::count("businesses b", $where, $params);
$pagination = paginate($total, 15);

$businesses = DB::select(
    "SELECT b.*, c.name as city_name, cat.name as category_name
     FROM businesses b LEFT JOIN cities c ON b.city_id = c.id LEFT JOIN categories cat ON b.category_id = cat.id
     WHERE {$where} ORDER BY b.{$sort} {$order} LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>

<div class="page-header">
    <div><h1 class="page-title">Businesses</h1><p class="page-subtitle">Manage all businesses</p></div>
    <a href="businesses.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Business</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search businesses..." value="<?php echo clean_input($search); ?>">
    <select name="city" class="form-select filter-auto-submit">
        <option value="">All Cities</option>
        <?php foreach ($cities as $city): ?><option value="<?php echo $city['id']; ?>" <?php echo $filter_city == $city['id'] ? 'selected' : ''; ?>><?php echo clean_input($city['name']); ?></option><?php endforeach; ?>
    </select>
    <select name="category" class="form-select filter-auto-submit">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php echo $filter_category == $cat['id'] ? 'selected' : ''; ?>><?php echo clean_input($cat['name']); ?></option><?php endforeach; ?>
    </select>
    <select name="status" class="form-select filter-auto-submit">
        <option value="">All Status</option>
        <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $filter_status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
        <option value="featured" <?php echo $filter_status === 'featured' ? 'selected' : ''; ?>>Featured</option>
        <option value="verified" <?php echo $filter_status === 'verified' ? 'selected' : ''; ?>>Verified</option>
    </select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="businesses.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Logo</th>
                        <th><a href="?sort=name&order=<?php echo $sort==='name'&&$order==='ASC'?'DESC':'ASC'; ?>&search=<?php echo urlencode($search); ?>&city=<?php echo $filter_city; ?>&category=<?php echo $filter_category; ?>&status=<?php echo $filter_status; ?>" class="sort-link">Name <?php if($sort==='name'):?><i class="fas fa-sort-<?php echo $order==='ASC'?'up':'down';?>"></i><?php endif;?></a></th>
                        <th>City</th><th>Category</th><th>Rating</th><th>Featured</th><th>Verified</th><th>Active</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($businesses)): ?>
                    <tr><td colspan="10"><div class="empty-state"><i class="fas fa-store"></i><h5>No Businesses Found</h5></div></td></tr>
                <?php else: ?>
                    <?php foreach ($businesses as $i => $biz): ?>
                    <tr>
                        <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                        <td><?php if (!empty($biz['logo'])): ?><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($biz['logo']); ?>" class="img-thumbnail-sm" alt=""><?php else: ?><div class="img-thumbnail-sm bg-light d-flex align-items-center justify-content-center"><i class="fas fa-store text-muted"></i></div><?php endif; ?></td>
                        <td><strong><?php echo clean_input($biz['name']); ?></strong><?php if(!empty($biz['address'])):?><br><small class="text-muted"><?php echo clean_input($biz['address']); ?></small><?php endif; ?></td>
                        <td><?php echo clean_input($biz['city_name'] ?? '-'); ?></td>
                        <td><?php echo clean_input($biz['category_name'] ?? '-'); ?></td>
                        <td><?php echo render_stars($biz['rating'] ?? 0); ?></td>
                        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="businesses.php" data-field="is_featured" data-id="<?php echo $biz['id']; ?>" <?php echo $biz['is_featured']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
                        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="businesses.php" data-field="is_verified" data-id="<?php echo $biz['id']; ?>" <?php echo $biz['is_verified']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
                        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="businesses.php" data-field="is_active" data-id="<?php echo $biz['id']; ?>" <?php echo $biz['is_active']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
                        <td>
                            <a href="businesses.php?action=edit&id=<?php echo $biz['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline;"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $biz['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete" onclick="return confirm('Delete this business?')"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'businesses.php'); ?></div>

<?php include 'includes/footer.php'; ?>
