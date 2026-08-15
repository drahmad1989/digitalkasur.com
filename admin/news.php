<?php
/**
 * Admin News Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'News';
$breadcrumb = ['News' => 'news.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $field = clean_input($_POST['field']); $id = (int)$_POST['id']; $value = (int)$_POST['value'];
    if (!in_array($field, ['is_active', 'is_featured', 'is_breaking'])) { echo json_encode(['success' => false]); exit; }
    $result = DB::update('news', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_news'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('news.php');
    }
    $title = clean_input($_POST['title']);
    $slug = generate_slug($title);
    $content = $_POST['content'] ?? '';
    $city_id = (int)($_POST['city_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $source = clean_input($_POST['source'] ?? '');
    $is_breaking = isset($_POST['is_breaking']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    if (empty($title)) {
        set_flash_message('error', 'News title is required.');
    } else {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['image']);
            if ($upload['success']) $image = $upload['filename'];
        }

        if ($id > 0) {
            $data = [
                'title'=>$title,'slug'=>$slug,'content'=>$content,'city_id'=>$city_id,'category_id'=>$category_id,
                'source'=>$source,'is_breaking'=>$is_breaking,'is_featured'=>$is_featured,'is_active'=>$is_active,
                'meta_title'=>$meta_title,'meta_description'=>$meta_description,'updated_at'=>date('Y-m-d H:i:s')
            ];
            if ($image) $data['image'] = $image;
            DB::update('news', $data, 'id = ?', [$id]);
            set_flash_message('success', 'News updated.');
        } else {
            $data = [
                'title'=>$title,'slug'=>$slug,'content'=>$content,'city_id'=>$city_id,'category_id'=>$category_id,
                'source'=>$source,'is_breaking'=>$is_breaking,'is_featured'=>$is_featured,'is_active'=>$is_active,
                'meta_title'=>$meta_title,'meta_description'=>$meta_description,
                'user_id'=>$_SESSION['user_id'],'views'=>0,'created_at'=>date('Y-m-d H:i:s')
            ];
            if ($image) $data['image'] = $image;
            DB::insert('news', $data);
            set_flash_message('success', 'News created.');
        }
        redirect('news.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('news.php'); }
    DB::update('news', ['is_active' => 0, 'deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'News deleted.'); redirect('news.php');
}

$cities = DB::select("SELECT * FROM cities WHERE is_active = 1 ORDER BY name ASC");
$categories = DB::select("SELECT * FROM categories WHERE type = 'news' AND is_active = 1 ORDER BY name ASC");

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $news = [];
    if ($action === 'edit' && $id > 0) {
        $news = DB::selectOne("SELECT * FROM news WHERE id = ?", [$id]);
        if (!$news) { set_flash_message('error', 'News not found.'); redirect('news.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' News';
    $breadcrumb = ['News' => 'news.php', $page_title => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add News':'Edit News'; ?></h1></div>
        <a href="news.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_news" value="1">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3"><label class="form-label">Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" required value="<?php echo clean_input($news['title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="10" id="newsContent"><?php echo $news['content']??''; ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Source</label><input type="text" name="source" class="form-control" value="<?php echo clean_input($news['source']??''); ?>"></div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <?php if(!empty($news['image'])): ?><div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($news['image']); ?>" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;" alt=""></div><?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3"><label class="form-label">City</label><select name="city_id" class="form-select"><option value="0">-- Select --</option><?php foreach($cities as $city): ?><option value="<?php echo $city['id']; ?>" <?php echo ($news['city_id']??0)==$city['id']?'selected':''; ?>><?php echo clean_input($city['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><label class="form-label">Category</label><select name="category_id" class="form-select"><option value="0">-- Select --</option><?php foreach($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php echo ($news['category_id']??0)==$cat['id']?'selected':''; ?>><?php echo clean_input($cat['name']); ?></option><?php endforeach; ?></select></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_breaking" id="is_breaking" <?php echo !empty($news['is_breaking'])?'checked':''; ?>><label class="form-check-label" for="is_breaking"><span class="badge bg-danger me-1"><i class="fas fa-bolt"></i></span>Breaking News</label></div></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo !empty($news['is_featured'])?'checked':''; ?>><label class="form-check-label" for="is_featured"><i class="fas fa-star text-warning me-1"></i>Featured</label></div></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($news['is_active'])||!empty($news['is_active']))?'checked':''; ?>><label class="form-check-label" for="is_active">Active</label></div></div>
                    <?php if(!empty($news['views'])): ?><div class="mb-3"><label class="form-label">Views</label><input type="text" class="form-control" value="<?php echo number_format($news['views']); ?>" disabled></div><?php endif; ?>
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-search me-1"></i> SEO</h6>
                    <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?php echo clean_input($news['meta_title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?php echo clean_input($news['meta_description']??''); ?></textarea></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save News</button><a href="news.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_city = (int)($_GET['city'] ?? 0);
$filter_category = (int)($_GET['category'] ?? 0);
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (n.title LIKE ? OR n.content LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_city) { $where .= " AND n.city_id = ?"; $params[]=$filter_city; }
if ($filter_category) { $where .= " AND n.category_id = ?"; $params[]=$filter_category; }
if ($filter_status==='active') $where .= " AND n.is_active = 1";
elseif ($filter_status==='inactive') $where .= " AND n.is_active = 0";
elseif ($filter_status==='breaking') $where .= " AND n.is_breaking = 1";
elseif ($filter_status==='featured') $where .= " AND n.is_featured = 1";

$total = DB::count("news n", $where, $params);
$pagination = paginate($total, 15);
$all_news = DB::select(
    "SELECT n.*, c.name as city_name, cat.name as category_name, u.name as author_name
     FROM news n LEFT JOIN cities c ON n.city_id = c.id LEFT JOIN categories cat ON n.category_id = cat.id LEFT JOIN users u ON n.user_id = u.id
     WHERE {$where} ORDER BY n.created_at DESC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>

<div class="page-header">
    <div><h1 class="page-title">News</h1><p class="page-subtitle">Manage all news articles</p></div>
    <a href="news.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add News</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search news..." value="<?php echo clean_input($search); ?>">
    <select name="city" class="form-select filter-auto-submit"><option value="">All Cities</option><?php foreach($cities as $city): ?><option value="<?php echo $city['id']; ?>" <?php echo $filter_city==$city['id']?'selected':''; ?>><?php echo clean_input($city['name']); ?></option><?php endforeach; ?></select>
    <select name="category" class="form-select filter-auto-submit"><option value="">All Categories</option><?php foreach($categories as $cat): ?><option value="<?php echo $cat['id']; ?>" <?php echo $filter_category==$cat['id']?'selected':''; ?>><?php echo clean_input($cat['name']); ?></option><?php endforeach; ?></select>
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="active" <?php echo $filter_status==='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo $filter_status==='inactive'?'selected':''; ?>>Inactive</option><option value="breaking" <?php echo $filter_status==='breaking'?'selected':''; ?>>Breaking</option><option value="featured" <?php echo $filter_status==='featured'?'selected':''; ?>>Featured</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="news.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>Image</th><th>Title</th><th>City</th><th>Category</th><th>Views</th><th>Breaking</th><th>Featured</th><th>Active</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($all_news)): ?><tr><td colspan="10"><div class="empty-state"><i class="fas fa-newspaper"></i><h5>No News Found</h5></div></td></tr>
    <?php else: foreach($all_news as $i=>$item): ?>
    <tr>
        <td><?php echo $pagination['offset']+$i+1; ?></td>
        <td><?php if(!empty($item['image'])): ?><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($item['image']); ?>" class="img-thumbnail-sm" alt=""><?php else: ?><div class="img-thumbnail-sm bg-light d-flex align-items-center justify-content-center"><i class="fas fa-newspaper text-muted"></i></div><?php endif; ?></td>
        <td><strong><?php echo clean_input($item['title']); ?></strong><br><small class="text-muted">By <?php echo clean_input($item['author_name']??'Admin'); ?> &bull; <?php echo time_ago($item['created_at']); ?></small></td>
        <td><?php echo clean_input($item['city_name']??'-'); ?></td>
        <td><?php echo clean_input($item['category_name']??'-'); ?></td>
        <td><span class="badge bg-secondary"><?php echo number_format($item['views']??0); ?></span></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="news.php" data-field="is_breaking" data-id="<?php echo $item['id']; ?>" <?php echo $item['is_breaking']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="news.php" data-field="is_featured" data-id="<?php echo $item['id']; ?>" <?php echo $item['is_featured']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="news.php" data-field="is_active" data-id="<?php echo $item['id']; ?>" <?php echo $item['is_active']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td>
            <a href="news.php?action=edit&id=<?php echo $item['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="POST" style="display:inline;"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $item['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete" onclick="return confirm('Delete this news?')"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'news.php'); ?></div>

<?php include 'includes/footer.php'; ?>
