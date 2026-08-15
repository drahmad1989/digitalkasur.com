<?php
/**
 * Admin Categories Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Categories';
$breadcrumb = ['Categories' => 'categories.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$category_types = ['event' => 'Events', 'business' => 'Businesses', 'job' => 'Jobs', 'news' => 'News', 'blog' => 'Blog'];

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $field = clean_input($_POST['field']); $id = (int)$_POST['id']; $value = (int)$_POST['value'];
    if (!in_array($field, ['is_active'])) { echo json_encode(['success' => false]); exit; }
    $result = DB::update('categories', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('categories.php');
    }
    $name = clean_input($_POST['name']);
    $slug = generate_slug($name);
    $type = clean_input($_POST['type'] ?? 'event');
    $icon = clean_input($_POST['icon'] ?? 'fas fa-folder');
    $description = $_POST['description'] ?? '';
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name)) {
        set_flash_message('error', 'Category name is required.');
    } else {
        if ($id > 0) {
            DB::update('categories', ['name'=>$name,'slug'=>$slug,'type'=>$type,'icon'=>$icon,'description'=>$description,'sort_order'=>$sort_order,'is_active'=>$is_active,'updated_at'=>date('Y-m-d H:i:s')], 'id = ?', [$id]);
            set_flash_message('success', 'Category updated.');
        } else {
            DB::insert('categories', ['name'=>$name,'slug'=>$slug,'type'=>$type,'icon'=>$icon,'description'=>$description,'sort_order'=>$sort_order,'is_active'=>$is_active,'created_at'=>date('Y-m-d H:i:s')]);
            set_flash_message('success', 'Category created.');
        }
        redirect('categories.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('categories.php'); }
    DB::delete('categories', 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'Category deleted.'); redirect('categories.php');
}

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $category = [];
    if ($action === 'edit' && $id > 0) {
        $category = DB::selectOne("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) { set_flash_message('error', 'Category not found.'); redirect('categories.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' Category';
    $breadcrumb = ['Categories' => 'categories.php', $page_title => ''];
    include 'includes/header.php';

    // Common icons for selection
    $common_icons = [
        'fas fa-calendar-alt', 'fas fa-music', 'fas fa-theater-masks', 'fas fa-graduation-cap',
        'fas fa-store', 'fas fa-utensils', 'fas fa-hospital', 'fas fa-car', 'fas fa-home',
        'fas fa-laptop', 'fas fa-shopping-bag', 'fas fa-dumbbell', 'fas fa-paint-brush',
        'fas fa-briefcase', 'fas fa-hard-hat', 'fas fa-chart-line', 'fas fa-code',
        'fas fa-newspaper', 'fas fa-bolt', 'fas fa-globe', 'fas fa-users',
        'fas fa-blog', 'fas fa-pen', 'fas fa-camera', 'fas fa-video',
        'fas fa-heart', 'fas fa-star', 'fas fa-flag', 'fas fa-trophy',
        'fas fa-tags', 'fas fa-folder', 'fas fa-bookmark', 'fas fa-th-large'
    ];
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add New Category':'Edit Category'; ?></h1></div>
        <a href="categories.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_category" value="1">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3"><label class="form-label">Category Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required value="<?php echo clean_input($category['name']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><?php foreach($category_types as $val=>$label): ?><option value="<?php echo $val; ?>" <?php echo ($category['type']??'event')===$val?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3"><?php echo $category['description']??''; ?></textarea></div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <div class="input-group">
                            <span class="input-group-text" id="iconPreview"><i class="<?php echo clean_input($category['icon']??'fas fa-folder'); ?>"></i></span>
                            <input type="text" name="icon" class="form-control" id="iconInput" value="<?php echo clean_input($category['icon']??'fas fa-folder'); ?>" placeholder="fas fa-folder">
                        </div>
                        <div class="mt-2 d-flex flex-wrap gap-2" style="max-height:120px;overflow-y:auto;">
                            <?php foreach($common_icons as $icon): ?>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('iconInput').value='<?php echo $icon; ?>';document.querySelector('#iconPreview i').className='<?php echo $icon; ?>';" title="<?php echo $icon; ?>"><i class="<?php echo $icon; ?>"></i></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" class="form-control" min="0" value="<?php echo $category['sort_order']??0; ?>"></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($category['is_active'])||!empty($category['is_active']))?'checked':''; ?>><label class="form-check-label" for="is_active">Active</label></div></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Category</button><a href="categories.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <script>
        document.getElementById('iconInput').addEventListener('input', function() {
            document.querySelector('#iconPreview i').className = this.value;
        });
    </script>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_type = clean_input($_GET['type'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (name LIKE ?)"; $params[]="%$search%"; }
if ($filter_type) { $where .= " AND type = ?"; $params[]=$filter_type; }
if ($filter_status==='active') $where .= " AND is_active = 1";
elseif ($filter_status==='inactive') $where .= " AND is_active = 0";

$total = DB::count("categories", $where, $params);
$pagination = paginate($total, 20);
$categories = DB::select("SELECT * FROM categories WHERE {$where} ORDER BY type ASC, sort_order ASC, name ASC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}", $params);
?>

<div class="page-header">
    <div><h1 class="page-title">Categories</h1><p class="page-subtitle">Manage categories for all content types</p></div>
    <a href="categories.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Category</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="<?php echo clean_input($search); ?>">
    <select name="type" class="form-select filter-auto-submit"><option value="">All Types</option><?php foreach($category_types as $val=>$label): ?><option value="<?php echo $val; ?>" <?php echo $filter_type===$val?'selected':''; ?>><?php echo $label; ?></option><?php endforeach; ?></select>
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="active" <?php echo $filter_status==='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo $filter_status==='inactive'?'selected':''; ?>>Inactive</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="categories.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>Icon</th><th>Name</th><th>Slug</th><th>Type</th><th>Sort</th><th>Active</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($categories)): ?><tr><td colspan="7"><div class="empty-state"><i class="fas fa-tags"></i><h5>No Categories Found</h5></div></td></tr>
    <?php else:
        $current_type = '';
        foreach($categories as $cat):
            if ($current_type !== $cat['type']):
                $current_type = $cat['type'];
                $type_label = $category_types[$cat['type']] ?? ucfirst($cat['type']);
                ?>
                <tr><td colspan="7" class="bg-light fw-bold" style="padding:8px 16px;"><i class="fas fa-layer-group me-1"></i> <?php echo clean_input($type_label); ?></td></tr>
            <?php endif; ?>
    <tr>
        <td><i class="<?php echo clean_input($cat['icon']??'fas fa-folder'); ?> text-primary" style="font-size:18px;"></i></td>
        <td><strong><?php echo clean_input($cat['name']); ?></strong><?php if(!empty($cat['description'])): ?><br><small class="text-muted"><?php echo truncate_text(clean_input($cat['description']),50); ?></small><?php endif; ?></td>
        <td><code><?php echo clean_input($cat['slug']); ?></code></td>
        <td><span class="badge bg-secondary"><?php echo clean_input($category_types[$cat['type']]??ucfirst($cat['type'])); ?></span></td>
        <td><?php echo $cat['sort_order']; ?></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="categories.php" data-field="is_active" data-id="<?php echo $cat['id']; ?>" <?php echo $cat['is_active']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td>
            <a href="categories.php?action=edit&id=<?php echo $cat['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this category?')"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $cat['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'categories.php'); ?></div>

<?php include 'includes/footer.php'; ?>
