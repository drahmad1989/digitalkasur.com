<?php
$pageTitle = 'Manage Pages';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'title' => clean($_POST['title']),
        'slug' => slugify($_POST['title']),
        'content' => clean($_POST['content'] ?? ''),
        'status' => $_POST['status'] ?? 'published',
    ];
    if ($action === 'add') {
        $data['created_at'] = date('Y-m-d H:i:s');
        db()->insert('pages', $data);
        setFlash('success', 'Page created!');
    } else {
        db()->update('pages', $data, 'id = ?', [$id]);
        setFlash('success', 'Page updated!');
    }
    redirect('pages.php');
}
if ($action === 'delete' && $id) { db()->delete('pages', 'id = ?', [$id]); setFlash('success', 'Page deleted!'); redirect('pages.php'); }
$editData = $action === 'edit' && $id ? db()->fetch("SELECT * FROM pages WHERE id = ?", [$id]) : null;
$pages = db()->fetchAll("SELECT * FROM pages ORDER BY title ASC");
?>

<?php if ($action === 'add' || ($action === 'edit' && $editData)): ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?php echo $action === 'add' ? 'Create Page' : 'Edit Page'; ?></h6>
        <a href="pages.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-8"><label class="form-label">Page Title *</label><input type="text" name="title" class="form-control" required value="<?php echo $editData['title'] ?? ''; ?>"></div>
                <div class="col-md-4"><label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="published" <?php echo ($editData['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>Published</option>
                        <option value="draft" <?php echo ($editData['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
                <div class="col-12"><label class="form-label">Content</label><textarea name="content" class="form-control" rows="10"><?php echo $editData['content'] ?? ''; ?></textarea></div>
                <div class="col-12"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo $action === 'add' ? 'Create Page' : 'Update'; ?></button></div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Pages (<?php echo count($pages); ?>)</h6>
        <a href="?action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add New</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>ID</th><th>Title</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($pages as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo clean($p['title']); ?></td>
                        <td><code><?php echo clean($p['slug']); ?></code></td>
                        <td><span class="badge bg-<?php echo $p['status'] === 'published' ? 'success' : 'warning'; ?>"><?php echo $p['status']; ?></span></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete?"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($pages)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No pages. <a href="?action=add">Create one!</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
