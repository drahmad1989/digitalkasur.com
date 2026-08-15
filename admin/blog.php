<?php
/**
 * Admin Blog Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Blog';
$breadcrumb = ['Blog' => 'blog.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $field = clean_input($_POST['field']); $id = (int)$_POST['id']; $value = (int)$_POST['value'];
    if (!in_array($field, ['is_published', 'is_featured'])) { echo json_encode(['success' => false]); exit; }
    $result = DB::update('blog', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_blog'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('blog.php');
    }
    $title = clean_input($_POST['title']);
    $slug = generate_slug($title);
    $content = $_POST['content'] ?? '';
    $category = clean_input($_POST['category'] ?? '');
    $tags = clean_input($_POST['tags'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_published = isset($_POST['is_published']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    if (empty($title)) {
        set_flash_message('error', 'Blog title is required.');
    } else {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['image']);
            if ($upload['success']) $image = $upload['filename'];
        }

        if ($id > 0) {
            $data = [
                'title'=>$title,'slug'=>$slug,'content'=>$content,'category'=>$category,'tags'=>$tags,
                'is_featured'=>$is_featured,'is_published'=>$is_published,
                'meta_title'=>$meta_title,'meta_description'=>$meta_description,
                'updated_at'=>date('Y-m-d H:i:s')
            ];
            if ($image) $data['image'] = $image;
            if ($is_published && empty($blog_current['published_at'])) $data['published_at'] = date('Y-m-d H:i:s');
            DB::update('blog', $data, 'id = ?', [$id]);
            set_flash_message('success', 'Blog post updated.');
        } else {
            $data = [
                'title'=>$title,'slug'=>$slug,'content'=>$content,'category'=>$category,'tags'=>$tags,
                'is_featured'=>$is_featured,'is_published'=>$is_published,
                'author_id'=>$_SESSION['user_id'],
                'published_at'=>$is_published?date('Y-m-d H:i:s'):null,
                'meta_title'=>$meta_title,'meta_description'=>$meta_description,
                'created_at'=>date('Y-m-d H:i:s')
            ];
            if ($image) $data['image'] = $image;
            DB::insert('blog', $data);
            set_flash_message('success', 'Blog post created.');
        }
        redirect('blog.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('blog.php'); }
    DB::delete('blog', 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'Blog post deleted.'); redirect('blog.php');
}

$blog_categories = DB::select("SELECT DISTINCT category FROM blog WHERE category IS NOT NULL AND category != '' ORDER BY category ASC");
$authors = DB::select("SELECT id, name FROM users WHERE is_active = 1 ORDER BY name ASC");

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $post = [];
    if ($action === 'edit' && $id > 0) {
        $post = DB::selectOne("SELECT * FROM blog WHERE id = ?", [$id]);
        if (!$post) { set_flash_message('error', 'Post not found.'); redirect('blog.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' Blog Post';
    $breadcrumb = ['Blog' => 'blog.php', $page_title => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add New Post':'Edit Post'; ?></h1></div>
        <a href="blog.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_blog" value="1">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="mb-3"><label class="form-label">Title <span class="text-danger">*</span></label><input type="text" name="title" class="form-control" required value="<?php echo clean_input($post['title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="12"><?php echo $post['content']??''; ?></textarea></div>
                    <div class="mb-3"><label class="form-label">Tags</label><input type="text" name="tags" class="form-control" placeholder="tag1, tag2, tag3" value="<?php echo clean_input($post['tags']??''); ?>"></div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <?php if(!empty($post['image'])): ?><div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($post['image']); ?>" style="width:100%;max-height:180px;object-fit:cover;border-radius:8px;" alt=""></div><?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control" list="blogCats" value="<?php echo clean_input($post['category']??''); ?>">
                        <datalist id="blogCats"><?php foreach($blog_categories as $bc): ?><option value="<?php echo clean_input($bc['category']); ?>"><?php endforeach; ?></datalist>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Author</label>
                        <select name="author_id" class="form-select">
                            <?php foreach($authors as $author): ?><option value="<?php echo $author['id']; ?>" <?php echo ($post['author_id']??$_SESSION['user_id'])==$author['id']?'selected':''; ?>><?php echo clean_input($author['name']); ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo !empty($post['is_featured'])?'checked':''; ?>><label class="form-check-label" for="is_featured">Featured</label></div></div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_published" id="is_published" <?php echo (!isset($post['is_published'])||!empty($post['is_published']))?'checked':''; ?>><label class="form-check-label" for="is_published">Published</label></div></div>
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-search me-1"></i> SEO</h6>
                    <div class="mb-3"><label class="form-label">Meta Title</label><input type="text" name="meta_title" class="form-control" value="<?php echo clean_input($post['meta_title']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Meta Description</label><textarea name="meta_description" class="form-control" rows="3"><?php echo clean_input($post['meta_description']??''); ?></textarea></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Post</button><a href="blog.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_category = clean_input($_GET['category'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (b.title LIKE ? OR b.content LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_category) { $where .= " AND b.category = ?"; $params[]=$filter_category; }
if ($filter_status==='published') $where .= " AND b.is_published = 1";
elseif ($filter_status==='draft') $where .= " AND b.is_published = 0";
elseif ($filter_status==='featured') $where .= " AND b.is_featured = 1";

$total = DB::count("blog b", $where, $params);
$pagination = paginate($total, 15);
$posts = DB::select(
    "SELECT b.*, u.name as author_name FROM blog b LEFT JOIN users u ON b.author_id = u.id WHERE {$where} ORDER BY b.created_at DESC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>

<div class="page-header">
    <div><h1 class="page-title">Blog</h1><p class="page-subtitle">Manage blog posts</p></div>
    <a href="blog.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Post</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search posts..." value="<?php echo clean_input($search); ?>">
    <select name="category" class="form-select filter-auto-submit"><option value="">All Categories</option><?php foreach($blog_categories as $bc): ?><option value="<?php echo clean_input($bc['category']); ?>" <?php echo $filter_category===$bc['category']?'selected':''; ?>><?php echo clean_input($bc['category']); ?></option><?php endforeach; ?></select>
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="published" <?php echo $filter_status==='published'?'selected':''; ?>>Published</option><option value="draft" <?php echo $filter_status==='draft'?'selected':''; ?>>Draft</option><option value="featured" <?php echo $filter_status==='featured'?'selected':''; ?>>Featured</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="blog.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>Image</th><th>Title</th><th>Category</th><th>Author</th><th>Published</th><th>Featured</th><th>Date</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($posts)): ?><tr><td colspan="9"><div class="empty-state"><i class="fas fa-blog"></i><h5>No Blog Posts Found</h5></div></td></tr>
    <?php else: foreach($posts as $i=>$post): ?>
    <tr>
        <td><?php echo $pagination['offset']+$i+1; ?></td>
        <td><?php if(!empty($post['image'])): ?><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($post['image']); ?>" class="img-thumbnail-sm" alt=""><?php else: ?><div class="img-thumbnail-sm bg-light d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div><?php endif; ?></td>
        <td><strong><?php echo clean_input($post['title']); ?></strong></td>
        <td><?php echo clean_input($post['category']??'-'); ?></td>
        <td><?php echo clean_input($post['author_name']??'Admin'); ?></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="blog.php" data-field="is_published" data-id="<?php echo $post['id']; ?>" <?php echo $post['is_published']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="blog.php" data-field="is_featured" data-id="<?php echo $post['id']; ?>" <?php echo $post['is_featured']?'checked':''; ?>><span class="toggle-slider"></span></label></td>
        <td><?php echo format_date($post['created_at'],'M j, Y'); ?></td>
        <td>
            <a href="blog.php?action=edit&id=<?php echo $post['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="POST" style="display:inline;"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $post['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete" onclick="return confirm('Delete this post?')"><i class="fas fa-trash"></i></button></form>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'blog.php'); ?></div>

<?php include 'includes/footer.php'; ?>
