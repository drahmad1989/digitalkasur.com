<?php
/**
 * Admin Users Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Users';
$breadcrumb = ['Users' => 'users.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle AJAX toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle') {
    header('Content-Type: application/json');
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { echo json_encode(['success' => false]); exit; }
    $field = clean_input($_POST['field']); $id = (int)$_POST['id']; $value = (int)$_POST['value'];
    if (!in_array($field, ['is_active'])) { echo json_encode(['success' => false]); exit; }
    // Prevent deactivating yourself
    if ($id == $_SESSION['user_id']) { echo json_encode(['success' => false, 'message' => 'Cannot deactivate yourself']); exit; }
    $result = DB::update('users', [$field => $value], 'id = ?', [$id]);
    echo json_encode(['success' => $result !== false]); exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_user'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('users.php');
    }
    $name = clean_input($_POST['name']);
    $email = clean_input($_POST['email']);
    $phone = clean_input($_POST['phone'] ?? '');
    $role = clean_input($_POST['role'] ?? 'user');
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if (empty($name) || empty($email)) {
        set_flash_message('error', 'Name and email are required.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_flash_message('error', 'Invalid email address.');
    } else {
        // Check email uniqueness
        $existing = DB::selectOne("SELECT id FROM users WHERE email = ? AND id != ?", [$email, $id]);
        if ($existing) {
            set_flash_message('error', 'Email already in use.');
        } else {
            if ($id > 0) {
                $data = ['name'=>$name,'email'=>$email,'phone'=>$phone,'role'=>$role,'is_active'=>$is_active,'updated_at'=>date('Y-m-d H:i:s')];
                // Handle password change
                if (!empty($_POST['password'])) {
                    if (strlen($_POST['password']) < 6) {
                        set_flash_message('error', 'Password must be at least 6 characters.');
                        redirect('users.php?action=edit&id=' . $id);
                    }
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                // Handle avatar upload
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $upload = upload_image($_FILES['avatar']);
                    if ($upload['success']) $data['avatar'] = $upload['filename'];
                }
                DB::update('users', $data, 'id = ?', [$id]);
                set_flash_message('success', 'User updated.');
            } else {
                if (empty($_POST['password'])) {
                    set_flash_message('error', 'Password is required for new users.');
                    redirect('users.php?action=add');
                }
                if (strlen($_POST['password']) < 6) {
                    set_flash_message('error', 'Password must be at least 6 characters.');
                    redirect('users.php?action=add');
                }
                $data = [
                    'name'=>$name,'email'=>$email,'phone'=>$phone,'role'=>$role,'is_active'=>$is_active,
                    'password'=>password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
                    $upload = upload_image($_FILES['avatar']);
                    if ($upload['success']) $data['avatar'] = $upload['filename'];
                }
                DB::insert('users', $data);
                set_flash_message('success', 'User created.');
            }
            redirect('users.php');
        }
    }
}

// Handle reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('users.php'); }
    $uid = (int)$_POST['id'];
    $new_pass = bin2hex(random_bytes(6)); // Generate random 12-char password
    DB::update('users', ['password' => password_hash($new_pass, PASSWORD_DEFAULT)], 'id = ?', [$uid]);
    set_flash_message('success', 'Password reset. New password: ' . $new_pass);
    redirect('users.php');
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('users.php'); }
    $del_id = (int)$_POST['id'];
    if ($del_id == $_SESSION['user_id']) {
        set_flash_message('error', 'Cannot delete your own account.');
    } else {
        DB::delete('users', 'id = ?', [$del_id]);
        set_flash_message('success', 'User deleted.');
    }
    redirect('users.php');
}

// ADD/EDIT
if ($action === 'add' || $action === 'edit') {
    $user = [];
    if ($action === 'edit' && $id > 0) {
        $user = DB::selectOne("SELECT * FROM users WHERE id = ?", [$id]);
        if (!$user) { set_flash_message('error', 'User not found.'); redirect('users.php'); }
    }
    $page_title = ($action === 'add' ? 'Add' : 'Edit') . ' User';
    $breadcrumb = ['Users' => 'users.php', $page_title => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title"><?php echo $action==='add'?'Add New User':'Edit User'; ?></h1></div>
        <a href="users.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>
    <div class="card"><div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="save_user" value="1">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3"><label class="form-label">Full Name <span class="text-danger">*</span></label><input type="text" name="name" class="form-control" required value="<?php echo clean_input($user['name']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" required value="<?php echo clean_input($user['email']??''); ?>"></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="<?php echo clean_input($user['phone']??''); ?>"></div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label"><?php echo $id > 0 ? 'New Password (leave blank to keep current)' : 'Password <span class="text-danger">*</span>'; ?></label>
                        <input type="password" name="password" class="form-control" <?php echo $id > 0 ? '' : 'required'; ?> minlength="6">
                    </div>
                    <div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select"><option value="user" <?php echo ($user['role']??'user')==='user'?'selected':''; ?>>User</option><option value="business" <?php echo ($user['role']??'')==='business'?'selected':''; ?>>Business</option><option value="admin" <?php echo ($user['role']??'')==='admin'?'selected':''; ?>>Admin</option></select></div>
                    <div class="mb-3">
                        <label class="form-label">Avatar</label>
                        <?php if(!empty($user['avatar'])): ?><div class="mb-2"><img src="<?php echo SITE_URL; ?>/uploads/<?php echo clean_input($user['avatar']); ?>" class="img-thumbnail-sm" style="width:60px;height:60px;object-fit:cover;border-radius:50%;" alt=""></div><?php endif; ?>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?php echo (!isset($user['is_active'])||!empty($user['is_active']))?'checked':''; ?>><label class="form-check-label" for="is_active">Active</label></div></div>
                </div>
            </div>
            <div class="mt-4"><button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save User</button><a href="users.php" class="btn btn-secondary ms-2">Cancel</a></div>
        </form>
    </div></div>
    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_role = clean_input($_GET['role'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (u.name LIKE ? OR u.email LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_role) { $where .= " AND u.role = ?"; $params[]=$filter_role; }
if ($filter_status==='active') $where .= " AND u.is_active = 1";
elseif ($filter_status==='inactive') $where .= " AND u.is_active = 0";

$total = DB::count("users u", $where, $params);
$pagination = paginate($total, 15);
$users = DB::select(
    "SELECT u.* FROM users u WHERE {$where} ORDER BY u.created_at DESC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>

<div class="page-header">
    <div><h1 class="page-title">Users</h1><p class="page-subtitle">Manage all users</p></div>
    <a href="users.php?action=add" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Add User</a>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search users..." value="<?php echo clean_input($search); ?>">
    <select name="role" class="form-select filter-auto-submit"><option value="">All Roles</option><option value="admin" <?php echo $filter_role==='admin'?'selected':''; ?>>Admin</option><option value="business" <?php echo $filter_role==='business'?'selected':''; ?>>Business</option><option value="user" <?php echo $filter_role==='user'?'selected':''; ?>>User</option></select>
    <select name="status" class="form-select filter-auto-submit"><option value="">All Status</option><option value="active" <?php echo $filter_status==='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo $filter_status==='inactive'?'selected':''; ?>>Inactive</option></select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="users.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table table-hover mb-0"><thead><tr><th>#</th><th>Avatar</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Active</th><th>Last Login</th><th>Actions</th></tr></thead><tbody>
    <?php if(empty($users)): ?><tr><td colspan="9"><div class="empty-state"><i class="fas fa-users"></i><h5>No Users Found</h5></div></td></tr>
    <?php else: foreach($users as $i=>$u): ?>
    <tr>
        <td><?php echo $pagination['offset']+$i+1; ?></td>
        <td><div class="admin-avatar" style="width:36px;height:36px;font-size:14px;"><?php echo strtoupper(substr($u['name'],0,1)); ?></div></td>
        <td><strong><?php echo clean_input($u['name']); ?></strong></td>
        <td><?php echo clean_input($u['email']); ?></td>
        <td><?php echo clean_input($u['phone']??'-'); ?></td>
        <td><span class="badge <?php echo $u['role']==='admin'?'bg-danger':($u['role']==='business'?'bg-warning':'bg-info'); ?>"><?php echo ucfirst($u['role']); ?></span></td>
        <td><label class="toggle-switch"><input type="checkbox" class="toggle-ajax" data-url="users.php" data-field="is_active" data-id="<?php echo $u['id']; ?>" <?php echo $u['is_active']?'checked':''; ?> <?php echo $u['id']==$_SESSION['user_id']?'disabled':''; ?>><span class="toggle-slider"></span></label></td>
        <td><?php echo !empty($u['last_login'])?time_ago($u['last_login']):'<span class="text-muted">Never</span>'; ?></td>
        <td>
            <a href="users.php?action=edit&id=<?php echo $u['id']; ?>" class="action-btn action-btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Reset this user\\'s password?')"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="reset_password"><input type="hidden" name="id" value="<?php echo $u['id']; ?>"><button type="submit" class="action-btn action-btn-view" title="Reset Password"><i class="fas fa-key"></i></button></form>
            <?php if($u['id']!=$_SESSION['user_id']): ?>
            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?')"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $u['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete"><i class="fas fa-trash"></i></button></form>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'users.php'); ?></div>

<?php include 'includes/footer.php'; ?>
