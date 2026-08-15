<?php
/**
 * Admin Payments Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Payments';
$breadcrumb = ['Payments' => 'payments.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// VIEW PAYMENT DETAILS
if ($action === 'view' && $id > 0) {
    $payment = DB::selectOne(
        "SELECT p.*, u.name as user_name, u.email as user_email
         FROM payments p
         LEFT JOIN users u ON p.user_id = u.id
         WHERE p.id = ?",
        [$id]
    );
    if (!$payment) { set_flash_message('error', 'Payment not found.'); redirect('payments.php'); }

    $page_title = 'Payment Details';
    $breadcrumb = ['Payments' => 'payments.php', 'Details' => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title">Payment Details</h1></div>
        <a href="payments.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-receipt me-2"></i>Payment Information</div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr><th style="width:180px;">Transaction ID</th><td><code><?php echo clean_input($payment['transaction_id'] ?? '-'); ?></code></td></tr>
                        <tr><th>Amount</th><td><strong style="font-size:18px;color:var(--accent-green);"><?php echo format_price($payment['amount'] ?? 0); ?></strong></td></tr>
                        <tr><th>Currency</th><td><?php echo clean_input($payment['currency'] ?? 'PKR'); ?></td></tr>
                        <tr><th>Gateway</th><td>
                            <?php
                            $gw = $payment['gateway'] ?? '';
                            $gw_class = $gw === 'jazzcash' ? 'bg-danger' : ($gw === 'easypaisa' ? 'bg-success' : 'bg-secondary');
                            ?>
                            <span class="badge <?php echo $gw_class; ?>"><?php echo ucfirst($gw ?: 'Unknown'); ?></span>
                        </td></tr>
                        <tr><th>Status</th><td>
                            <?php
                            $st = $payment['status'] ?? '';
                            $st_class = $st === 'completed' ? 'badge-active' : ($st === 'pending' ? 'badge-pending' : ($st === 'failed' ? 'badge-inactive' : 'badge-pending'));
                            ?>
                            <span class="badge-status <?php echo $st_class; ?>"><?php echo ucfirst($st ?: 'Pending'); ?></span>
                        </td></tr>
                        <tr><th>Payment For</th><td><?php echo clean_input($payment['payment_type'] ?? '-'); ?> #<?php echo clean_input($payment['reference_id'] ?? '-'); ?></td></tr>
                        <tr><th>Created</th><td><?php echo format_date($payment['created_at'] ?? '', 'F j, Y \a\t g:i A'); ?></td></tr>
                        <?php if (!empty($payment['paid_at'])): ?>
                        <tr><th>Paid At</th><td><?php echo format_date($payment['paid_at'], 'F j, Y \a\t g:i A'); ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-user me-2"></i>User Information</div>
                <div class="card-body">
                    <?php if (!empty($payment['user_name'])): ?>
                        <p><strong><?php echo clean_input($payment['user_name']); ?></strong></p>
                        <p class="text-muted mb-0"><?php echo clean_input($payment['user_email']); ?></p>
                    <?php else: ?>
                        <p class="text-muted">Guest payment</p>
                        <p><?php echo clean_input($payment['customer_email'] ?? '-'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($payment['gateway_response'])): ?>
            <div class="card mt-3">
                <div class="card-header"><i class="fas fa-code me-2"></i>Gateway Response</div>
                <div class="card-body">
                    <pre style="font-size:11px;max-height:200px;overflow-y:auto;background:var(--content-bg);padding:10px;border-radius:6px;"><?php echo clean_input($payment['gateway_response']); ?></pre>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');
$filter_gateway = clean_input($_GET['gateway'] ?? '');
$filter_date_from = clean_input($_GET['date_from'] ?? '');
$filter_date_to = clean_input($_GET['date_to'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (p.transaction_id LIKE ? OR u.name LIKE ? OR u.email LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_status) { $where .= " AND p.status = ?"; $params[]=$filter_status; }
if ($filter_gateway) { $where .= " AND p.gateway = ?"; $params[]=$filter_gateway; }
if ($filter_date_from) { $where .= " AND p.created_at >= ?"; $params[]=$filter_date_from . ' 00:00:00'; }
if ($filter_date_to) { $where .= " AND p.created_at <= ?"; $params[]=$filter_date_to . ' 23:59:59'; }

$total = DB::count("payments p", $where, $params);
$pagination = paginate($total, 15);

$payments = DB::select(
    "SELECT p.*, u.name as user_name, u.email as user_email
     FROM payments p
     LEFT JOIN users u ON p.user_id = u.id
     WHERE {$where}
     ORDER BY p.created_at DESC
     LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}",
    $params
);

// Payment stats
$total_revenue = DB::sum("payments", "amount", "status = 'completed'");
$pending_amount = DB::sum("payments", "amount", "status = 'pending'");
$completed_count = DB::count("payments", "status = 'completed'");
$pending_count = DB::count("payments", "status = 'pending'");
?>

<div class="page-header">
    <div><h1 class="page-title">Payments</h1><p class="page-subtitle">Track and manage all payments</p></div>
</div>

<!-- Payment Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3><?php echo format_price($total_revenue); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
            <div class="stat-info">
                <h3><?php echo format_price($pending_amount); ?></h3>
                <p>Pending Amount</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon blue"><i class="fas fa-check"></i></div>
            <div class="stat-info">
                <h3><?php echo $completed_count; ?></h3>
                <p>Completed Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon red"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <h3><?php echo $pending_count; ?></h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search transaction/user..." value="<?php echo clean_input($search); ?>">
    <select name="status" class="form-select filter-auto-submit">
        <option value="">All Status</option>
        <option value="completed" <?php echo $filter_status==='completed'?'selected':''; ?>>Completed</option>
        <option value="pending" <?php echo $filter_status==='pending'?'selected':''; ?>>Pending</option>
        <option value="failed" <?php echo $filter_status==='failed'?'selected':''; ?>>Failed</option>
        <option value="refunded" <?php echo $filter_status==='refunded'?'selected':''; ?>>Refunded</option>
    </select>
    <select name="gateway" class="form-select filter-auto-submit">
        <option value="">All Gateways</option>
        <option value="jazzcash" <?php echo $filter_gateway==='jazzcash'?'selected':''; ?>>JazzCash</option>
        <option value="easypaisa" <?php echo $filter_gateway==='easypaisa'?'selected':''; ?>>EasyPaisa</option>
    </select>
    <input type="date" name="date_from" class="form-control" value="<?php echo clean_input($filter_date_from); ?>" placeholder="From">
    <input type="date" name="date_to" class="form-control" value="<?php echo clean_input($filter_date_to); ?>" placeholder="To">
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="payments.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<!-- Payments Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Transaction ID</th><th>User</th><th>Amount</th><th>Gateway</th><th>Status</th><th>For</th><th>Date</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($payments)): ?>
                    <tr><td colspan="9"><div class="empty-state"><i class="fas fa-credit-card"></i><h5>No Payments Found</h5></div></td></tr>
                <?php else: ?>
                    <?php foreach($payments as $i=>$pay): ?>
                    <tr>
                        <td><?php echo $pagination['offset']+$i+1; ?></td>
                        <td><code><?php echo clean_input($pay['transaction_id'] ?? '-'); ?></code></td>
                        <td>
                            <?php if(!empty($pay['user_name'])): ?>
                                <?php echo clean_input($pay['user_name']); ?><br><small class="text-muted"><?php echo clean_input($pay['user_email']); ?></small>
                            <?php else: ?>
                                <span class="text-muted">Guest</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo format_price($pay['amount'] ?? 0); ?></strong></td>
                        <td>
                            <?php $gw=$pay['gateway']??''; $gc=$gw==='jazzcash'?'bg-danger':($gw==='easypaisa'?'bg-success':'bg-secondary'); ?>
                            <span class="badge <?php echo $gc; ?>"><?php echo ucfirst($gw?:'N/A'); ?></span>
                        </td>
                        <td>
                            <?php
                            $st=$pay['status']??'pending';
                            $sc=$st==='completed'?'badge-active':($st==='pending'?'badge-pending':($st==='failed'?'badge-inactive':'badge-pending'));
                            ?>
                            <span class="badge-status <?php echo $sc; ?>"><?php echo ucfirst($st); ?></span>
                        </td>
                        <td><?php echo clean_input($pay['payment_type']??'-'); ?></td>
                        <td><?php echo format_date($pay['created_at']??'','M j, Y'); ?></td>
                        <td><a href="payments.php?action=view&id=<?php echo $pay['id']; ?>" class="action-btn action-btn-view" title="View"><i class="fas fa-eye"></i></a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'payments.php'); ?></div>

<?php include 'includes/footer.php'; ?>
