<h1>⏰ My Tasks</h1>

<div class="tasks-container">

<?php foreach($tasks as $task): ?>

<div class="task-card">

```
<h3><?= htmlspecialchars($task['title']) ?></h3>

<p><?= htmlspecialchars($task['description']) ?></p>

<div class="task-footer">

    <span class="task-status <?= $task['status'] ?>">
        <?= ucfirst($task['status']) ?>
    </span>

    <button class="task-complete"
        data-id="<?= $task['id'] ?>">
        ✓
    </button>

</div>
```

</div>

<?php endforeach; ?>

</div>

<div class="task-add">

<input id="task-title" placeholder="New task title">
<button onclick="addTask()">Add</button>

</div>
