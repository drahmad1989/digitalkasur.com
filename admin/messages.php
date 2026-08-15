<?php
/**
 * Admin Messages Management - DigitalKasur.com
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();
require_admin();

$page_title = 'Messages';
$breadcrumb = ['Messages' => 'messages.php'];
$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        set_flash_message('error', 'Invalid request.'); redirect('messages.php');
    }
    $msg_id = (int)$_POST['message_id'];
    $reply = clean_input($_POST['reply']);
    $msg = DB::selectOne("SELECT * FROM messages WHERE id = ?", [$msg_id]);

    if ($msg && !empty($reply)) {
        // Send email reply
        $subject = 'Re: ' . $msg['subject'];
        $email_body = "<p>Dear " . clean_input($msg['name']) . ",</p>";
        $email_body .= "<p>" . nl2br($reply) . "</p>";
        $email_body .= "<p>Best regards,<br>DigitalKasur Team<br>" . ADMIN_EMAIL . "</p>";

        if (send_email($msg['email'], $subject, $email_body)) {
            // Save reply to message
            $existing_replies = $msg['replies'] ?? '';
            $new_reply = date('Y-m-d H:i:s') . ' | ' . $_SESSION['user_name'] . ': ' . $reply;
            DB::update('messages', [
                'replies' => $existing_replies ? $existing_replies . "\n---\n" . $new_reply : $new_reply,
                'is_read' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$msg_id]);
            set_flash_message('success', 'Reply sent successfully.');
        } else {
            // Still save reply even if email fails
            $existing_replies = $msg['replies'] ?? '';
            $new_reply = date('Y-m-d H:i:s') . ' | ' . $_SESSION['user_name'] . ': ' . $reply;
            DB::update('messages', [
                'replies' => $existing_replies ? $existing_replies . "\n---\n" . $new_reply : $new_reply,
                'is_read' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$msg_id]);
            set_flash_message('warning', 'Reply saved but email could not be sent.');
        }
    } else {
        set_flash_message('error', 'Please enter a reply message.');
    }
    redirect('messages.php?action=view&id=' . $msg_id);
}

// Handle mark as read
if ($action === 'mark_read' && $id > 0) {
    DB::update('messages', ['is_read' => 1], 'id = ?', [$id]);
    redirect('messages.php?action=view&id=' . $id);
}

// Handle mark as unread
if ($action === 'mark_unread' && $id > 0) {
    DB::update('messages', ['is_read' => 0], 'id = ?', [$id]);
    set_flash_message('success', 'Message marked as unread.');
    redirect('messages.php');
}

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) { redirect('messages.php'); }
    DB::delete('messages', 'id = ?', [(int)$_POST['id']]);
    set_flash_message('success', 'Message deleted.'); redirect('messages.php');
}

// VIEW MESSAGE
if ($action === 'view' && $id > 0) {
    $message = DB::selectOne("SELECT * FROM messages WHERE id = ?", [$id]);
    if (!$message) { set_flash_message('error', 'Message not found.'); redirect('messages.php'); }

    // Mark as read
    if (!$message['is_read']) {
        DB::update('messages', ['is_read' => 1], 'id = ?', [$id]);
        $message['is_read'] = 1;
    }

    $page_title = 'View Message';
    $breadcrumb = ['Messages' => 'messages.php', 'View' => ''];
    include 'includes/header.php';
    ?>
    <div class="page-header">
        <div><h1 class="page-title">View Message</h1></div>
        <div class="d-flex gap-2">
            <a href="messages.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Back</a>
            <a href="messages.php?action=mark_unread&id=<?php echo $id; ?>" class="btn btn-outline-secondary"><i class="fas fa-envelope me-1"></i> Mark Unread</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h4 class="mb-1"><?php echo clean_input($message['subject'] ?? '(No Subject)'); ?></h4>
                    <div class="text-muted" style="font-size:14px;">
                        From: <strong><?php echo clean_input($message['name']); ?></strong>
                        &lt;<?php echo clean_input($message['email']); ?>&gt;
                        &bull; <?php echo format_date($message['created_at'], 'F j, Y \a\t g:i A'); ?>
                        <?php if(!empty($message['phone'])): ?>&bull; <i class="fas fa-phone"></i> <?php echo clean_input($message['phone']); ?><?php endif; ?>
                    </div>
                </div>
                <div>
                    <?php if($message['is_read']): ?>
                        <span class="badge bg-secondary"><i class="fas fa-envelope-open me-1"></i>Read</span>
                    <?php else: ?>
                        <span class="badge bg-primary"><i class="fas fa-envelope me-1"></i>Unread</span>
                    <?php endif; ?>
                </div>
            </div>
            <hr>
            <div style="font-size:15px;line-height:1.8;white-space:pre-wrap;"><?php echo clean_input($message['message']); ?></div>
        </div>
    </div>

    <!-- Previous Replies -->
    <?php if(!empty($message['replies'])): ?>
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-reply me-2"></i>Previous Replies</div>
        <div class="card-body">
            <?php
            $replies = explode("\n---\n", $message['replies']);
            foreach ($replies as $reply):
                $reply = clean_input($reply);
                ?>
                <div class="mb-3 p-3" style="background:var(--content-bg);border-radius:8px;border-left:3px solid var(--accent-blue);">
                    <div class="text-muted" style="font-size:12px;margin-bottom:4px;">
                        <?php
                        $parts = explode('|', $reply, 3);
                        echo trim($parts[0] ?? '') . ' - ' . trim($parts[1] ?? '');
                        ?>
                    </div>
                    <div style="white-space:pre-wrap;"><?php echo trim($parts[2] ?? $reply); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Reply Form -->
    <div class="card">
        <div class="card-header"><i class="fas fa-reply me-2"></i>Reply to Message</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="message_id" value="<?php echo $id; ?>">
                <div class="mb-3">
                    <label class="form-label">Reply</label>
                    <textarea name="reply" class="form-control" rows="6" required placeholder="Type your reply here..."></textarea>
                </div>
                <button type="submit" name="reply_message" class="btn btn-primary"><i class="fas fa-paper-plane me-1"></i> Send Reply</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; exit;
}

// LIST VIEW
include 'includes/header.php';

$search = clean_input($_GET['search'] ?? '');
$filter_status = clean_input($_GET['status'] ?? '');

$where = "1=1"; $params = [];
if ($search) { $where .= " AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)"; $params[]="%$search%"; $params[]="%$search%"; $params[]="%$search%"; $params[]="%$search%"; }
if ($filter_status === 'unread') $where .= " AND is_read = 0";
elseif ($filter_status === 'read') $where .= " AND is_read = 1";

$total = DB::count("messages", $where, $params);
$pagination = paginate($total, 15);
$messages = DB::select("SELECT * FROM messages WHERE {$where} ORDER BY is_read ASC, created_at DESC LIMIT {$pagination['items_per_page']} OFFSET {$pagination['offset']}", $params);

$unread_count = DB::count("messages", "is_read = 0");
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Messages</h1>
        <p class="page-subtitle"><?php echo $unread_count; ?> unread message<?php echo $unread_count !== 1 ? 's' : ''; ?></p>
    </div>
</div>

<form method="GET" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="Search messages..." value="<?php echo clean_input($search); ?>">
    <select name="status" class="form-select filter-auto-submit">
        <option value="">All Messages</option>
        <option value="unread" <?php echo $filter_status==='unread'?'selected':''; ?>>Unread</option>
        <option value="read" <?php echo $filter_status==='read'?'selected':''; ?>>Read</option>
    </select>
    <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
    <a href="messages.php" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
</form>

<div class="card"><div class="card-body p-0">
    <?php if(empty($messages)): ?>
        <div class="empty-state"><i class="fas fa-envelope-open"></i><h5>No Messages Found</h5><p>Messages from the contact form will appear here.</p></div>
    <?php else: ?>
        <?php foreach($messages as $msg): ?>
        <div class="message-item <?php echo !$msg['is_read']?'unread':''; ?>">
            <div class="d-flex justify-content-between align-items-start">
                <div style="flex:1;min-width:0;">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="message-sender"><?php echo clean_input($msg['name']); ?></span>
                        <?php if(!$msg['is_read']): ?><span class="badge bg-primary" style="font-size:10px;">NEW</span><?php endif; ?>
                        <?php if(!empty($msg['replies'])): ?><span class="badge bg-success" style="font-size:10px;"><i class="fas fa-reply"></i> Replied</span><?php endif; ?>
                    </div>
                    <div class="message-subject"><?php echo clean_input($msg['subject'] ?? '(No Subject)'); ?></div>
                    <div class="message-preview"><?php echo truncate_text(clean_input($msg['message']), 100); ?></div>
                    <small class="text-muted" style="font-size:12px;"><?php echo clean_input($msg['email']); ?><?php if(!empty($msg['phone'])): ?> &bull; <?php echo clean_input($msg['phone']); ?><?php endif; ?></small>
                </div>
                <div class="d-flex align-items-center gap-2 ms-3">
                    <span class="message-time" style="white-space:nowrap;"><?php echo time_ago($msg['created_at']); ?></span>
                    <a href="messages.php?action=view&id=<?php echo $msg['id']; ?>" class="action-btn action-btn-view" title="View"><i class="fas fa-eye"></i></a>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this message?')"><input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $msg['id']; ?>"><button type="submit" class="action-btn action-btn-delete" title="Delete"><i class="fas fa-trash"></i></button></form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div></div>
<div class="mt-3"><?php echo render_pagination($pagination['current_page'], $pagination['total_pages'], 'messages.php'); ?></div>

<?php include 'includes/footer.php'; ?>
