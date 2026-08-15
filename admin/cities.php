<?php
/**
 * Admin Cities Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Cities';
$breadcrumb = ['Cities' => 'cities.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $field = clean_input($_POST['field']); $id = (int)$_POST['id']; $value = (int)$_POST['value'];
    if (!in_array($field, ['is_active'])) { echo json_encode(['success' => false]); exit; }
    $result = DB::update('cities', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_city'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('cities.php');
    }
    $name = clean_input($_POST['name']);
    $slug = generate_slug($name);
    $description = $_POST['description'] ?? '';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    if (empty($name)) {
        set_flash_message('error', 'City name is required.');
    } else {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['image']);
            if ($upload['success']) $image = $upload['filename'];
        }

        if ($id > 0) {
            $data = ['name'=>$name,'slug'=>$slug,'description'=>$description,'sort_order'=>$sort_order,'is_active'=>$is_active,'meta_title'=>$meta_title,'meta_description'=>$meta_description,'updated_at'=>date('Y-m-d H:i:s')];
            if ($image) $data['image'] = $image;
            DB::update('cities', $data, 'id = ?', [$id]);
            set_flash_message('success', 'City updated.');
        } else {
            $data = ['name'=>$name,'slug'=>$slug,'description'=>$description,'sort_order'=>$sort_order,'is_active'=>$is_active,'meta_title'=>$meta_title,'meta_description'=>$meta_description,'created_at'=>date('Y-m-d H:i:s')];
            if ($image) $data['image'] = $image;
            DB::insert('cities', $data);
            set_flash_message('success', 'City created.');
        }
        redirect('cities.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('cities.php'); }
    DB::delete('cities', 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'City deleted.'); redirect('cities.php');
}

// Handle reorder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reorder') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $orders = $_POST['orders'] ?? [];
    foreach ($orders as $item_id => $sort_order) {
        DB::update('cities', ['sort_order' => (int)$sort_order], 'id = ?', [(int)$item_id]);
    }
    echo json_encode(['success' => true]); exit;
}

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $city = [];
    if ($action === 'edit' && $id > 0) {
        $city = DB::selectOne("SELECT * FROM cities WHERE id = ?", [$id]);
        if (!$city) { set_flash_message('error', 'City not found.'); redirect('cities.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' City';
    $breadcrumb = ['Cities' => 'cities.php', $page_title => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add New City':'Edit City'; ?></h1></div>
        <a href="cities.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_city" value="1">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3"><label class="form-label">City Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required value="<?php echo clean_input($city['name']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="5"><?php echo $city['description']??''; ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?php echo clean_input($city['meta_title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?php echo clean_input($city['meta_description']??''); ?></textarea></div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">City Image</label>
                        <?php if(!empty($city['image'])): ?><div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($city['image']); ?>" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;" alt=""></div><?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" min="0" value="<?php echo $city['sort_order']??0; ?>"><small class="text-muted">Lower numbers appear first.</small></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($city['is_active'])||!empty($city['is_active']))?'checked':''; ?>><label class="form-check-label" for="is_active">Active</label></div></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save City</button><a href="cities.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (name LIKE ? OR description LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_status==='active') $where .= " AND is_active = 1";
elseif ($filter_status==='inactive') $where .= " AND is_active = 0";

$total = DB::count("cities", $where, $params);
$pagination = paginate($total, 20);
$cities = DB::select("SELECT c.*, (SELECT COUNT(*) FROM events WHERE city_id = c.id) as events_count, (SELECT COUNT(*) FROM businesses WHERE city_id = c.id) as businesses_count, (SELECT COUNT(*) FROM jobs WHERE city_id = c.id) as jobs_count FROM cities c WHERE {$where} ORDER BY c.sort_order ASC, c.name ASC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}", $params);
?>

<div class="page-header">
    <div><h1 class="page-title">Cities</h1><p class="page-subtitle">Manage cities across the platform</p></div>
    <a href="cities.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add City</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search cities..." value="<?php echo clean_input($search); ?>">
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="active" <?php echo $filter_status==='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo $filter_status==='inactive'?'selected':''; ?>>Inactive</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="cities.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>Sort</th><th>Image</th><th>Name</th><th>Slug</th><th>Events</th><th>Businesses</th><th>Jobs</th><th>Active</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($cities)): ?><tr><td colspan="9"><div class="empty-state"><i class="fas fa-city"></i><h5>No Cities Found</h5></div></td></tr>
    <?php else: foreach($cities as $i=>$city): ?>
    <tr>
        <td><span class="badge bg-secondary"><?php echo $city['sort_order']; ?></span></td>
        <td><?php if(!empty($city['image'])): ?><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($city['image']); ?>" class="img-thumbnail-sm" alt=""><?php else: ?><div class="img-thumbnail-sm bg-light d-flex align-items-center justify-content-center"><i class="fas fa-city text-muted"></i></div><?php endif; ?></td>
        <td><strong><?php echo clean_input($city['name']); ?></strong></td>
        <td><code><?php echo clean_input($city['slug']); ?></code></td>
        <td><span class="badge bg-primary"><?php echo $city['events_count']; ?></span></td>
        <td><span class="badge bg-warning text-dark"><?php echo $city['businesses_count']; ?></span></td>
        <td><span class="badge bg-success"><?php echo $city['jobs_count']; ?></span></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="cities.php" data-field="is_active" data-id="<?php echo $city['id']; ?>" <?php echo $city['is_active']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td>
            <a href="cities.php?action=edit&id=<?php echo $city['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this city?')"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $city['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'cities.php'); ?></div>

<?php include 'includes/footer.php'; ?>
