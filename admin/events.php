<?php
/**
 * Admin Events Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Events';
$breadcrumb = ['Events' => 'events.php'];
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
    $allowed_fields = ['is_active', 'is_featured'];
    if (!in_array($field, $allowed_fields)) {
        echo json_encode(['success' => false, 'message' => 'Invalid field']);
        exit;
    }
    $result = DB::update('events', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]);
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_event'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request. Please try again.');
        redirect('events.php');
    }

    $title = clean_input($_POST['title']);
    $slug = generate_slug($title);
    $description = $_POST['description'] ?? '';
    $venue = clean_input($_POST['venue'] ?? '');
    $event_date = clean_input($_POST['event_date'] ?? '');
    $event_time = clean_input($_POST['event_time'] ?? '');
    $city_id = (int)($_POST['city_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price = clean_input($_POST['price'] ?? '0');
    $contact_phone = clean_input($_POST['contact_phone'] ?? '');
    $contact_email = clean_input($_POST['contact_email'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $meta_title = clean_input($_POST['meta_title'] ?? '');
    $meta_description = clean_input($_POST['meta_description'] ?? '');

    if (empty($title)) {
        set_flash_message('error', 'Event title is required.');
    } else {
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = upload_image($_FILES['image']);
            if ($upload['success']) {
                $image = $upload['filename'];
            } else {
                set_flash_message('error', $upload['message']);
                redirect('events.php?action=' . ($id ? 'edit&id=' . $id : 'add'));
            }
        }

        if ($id > 0) {
            // Update
            $data = [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'venue' => $venue,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'city_id' => $city_id,
                'category_id' => $category_id,
                'price' => $price,
                'contact_phone' => $contact_phone,
                'contact_email' => $contact_email,
                'is_featured' => $is_featured,
                'is_active' => $is_active,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            if ($image) $data['image'] = $image;
            DB::update('events', $data, 'id = ?', [$id]);
            set_flash_message('success', 'Event updated successfully.');
        } else {
            // Insert
            $data = [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'venue' => $venue,
                'event_date' => $event_date,
                'event_time' => $event_time,
                'city_id' => $city_id,
                'category_id' => $category_id,
                'price' => $price,
                'contact_phone' => $contact_phone,
                'contact_email' => $contact_email,
                'is_featured' => $is_featured,
                'is_active' => $is_active,
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'user_id' => $_SESSION['user_id'],
                'created_at' => date('Y-m-d H:i:s'),
            ];
            if ($image) $data['image'] = $image;
            DB::insert('events', $data);
            set_flash_message('success', 'Event created successfully.');
        }
        redirect('events.php');
    }
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.');
        redirect('events.php');
    }
    $del_id = (int)$_POST['id'];
    // Soft delete
    DB::update('events', ['is_active' => 0, 'deleted_at' => date('Y-m-d H:i:s')], 'id = ?', [$del_id]);
    set_flash_message('success', 'Event deleted successfully.');
    redirect('events.php');
}

// Get data for form
$cities = DB::select("SELECT * FROM cities WHERE is_active = 1 ORDER BY name ASC");
$categories = DB::select("SELECT * FROM categories WHERE type = 'event' AND is_active = 1 ORDER BY name ASC");

if ($action === 'add' || $action === 'edit') {
    $event = [];
    if ($action === 'edit' && $id > 0) {
        $event = DB::selectOne("SELECT * FROM events WHERE id = ?", [$id]);
        if (!$event) {
            set_flash_message('error', 'Event not found.');
            redirect('events.php');
        }
    }

    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' Event';
    $breadcrumb = ['Events' => 'events.php', $page_title => ''];

    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div>
            <h1 class="page-title"><?php echo $action === 'add' ? 'Add New Event' : 'Edit Event'; ?></h1>
            <p class="page-subtitle">Fill in the event details below.</p>
        </div>
        <a href="events.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back to Events</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" id="eventForm">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="save_event" value="1">

                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Event Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required value="<?php echo clean_input($event['title'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6"><?php echo $event['description'] ?? ''; ?></textarea>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Venue</label>
                                <input type="text" name="venue" class="form-control" value="<?php echo clean_input($event['venue'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Price (PKR)</label>
                                <input type="text" name="price" class="form-control" value="<?php echo clean_input($event['price'] ?? '0'); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Event Date <span class="text-danger">*</span></label>
                                <input type="date" name="event_date" class="form-control" required value="<?php echo clean_input($event['event_date'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Event Time</label>
                                <input type="time" name="event_time" class="form-control" value="<?php echo clean_input($event['event_time'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo clean_input($event['contact_phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="<?php echo clean_input($event['contact_email'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Event Image</label>
                            <?php if (!empty($event['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($event['image']); ?>" class="img-thumbnail-sm" style="width:100%;height:auto;max-height:200px;object-fit:cover;" alt="Current image">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted">Max 5MB. JPEG, PNG, WebP.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <select name="city_id" class="form-select">
                                <option value="0">-- Select City --</option>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo $city['id']; ?>" <?php echo (isset($event['city_id']) && $event['city_id'] == $city['id']) ? 'selected' : ''; ?>>
                                        <?php echo clean_input($city['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category_id" class="form-select">
                                <option value="0">-- Select Category --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($event['category_id']) && $event['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo clean_input($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?php echo (!empty($event['is_featured'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_featured">Featured Event</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($event['is_active']) || !empty($event['is_active'])) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>

                        <hr>
                        <h6 class="mb-3"><i class="fas fa-search me-1"></i> SEO Settings</h6>
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="<?php echo clean_input($event['meta_title'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="3"><?php echo clean_input($event['meta_description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save Event</button>
                    <a href="events.php" class="btn btn-secondary ms-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
    exit;
}

// LIST VIEW
include 'includes/header.php';

// Filters
$search = clean_input($_GET['search'] ?? '');
$filter_city = (int)($_GET['city'] ?? 0);
$filter_category = (int)($_GET['category'] ?? 0);
$filter_status = clean_input($_GET['status'] ?? '');
$sort = clean_input($_GET['sort'] ?? 'created_at');
$order = clean_input($_GET['order'] ?? 'DESC');

// Build query
$where = "1=1";
$params = [];

if ($search) {
    $where .= " AND (e.title LIKE ? OR e.venue LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filter_city) {
    $where .= " AND e.city_id = ?";
    $params[] = $filter_city;
}
if ($filter_category) {
    $where .= " AND e.category_id = ?";
    $params[] = $filter_category;
}
if ($filter_status === 'active') {
    $where .= " AND e.is_active = 1";
} elseif ($filter_status === 'inactive') {
    $where .= " AND e.is_active = 0";
} elseif ($filter_status === 'featured') {
    $where .= " AND e.is_featured = 1";
}

$allowed_sorts = ['title', 'event_date', 'created_at', 'city_name'];
if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';
$allowed_orders = ['ASC', 'DESC'];
if (!in_array($order, $allowed_orders)) $order = 'DESC';

$total = DB::count("events e", $where, $params);
$pagination = paginate($total, 15);

$query = "SELECT e.*, c.name as city_name, cat.name as category_name
          FROM events e
          LEFT JOIN cities c ON e.city_id = c.id
          LEFT JOIN categories cat ON e.category_id = cat.id
          WHERE {$where}
          ORDER BY e.{$sort} {$order}
          LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}";

$events = DB::select($query, $params);
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Events</h1>
        <p class="page-subtitle">Manage all events across the platform</p>
    </div>
    <a href="events.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add Event</a>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search events..." value="<?php echo clean_input($search); ?>">
    <select name="city" class="form-select filter-auto-submit">
        <option value="">All Cities</option>
        <?php foreach ($cities as $city): ?>
            <option value="<?php echo $city['id']; ?>" <?php echo $filter_city == $city['id'] ? 'selected' : ''; ?>><?php echo clean_input($city['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="category" class="form-select filter-auto-submit">
        <option value="">All Categories</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat['id']; ?>" <?php echo $filter_category == $cat['id'] ? 'selected' : ''; ?>><?php echo clean_input($cat['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="status" class="form-select filter-auto-submit">
        <option value="">All Status</option>
        <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Active</option>
        <option value="inactive" <?php echo $filter_status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
        <option value="featured" <?php echo $filter_status === 'featured' ? 'selected' : ''; ?>>Featured</option>
    </select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="events.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<!-- Events Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th style="width:60px;">Image</th>
                        <th>
                            <a href="?sort=title&order=<?php echo $sort === 'title' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&city=<?php echo $filter_city; ?>&category=<?php echo $filter_category; ?>&status=<?php echo $filter_status; ?>" class="sort-link">
                                Title <?php if ($sort === 'title'): ?><i class="fas fa-sort-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i><?php endif; ?>
                            </a>
                        </th>
                        <th>City</th>
                        <th>Category</th>
                        <th>
                            <a href="?sort=event_date&order=<?php echo $sort === 'event_date' && $order === 'ASC' ? 'DESC' : 'ASC'; ?>&search=<?php echo urlencode($search); ?>&city=<?php echo $filter_city; ?>&category=<?php echo $filter_category; ?>&status=<?php echo $filter_status; ?>" class="sort-link">
                                Date <?php if ($sort === 'event_date'): ?><i class="fas fa-sort-<?php echo $order === 'ASC' ? 'up' : 'down'; ?>"></i><?php endif; ?>
                            </a>
                        </th>
                        <th>Featured</th>
                        <th>Active</th>
                        <th style="width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($events)): ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-alt"></i>
                                    <h5>No Events Found</h5>
                                    <p>Try adjusting your filters or add a new event.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($events as $i => $event): ?>
                            <tr>
                                <td><?php echo $pagination['offset'] + $i + 1; ?></td>
                                <td>
                                    <?php if (!empty($event['image'])): ?>
                                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($event['image']); ?>" class="img-thumbnail-sm" alt="">
                                    <?php else: ?>
                                        <div class="img-thumbnail-sm bg-light d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo clean_input($event['title']); ?></strong>
                                    <?php if (!empty($event['venue'])): ?>
                                        <br><small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i><?php echo clean_input($event['venue']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo clean_input($event['city_name'] ?? '-'); ?></td>
                                <td><?php echo clean_input($event['category_name'] ?? '-'); ?></td>
                                <td><?php echo format_date($event['event_date'], 'M j, Y'); ?></td>
                                <td>
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-ajax" data-url="events.php" data-field="is_featured" data-id="<?php echo $event['id']; ?>" <?php echo $event['is_featured'] ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </td>
                                <td>
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-ajax" data-url="events.php" data-field="is_active" data-id="<?php echo $event['id']; ?>" <?php echo $event['is_active'] ? 'checked' : ''; ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </td>
                                <td>
                                    <a href="events.php?action=edit&id=<?php echo $event['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                    <button onclick="confirmDelete('events.php')" class="action-btn action-btn-delete" title="Delete" data-id="<?php echo $event['id']; ?>"
                                        onclick="document.getElementById('deleteId').value=<?php echo $event['id']; ?>">
                                    </button>
                                    <form method="POST" style="display:inline;" id="delForm<?php echo $event['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                                        <button type="button" class="action-btn action-btn-delete" title="Delete" onclick="if(confirm('Delete this event?')) this.closest('form').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Pagination -->
<div class="mt-3">
    <?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'events.php'); ?>
</div>

<?php include 'includes/footer.php'; ?>
