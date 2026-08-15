<?php
$pageTitle = 'Manage Digital Services';
require_once __DIR__ . '/includes/header.php';

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => clean($_POST['name']),
        'description' => clean($_POST['description']),
        'category' => clean($_POST['category'] ?? 'web'),
        'price' => !empty($_POST['price']) ? (float)$_POST['price'] : null,
        'icon' => clean($_POST['icon'] ?? 'laptop-code'),
        'sort_order' => (int)($_POST['sort_order'] ?? 0),
        'status' => $_POST['status'] ?? 'active',
    ];

    if (!empty($_FILES['image']['name'])) {
        $uploaded = uploadFile($_FILES['image'], 'services');
        if ($uploaded) $data['image'] = $uploaded;
    }

    if ($action === 'add') {
        $data['created_at'] = date('Y-m-d H:i:s');
        db()->insert('digital_services', $data);
        setFlash('success', 'Service added!');
    } else {
        db()->update('digital_services', $data, 'id = ?', [$id]);
        setFlash('success', 'Service updated!');
    }
    redirect('services.php');
}

if ($action === 'delete' && $id) {
    db()->delete('digital_services', 'id = ?', [$id]);
    setFlash('success', 'Service deleted!');
    redirect('services.php');
}

$editData = null;
if ($action === 'edit' && $id) {
    $editData = db()->fetch("SELECT * FROM digital_services WHERE id = ?", [$id]);
}

$services = db()->fetchAll("SELECT * FROM digital_services ORDER BY sort_order ASC, created_at DESC");
?>

<?php if ($action === 'add' || ($action === 'edit' && $editData)): ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><?php echo $action === 'add' ? 'Add New Service' : 'Edit Service'; ?></h6>
        <a href="services.php" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Service Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo $editData['name'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price (Rs.)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $editData['price'] ?? ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $editData['sort_order'] ?? 0; ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="web" <?php echo ($editData['category'] ?? '') === 'web' ? 'selected' : ''; ?>>Web Development</option>
                        <option value="design" <?php echo ($editData['category'] ?? '') === 'design' ? 'selected' : ''; ?>>Graphic Design</option>
                        <option value="video" <?php echo ($editData['category'] ?? '') === 'video' ? 'selected' : ''; ?>>Video Editing</option>
                        <option value="social" <?php echo ($editData['category'] ?? '') === 'social' ? 'selected' : ''; ?>>Social Media</option>
                        <option value="seo" <?php echo ($editData['category'] ?? '') === 'seo' ? 'selected' : ''; ?>>SEO</option>
                        <option value="app" <?php echo ($editData['category'] ?? '') === 'app' ? 'selected' : ''; ?>>App Development</option>
                        <option value="content" <?php echo ($editData['category'] ?? '') === 'content' ? 'selected' : ''; ?>>Content Writing</option>
                        <option value="other" <?php echo ($editData['category'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Icon (Font Awesome class)</label>
                    <input type="text" name="icon" class="form-control" placeholder="e.g. laptop-code" value="<?php echo $editData['icon'] ?? 'laptop-code'; ?>">
                    <small class="text-muted">fas fa- icon name from fontawesome.com</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo ($editData['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($editData['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?php echo $editData['description'] ?? ''; ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i><?php echo $action === 'add' ? 'Add Service' : 'Update Service'; ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">All Services (<?php echo count($services); ?>)</h6>
        <a href="?action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add New</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($services as $s): ?>
                    <tr>
                        <td><?php echo $s['id']; ?></td>
                        <td><i class="fas fa-<?php echo $s['icon'] ?? 'laptop-code'; ?> me-1"></i><?php echo clean($s['name']); ?></td>
                        <td><?php echo clean($s['category'] ?? 'web'); ?></td>
                        <td><?php echo $s['price'] ? 'Rs. ' . number_format($s['price']) : '-'; ?></td>
                        <td><?php echo $s['sort_order'] ?? 0; ?></td>
                        <td><span class="badge bg-<?php echo $s['status'] === 'active' ? 'success' : 'secondary'; ?>"><?php echo $s['status']; ?></span></td>
                        <td>
                            <a href="?action=edit&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="?action=delete&id=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this service?"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($services)): ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">No services. <a href="?action=add">Add one!</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
