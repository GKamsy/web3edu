<!-- views/messages/announcements.php -->

<?php
    $role = $_SESSION['role'];
?>

<div class="notification-layout">

    <!-- =========== Sidebar or Left Panels ================= -->
    <aside class="notification-sidebar">
        <?php if ($role === "admin" || $role === "principal" || $role === "teacher"): ?>
            <a href="/announcements/create" class="compose-btn">➕ New Announcement</a>
        <?php endif; ?>

        <nav class="notification-filters">

            <a href="#" class="filter-link active" data-filter="unread">
                📬 Unread Announcements
            </a>
            <a href="#" class="filter-link" data-filter="read">
                                        🗂 Seen Announcements
            <a href="#" class="filter-link" data-filter="all">
                📢 All Announcements
            </a>
            </a>
        </nav>
    </aside>


    <!-- ================= Right Panel ================= -->
    <section id="notification-list" class="notification-list">

        <?php if (empty($announcements)): ?>
            <div class="empty">No more announcements available.</div>
        <?php else: ?>
            <div class="bulk-bar">
                <label>
                    <input type="checkbox" id="select-all">
                    Select All
                </label>
            </div>
            <?php foreach ($announcements as $n): ?>
                <div class="notification-item <?= $n['is_read'] ? 'read' : 'unread' ?>"
                     data-id="<?= $n['id'] ?>"
                     data-status="<?= $n['is_read'] ? 'read' : 'unread' ?>">

                    <!-- User may select items for special operation -->
                    <input type="checkbox" class="select-item" value="<?= $n['id'] ?>">
                    
                    <!-- Listing all required announcements -->
                    <div class="notification-header">
                        <span class="notification-title">
                            <?= htmlspecialchars($n['title']) ?>
                        </span>

                        <span class="notification-date">
                            <?= $n['created_at'] ?>
                        </span>
                    </div>

                    <div class="notification-message">
                        <?= htmlspecialchars($n['content']) ?>
                    </div>

                    <div class="notification-actions">
                        <?php if (isset($n['is_read']) && !$n['is_read']): ?>
                            <button class="mark-read-btn">Mark as read</button>
                        <?php endif; ?>
                        <button class="delete-btn">Delete</button>
                    </div>

                </div>
            <?php endforeach; ?>

            <!-- Bulk actions -->
            <div class="bulk-bar">
                <button id="bulk-read">Mark Selected as Read</button>
                <button id="bulk-delete">Delete Selected</button>
            </div>
            
            <!-- Pagging -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?filter=<?= $filter ?>&page=<?= $i ?>"
                       class="<?= $i == $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

            </div>
        <?php endif; ?>

    </section>
</div>
<script>
    document.addEventListener('click', function (e) {
    
        // FILTERING
        if (e.target.classList.contains('filter-link')) {
            e.preventDefault();
            document.querySelectorAll('.filter-link')
                .forEach(link => link.classList.remove('active'));
            e.target.classList.add('active');
            const filter = e.target.dataset.filter;

            document.querySelectorAll('.notification-item')
                .forEach(item => {
                    if (filter === 'all') {
                        item.style.display = 'block';
                    } else {
                        item.style.display =
                            item.dataset.status === filter
                            ? 'block'
                            : 'none';
                    }
                });
        }

        // MARK READ
        if (e.target.classList.contains('mark-read-btn')) {
            const item = e.target.closest('.notification-item');
            const id = item.dataset.id;
            fetch('/announcements/markAsRead/' + id, { method: 'POST' })
                .then(() => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    item.dataset.status = 'read';
                    e.target.remove();
                });
        }

        // DELETE
        if (e.target.classList.contains('delete-btn')) {
            const item = e.target.closest('.notification-item');
            const id = item.dataset.id;

            fetch('/announcements/delete/' + id, { method: 'POST' })
                .then(() => item.remove());
        }

        // SELECT ALL
        if (e.target.id === 'select-all') {
            document.querySelectorAll('.select-item')
                .forEach(cb => cb.checked = e.target.checked);
        }

        // BULK READ
        if (e.target.id === 'bulk-read') {
            const ids = [...document.querySelectorAll('.select-item:checked')]
                .map(cb => cb.value);
            if (!ids.length) return;

            fetch('/announcements/bulkRead', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids })
            })
            .then(() => location.reload());
        }

        // BULK DELETE
        if (e.target.id === 'bulk-delete') {
            const ids = [...document.querySelectorAll('.select-item:checked')]
                .map(cb => cb.value);
            if (!ids.length) return;
            fetch('/announcements/bulkDelete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids })
            })
            .then(() => location.reload());
        }
    });
</script>