<div class="notification-layout">

    <!-- Sidebar -->
    <aside class="notification-sidebar">
        <a href="/messages/compose" class="compose-btn">➕ Compose</a>

        <nav class="notification-filters">
            <a href="?filter=inbox"
               class="<?= $filter === 'inbox' ? 'active' : '' ?>">
                📥 Inbox
            </a>

            <a href="?filter=sent"
               class="<?= $filter === 'sent' ? 'active' : '' ?>">
                📤 Sent
            </a>

            <a href="?filter=trash"
               class="<?= $filter === 'trash' ? 'active' : '' ?>">
                🗑 Trash
            </a>
        </nav>
    </aside>

    <!-- Right Panel -->
    <section class="notification-list">

        <div id="message-viewer" style="display:none;"></div>
        <div id="message-list-wrapper">
            <?php if (empty($messages)): ?>
                <div class="empty">No more messages available.</div>
            <?php else: ?>

                <div class="bulk-bar">
                    <label>
                        <input type="checkbox" id="select-all">
                        Select All
                    </label>
                </div>

                <?php foreach ($messages as $m): ?>
                    <div class="notification-item <?= $m['is_read'] ? 'read' : 'unread' ?>"
                         data-id="<?= $m['id'] ?>"
                         data-status="<?= $m['is_read'] ? 'read' : 'unread' ?>">

                        <input type="checkbox" class="select-item" value="<?= $m['id'] ?>">
                        <div class="notification-header">
                            <a href="#" class="notification-title message-link" data-id="<?= $m['id'] ?>">
                                <?= htmlspecialchars($m['subject']) ?>
                            </a>

                            <span class="notification-date">
                                <?= $m['created_at'] ?>
                            </span>

                        </div>

                        <div class="notification-message">
                            <?= htmlspecialchars(substr($m['body'], 0, 60)) ?>...
                        </div>

                        <div class="notification-actions">

                            <?php if (!$m['is_read'] && $filter === 'inbox'): ?>
                                <button class="mark-read-btn">
                                    Mark as read
                                </button>
                            <?php endif; ?>

                            <button class="delete-btn">Delete</button>

                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Bulk Actions -->
                <div class="bulk-bar">
                    <button id="bulk-delete">Delete Selected</button>
                </div>
            </div>

            <!-- Pagination -->
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

        // ===============================
        // OPEN MESSAGE VIA AJAX
        // ===============================
        if (e.target.classList.contains('message-link')) {

            e.preventDefault();

            const id = e.target.dataset.id;

            const viewer = document.getElementById('message-viewer');
            const listWrapper = document.getElementById('message-list-wrapper');

            fetch('/messages/thread/' + id)
                .then(res => res.json())
                .then(data => {

                    viewer.innerHTML = `
                        <button id="back-to-list" style="margin-bottom:15px;">
                            ← Back
                        </button>

                        <h2>${data.subject}</h2>

                        <div class="notification-date">
                            ${data.created_at}
                        </div>

                        <hr>

                        <div class="message-body">
                            ${data.body.replace(/\n/g, '<br>')}
                        </div>
                    `;

                    // Switch views
                    listWrapper.style.display = 'none';
                    viewer.style.display = 'block';

                    // Update read state visually
                    const item = document.querySelector(
                        '.notification-item[data-id="' + id + '"]'
                    );
                    if (item) {
                        item.classList.remove('unread');
                        item.classList.add('read');
                        item.dataset.status = 'read';
                    }
                });
        }

        // ===============================
        // BACK BUTTON
        // ===============================
        if (e.target.id === 'back-to-list') {
            document.getElementById('message-viewer').style.display = 'none';
            document.getElementById('message-list-wrapper').style.display = 'block';
        }

        // ===============================
        // SELECT ALL
        // ===============================
        if (e.target.id === 'select-all') {
            document.querySelectorAll('.select-item')
                .forEach(cb => cb.checked = e.target.checked);
        }

        // ===============================
        // MARK READ
        // ===============================
        if (e.target.classList.contains('mark-read-btn')) {

            const item = e.target.closest('.notification-item');
            const id = item.dataset.id;

            fetch('/messages/markAsRead/' + id, { method: 'POST' })
                .then(() => location.reload());
        }

        // ===============================
        // DELETE
        // ===============================
        if (e.target.classList.contains('delete-btn')) {

            const item = e.target.closest('.notification-item');
            const id = item.dataset.id;

            fetch('/messages/delete/' + id, { method: 'POST' })
                .then(() => location.reload());
        }

        // ===============================
        // BULK DELETE
        // ===============================
        if (e.target.id === 'bulk-delete') {

            const ids = [...document.querySelectorAll('.select-item:checked')]
                .map(cb => cb.value);

            if (!ids.length) return;

            fetch('/messages/bulkDelete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids })
            })
            .then(() => location.reload());
        }

    });
</script>

