<?php
require_once __DIR__ . '/includes/auth.php';
admin_require_login();

$adminTitle = 'Messages';
$adminActive = 'messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        flash_set('error', 'Your session expired. Please try that again.');
        redirect('messages.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id = (int) ($_POST['id'] ?? 0);

    if ($id > 0 && $action === 'read') {
        db_run('UPDATE messages SET is_read = ? WHERE id = ?', [(int) ($_POST['next'] ?? 1), $id]);
        flash_set('success', 'Message updated.');
    } elseif ($id > 0 && $action === 'delete') {
        db_run('DELETE FROM messages WHERE id = ?', [$id]);
        flash_set('success', 'Message deleted.');
    }

    redirect('messages.php');
}

$messages = db_all('SELECT * FROM messages ORDER BY created_at DESC, id DESC');

require __DIR__ . '/includes/header.php';
?>

<div class="admin-intro">
    <p>Enquiries from the contact form. Mark them read once you have replied.</p>
</div>

<?php if (!$messages): ?>
    <div class="empty-state">No messages yet. Enquiries from the contact form will appear here.</div>
<?php else: ?>
    <div class="stack">
        <?php foreach ($messages as $message): ?>
            <?php $isNew = (int) $message['is_read'] === 0; ?>
            <article class="record <?= $isNew ? 'record--new' : '' ?>">
                <div class="record__head">
                    <div>
                        <div class="record__meta">
                            <span class="badge <?= $isNew ? 'badge--new' : 'badge--read' ?>">
                                <?= $isNew ? 'new' : 'read' ?>
                            </span>
                            <span><?= e(format_datetime($message['created_at'])) ?></span>
                        </div>
                        <h3 class="record__title"><?= e($message['subject']) ?></h3>
                        <p class="record__meta" style="margin:4px 0 0">
                            from <?= e($message['name']) ?>
                            <?php if ($message['phone'] !== ''): ?> &middot; <?= e($message['phone']) ?><?php endif; ?>
                        </p>
                    </div>
                    <a class="link-arrow" href="mailto:<?= e($message['email']) ?>">Reply to <?= e($message['email']) ?></a>
                </div>

                <p class="record__body"><?= e($message['message']) ?></p>

                <div class="record__actions">
                    <form method="post" action="messages.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="read">
                        <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                        <input type="hidden" name="next" value="<?= $isNew ? 0 : 1 ?>">
                        <button class="btn btn--sm btn--ink" type="submit">
                            Mark as <?= $isNew ? 'unread' : 'read' ?>
                        </button>
                    </form>
                    <form method="post" action="messages.php">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                        <button class="btn btn--sm btn--quiet btn--danger" type="submit">Delete</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
